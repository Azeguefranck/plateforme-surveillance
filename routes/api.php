<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

// ═══════════════════════════════════════════════════════════
//  SEUILS D'ALERTE (configurables via .env)
// ═══════════════════════════════════════════════════════════

define('SEUIL_TEMP_WARN', (float) env('ALERTE_TEMP_WARN', 30));
define('SEUIL_TEMP_CRIT', (float) env('ALERTE_TEMP_CRIT', 40));
define('SEUIL_HUM_MIN',   (float) env('ALERTE_HUM_MIN', 30));
define('SEUIL_HUM_MAX',   (float) env('ALERTE_HUM_MAX', 80));
define('SEUIL_GAZ_WARN',  (float) env('ALERTE_GAZ_WARN', 300));
define('SEUIL_GAZ_CRIT',  (float) env('ALERTE_GAZ_CRIT', 500));
define('SEUIL_CUR_WARN',  (float) env('ALERTE_CUR_WARN', 10));
define('SEUIL_CUR_CRIT',  (float) env('ALERTE_CUR_CRIT', 15));

// ═══════════════════════════════════════════════════════════
//  HELPER : analyse des seuils
// ═══════════════════════════════════════════════════════════

function analyserSeuils(array $data): array {
    $alertes = [];

    $temp = (float) ($data['temperature'] ?? 0);
    $hum  = (float) ($data['humidite']    ?? 0);
    $gaz  = (float) ($data['gaz']         ?? 0);
    $cur  = (float) ($data['courant']     ?? 0);
    $pwr  = (float) ($data['puissance']   ?? 0);
    $pir  = (int)   ($data['pir']         ?? 0);

    // Température
    if ($temp >= SEUIL_TEMP_CRIT) {
        $alertes[] = ['type'=>'temperature','valeur'=>"{$temp}°C",'niveau'=>'CRITIQUE',
            'risques'=>'surchauffe serveurs, incendie','solutions'=>'activer ventilation, couper alimentation','sms'=>true];
    } elseif ($temp >= SEUIL_TEMP_WARN) {
        $alertes[] = ['type'=>'temperature','valeur'=>"{$temp}°C",'niveau'=>'AVERTISSEMENT',
            'risques'=>'échauffement progressif','solutions'=>'vérifier climatisation','sms'=>false];
    }

    // Humidité
    if ($hum > SEUIL_HUM_MAX || $hum < SEUIL_HUM_MIN) {
        $niv = ($hum > 85 || $hum < 20) ? 'CRITIQUE' : 'AVERTISSEMENT';
        $alertes[] = ['type'=>'humidite','valeur'=>"{$hum}%",'niveau'=>$niv,
            'risques'=>($hum>SEUIL_HUM_MAX?'condensation, courts-circuits':'dessèchement composants'),
            'solutions'=>'ajuster humidificateur/déshumidificateur','sms'=>$niv==='CRITIQUE'];
    }

    // Gaz / Fumée
    if ($gaz >= SEUIL_GAZ_CRIT) {
        $alertes[] = ['type'=>'gaz','valeur'=>"{$gaz} ppm",'niveau'=>'CRITIQUE',
            'risques'=>'fuite gaz, incendie, intoxication','solutions'=>'évacuer salle, couper alimentation, contacter pompiers','sms'=>true];
    } elseif ($gaz >= SEUIL_GAZ_WARN) {
        $alertes[] = ['type'=>'gaz','valeur'=>"{$gaz} ppm",'niveau'=>'AVERTISSEMENT',
            'risques'=>'pollution air, surchauffe','solutions'=>'aérer la salle, vérifier équipements','sms'=>false];
    }

    // Courant
    if ($cur >= SEUIL_CUR_CRIT) {
        $alertes[] = ['type'=>'courant','valeur'=>"{$cur} A",'niveau'=>'CRITIQUE',
            'risques'=>'surcharge électrique, incendie','solutions'=>'déconnecter charges, vérifier disjoncteur','sms'=>true];
    } elseif ($cur >= SEUIL_CUR_WARN) {
        $alertes[] = ['type'=>'courant','valeur'=>"{$cur} A",'niveau'=>'AVERTISSEMENT',
            'risques'=>'charge élevée','solutions'=>'surveiller la consommation','sms'=>false];
    }

    // PIR
    if ($pir) {
        $alertes[] = ['type'=>'intrusion','valeur'=>'Mouvement détecté','niveau'=>'CRITIQUE',
            'risques'=>'intrusion non autorisée, vol, sabotage','solutions'=>'alerter sécurité, vérifier caméras, sécuriser salle','sms'=>true];
    }

    return $alertes;
}

// ═══════════════════════════════════════════════════════════
//  HELPER : construire message SMS
// ═══════════════════════════════════════════════════════════

function construireSMS(array $alertesCrit, array $data, string $salle): string {
    $now    = now()->format('d/m/Y H:i');
    $types  = array_column($alertesCrit, 'type');
    $risques = [];
    $solutions = [];
    foreach ($alertesCrit as $a) {
        $risques[]   = $a['risques'];
        $solutions[] = $a['solutions'];
    }

    $msg = "ALERTE CRITIQUE - {$salle}\n";
    $msg .= "Date: {$now}\n\n";

    if (in_array('temperature', $types))  $msg .= "Temp: {$data['temperature']}C\n";
    if (in_array('humidite',    $types))  $msg .= "Hum: {$data['humidite']}%\n";
    if (in_array('gaz',         $types))  $msg .= "Gaz: {$data['gaz']}ppm\n";
    if (in_array('courant',     $types))  $msg .= "Cur: {$data['courant']}A\n";
    if (in_array('intrusion',   $types))  $msg .= "Mouvement: OUI\n";

    $msg .= "\nRISQUES: " . implode(', ', array_unique($risques));
    $msg .= "\n\nSOLUTIONS: " . implode('; ', array_unique($solutions));

    return substr($msg, 0, 320);
}

// ═══════════════════════════════════════════════════════════
//  ENDPOINT ARDUINO (principal)
// ═══════════════════════════════════════════════════════════

Route::post('/arduino/data', function (Request $request) {

    // Vérification clé API
    $apiKey = $request->input('api_key') ?? $request->header('X-API-KEY');
    if ($apiKey !== env('ARDUINO_API_KEY')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $salleId = (int) ($request->input('salle_id') ?? 1);
    $salle   = DB::table('salles_serveurs')->find($salleId);
    $salleNom = $salle ? $salle->nom : 'Salle Serveur';

    $data = [
        'temperature' => (float) $request->input('temperature', 0),
        'humidite'    => (float) $request->input('humidite', 0),
        'gaz'         => (float) $request->input('gaz', 0),
        'courant'     => (float) $request->input('courant', 0),
        'tension'     => (float) $request->input('tension', 220),
        'puissance'   => (float) $request->input('puissance', 0),
        'pir'         => (int)   $request->input('pir', 0),
    ];

    // Calcul puissance si non fournie
    if ($data['puissance'] == 0 && $data['courant'] > 0) {
        $data['puissance'] = round($data['courant'] * $data['tension'], 2);
    }

    // Enregistrement mesure
    DB::table('mesures')->insert([
        'salle_id'    => $salleId,
        'temperature' => $data['temperature'],
        'humidite'    => $data['humidite'],
        'gaz'         => $data['gaz'],
        'courant'     => $data['courant'],
        'tension'     => $data['tension'],
        'puissance'   => $data['puissance'],
        'pir_detecte' => $data['pir'],
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    // Analyse des seuils
    $toutesAlertes = analyserSeuils($data);
    $alertesCrit   = array_filter($toutesAlertes, fn($a) => $a['sms']);
    $alertesCrit   = array_values($alertesCrit);
    $niveauGlobal  = 'NORMAL';
    if (count($alertesCrit))  $niveauGlobal = 'CRITIQUE';
    elseif (count($toutesAlertes)) $niveauGlobal = 'AVERTISSEMENT';

    // Sauvegarde des alertes
    foreach ($toutesAlertes as $a) {
        DB::table('alertes')->insert([
            'salle_id'    => $salleId,
            'type'        => $a['type'],
            'niveau'      => $a['niveau'],
            'valeur'      => $a['valeur'],
            'message'     => "RISQUES: {$a['risques']} | SOLUTIONS: {$a['solutions']}",
            'envoi_sms'   => $a['sms'] ? 1 : 0,
            'envoi_email' => count($alertesCrit) > 0 ? 1 : 0,
            'resolu'      => 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    // Log historique
    if (count($toutesAlertes)) {
        DB::table('historiques')->insert([
            'action'     => "ALERTE {$niveauGlobal} — Salle {$salleNom} — Capteurs: T={$data['temperature']}°C H={$data['humidite']}% G={$data['gaz']}ppm",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Emails aux utilisateurs validés
    $smsMessage = '';
    $numerosDestinataires = [env('ADMIN_SMS', '+237687988340')];

    if (count($alertesCrit) > 0) {
        $smsMessage = construireSMS($alertesCrit, $data, $salleNom);

        $users = DB::table('users')->where('validation_status', 'valide')->get();
        foreach ($users as $user) {
            // Numéros SMS si disponibles
            if (!empty($user->telephone) && str_starts_with($user->telephone, '+')) {
                $numerosDestinataires[] = $user->telephone;
            }

            // Email d'alerte
            try {
                $alertesHtml = '';
                foreach ($alertesCrit as $a) {
                    $color = $a['niveau'] === 'CRITIQUE' ? '#ef4444' : '#f59e0b';
                    $alertesHtml .= "<tr>
                        <td style='padding:8px;color:{$color};font-weight:bold;'>{$a['niveau']}</td>
                        <td style='padding:8px;color:white;'>" . ucfirst($a['type']) . "</td>
                        <td style='padding:8px;color:#39ff14;'>{$a['valeur']}</td>
                        <td style='padding:8px;color:#9ca3af;'>{$a['risques']}</td>
                    </tr>";
                }

                Mail::send([], [], function ($m) use ($user, $salleNom, $alertesHtml, $niveauGlobal, $data) {
                    $m->to($user->email)
                      ->subject("🚨 ALERTE {$niveauGlobal} — {$salleNom}")
                      ->html('<!DOCTYPE html><html><body style="background:#050816;font-family:Arial;padding:30px;">
<div style="max-width:600px;margin:auto;background:#101935;border-radius:16px;padding:35px;">
<h2 style="color:#ef4444;text-align:center;margin-bottom:5px;">🚨 ALERTE ' . $niveauGlobal . '</h2>
<p style="color:#9ca3af;text-align:center;margin-bottom:25px;">' . $salleNom . ' — ' . now()->format('d/m/Y H:i:s') . '</p>

<div style="background:#0b1225;border-radius:12px;padding:20px;margin-bottom:20px;">
<table style="width:100%;border-collapse:collapse;">
<tr style="border-bottom:1px solid #1f2d5e;">
  <th style="padding:8px;color:#9ca3af;text-align:left;">Niveau</th>
  <th style="padding:8px;color:#9ca3af;text-align:left;">Type</th>
  <th style="padding:8px;color:#9ca3af;text-align:left;">Valeur</th>
  <th style="padding:8px;color:#9ca3af;text-align:left;">Risques</th>
</tr>
' . $alertesHtml . '
</table>
</div>

<div style="background:#0b1225;border-radius:12px;padding:15px;margin-bottom:20px;">
<p style="color:#9ca3af;font-size:13px;margin-bottom:8px;">Mesures actuelles :</p>
<p style="color:white;">Température: <strong style="color:#ff5733;">' . $data['temperature'] . '°C</strong> &nbsp;
Humidité: <strong style="color:#33b5ff;">' . $data['humidite'] . '%</strong> &nbsp;
Gaz: <strong style="color:#ffd633;">' . $data['gaz'] . ' ppm</strong></p>
<p style="color:white;">Courant: <strong style="color:#33ff88;">' . $data['courant'] . ' A</strong> &nbsp;
Puissance: <strong style="color:#bb66ff;">' . $data['puissance'] . ' W</strong> &nbsp;
Tension: <strong>' . $data['tension'] . ' V</strong></p>
</div>

<p style="color:#9ca3af;text-align:center;font-size:12px;">Plateforme Surveillance IoT — Ne pas répondre à cet email</p>
</div></body></html>');
                });
            } catch (\Exception $e) {
                Log::warning("Email alerte non envoyé à {$user->email}: " . $e->getMessage());
            }
        }

        // Log SMS dans la base
        $numsUniq = array_unique($numerosDestinataires);
        foreach ($numsUniq as $num) {
            DB::table('sms_gsm')->insert([
                'numero'     => $num,
                'message'    => $smsMessage,
                'etat'       => 'EN_ATTENTE_ARDUINO',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // Mise à jour état salle
    $nouvelEtat = $niveauGlobal === 'CRITIQUE' ? 'ALERTE' : ($niveauGlobal === 'AVERTISSEMENT' ? 'ATTENTION' : 'ACTIVE');
    DB::table('salles_serveurs')->where('id', $salleId)->update(['etat' => $nouvelEtat]);

    return response()->json([
        'success'       => true,
        'timestamp'     => now()->format('Y-m-d H:i:s'),
        'alerte_active' => count($alertesCrit) > 0,
        'niveau'        => $niveauGlobal,
        'nb_alertes'    => count($toutesAlertes),
        'alertes'       => $alertesCrit,
        'envoyer_sms'   => count($alertesCrit) > 0,
        'sms_message'   => $smsMessage,
        'numeros_sms'   => array_values(array_unique($numerosDestinataires)),
    ]);
});

// ═══════════════════════════════════════════════════════════
//  ENDPOINT LEGACY (compatibilité ancienne API)
// ═══════════════════════════════════════════════════════════

Route::post('/capteurs', function (Request $request) {
    DB::table('mesures')->insert([
        'temperature' => $request->temperature,
        'humidite'    => $request->humidite,
        'gaz'         => $request->gaz,
        'courant'     => $request->courant,
        'puissance'   => $request->puissance,
        'tension'     => $request->tension ?? 220,
        'pir_detecte' => $request->pir ?? 0,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    $alertesDetect = analyserSeuils([
        'temperature' => $request->temperature,
        'humidite'    => $request->humidite,
        'gaz'         => $request->gaz,
        'courant'     => $request->courant,
        'puissance'   => $request->puissance,
        'pir'         => $request->pir ?? 0,
    ]);

    foreach ($alertesDetect as $a) {
        DB::table('alertes')->insert([
            'type'       => $a['type'],
            'niveau'     => $a['niveau'],
            'valeur'     => $a['valeur'],
            'message'    => $a['risques'],
            'envoi_sms'  => $a['sms'] ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return response()->json(['success' => true]);
});

// ═══════════════════════════════════════════════════════════
//  DASHBOARD DATA
// ═══════════════════════════════════════════════════════════

Route::get('/dashboard-data', function () {
    $last = DB::table('mesures')->latest('id')->first();

    $defaultData = [
        'temperature' => 0, 'humidite' => 0, 'gaz' => 0,
        'courant' => 0, 'puissance' => 0, 'tension' => 220,
        'pir_detecte' => 0, 'salle_id' => 1,
    ];

    $data = $last ? (array) $last : $defaultData;

    // Arduino considéré connecté si une mesure est arrivée dans les 30 dernières secondes
    $arduinoConnecte = $last && \Carbon\Carbon::parse($last->created_at)->diffInSeconds(now()) < 30;

    // Alertes non résolues
    $alertesActives = DB::table('alertes')
        ->where('resolu', 0)
        ->where('created_at', '>=', now()->subMinutes(30))
        ->count();

    // Dernière alerte
    $derniereAlerte = DB::table('alertes')->latest('id')->first();

    // État salle
    $salle = DB::table('salles_serveurs')->find($data['salle_id'] ?? 1);

    $data['alertes_actives']  = $alertesActives;
    $data['derniere_alerte']  = $derniereAlerte;
    $data['etat_salle']       = $salle ? $salle->etat : 'ACTIVE';
    $data['nom_salle']        = $salle ? $salle->nom : 'Salle Serveur';
    $data['timestamp']        = now()->format('Y-m-d H:i:s');

    // Niveaux alertes actuelles
    $alertesCourantes = analyserSeuils([
        'temperature' => $data['temperature'],
        'humidite'    => $data['humidite'],
        'gaz'         => $data['gaz'],
        'courant'     => $data['courant'],
        'puissance'   => $data['puissance'],
        'pir'         => $data['pir_detecte'],
    ]);
    $niveauGlobal = 'NORMAL';
    foreach ($alertesCourantes as $a) {
        if ($a['niveau'] === 'CRITIQUE')       { $niveauGlobal = 'CRITIQUE'; break; }
        if ($a['niveau'] === 'AVERTISSEMENT')  { $niveauGlobal = 'AVERTISSEMENT'; }
    }
    $data['niveau_global']      = $niveauGlobal;
    $data['alertes_courantes']  = $alertesCourantes;
    $data['arduino_connecte']   = $arduinoConnecte;

    return response()->json($data);
});

// ═══════════════════════════════════════════════════════════
//  HISTORIQUE CAPTEURS (pagination)
// ═══════════════════════════════════════════════════════════

Route::get('/historique', function (Request $request) {
    $limit = min((int) ($request->input('limit', 50)), 500);
    $page  = (int) ($request->input('page', 1));
    $debut = $request->input('debut');
    $fin   = $request->input('fin');

    $q = DB::table('mesures');
    if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
    if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');

    $total = $q->count();
    $data  = (clone $q)
        ->latest('id')
        ->skip(($page - 1) * $limit)
        ->take($limit)
        ->get();

    return response()->json([
        'data'       => $data,
        'total'      => $total,
        'page'       => $page,
        'per_page'   => $limit,
        'last_page'  => (int) ceil($total / max($limit,1)),
    ]);
});

// ═══════════════════════════════════════════════════════════
//  ALERTES RÉCENTES
// ═══════════════════════════════════════════════════════════

Route::get('/alertes/recent', function (Request $request) {
    $limit = (int) ($request->input('limit', 20));

    $alertes = DB::table('alertes')
        ->latest('id')
        ->take($limit)
        ->get();

    $stats = [
        'total'     => DB::table('alertes')->count(),
        'critiques' => DB::table('alertes')->where('niveau', 'CRITIQUE')->count(),
        'non_resolu'=> DB::table('alertes')->where('resolu', 0)->count(),
        'aujourd_hui'=> DB::table('alertes')->whereDate('created_at', today())->count(),
    ];

    return response()->json(['alertes' => $alertes, 'stats' => $stats]);
});

// ═══════════════════════════════════════════════════════════
//  RÉSOUDRE UNE ALERTE
// ═══════════════════════════════════════════════════════════

Route::post('/alertes/{id}/resoudre', function ($id) {
    DB::table('alertes')->where('id', $id)->update(['resolu' => 1, 'updated_at' => now()]);
    return response()->json(['success' => true]);
});

// ═══════════════════════════════════════════════════════════
//  STATS POUR LES GRAPHIQUES
// ═══════════════════════════════════════════════════════════

Route::get('/stats/graphiques', function (Request $request) {
    $heures = (int) ($request->input('heures', 1));

    $mesures = DB::table('mesures')
        ->where('created_at', '>=', now()->subHours($heures))
        ->orderBy('id')
        ->get(['created_at','temperature','humidite','gaz','courant','puissance']);

    return response()->json($mesures);
});

// ═══════════════════════════════════════════════════════════
//  SMS LOG
// ═══════════════════════════════════════════════════════════

Route::get('/sms/log', function (Request $request) {
    $sms = DB::table('sms_gsm')
        ->latest('id')
        ->take(100)
        ->get()
        ->map(function ($s) {
            return [
                'id'          => $s->id,
                'destinataire'=> $s->destinataire ?? ($s->numero ?? '—'),
                'numero'      => $s->numero ?? '—',
                'message'     => $s->message ?? '',
                'etat'        => $s->etat ?? '',
                'statut'      => isset($s->statut) ? $s->statut : (
                    (strtoupper($s->etat ?? '') === 'ENVOYÉ' || strtoupper($s->etat ?? '') === 'ENVOYE') ? 'envoye' :
                    (strtoupper($s->etat ?? '') === 'ECHEC'  ? 'echec'  : 'en_attente')
                ),
                'type'        => $s->type ?? 'manuel',
                'created_at'  => $s->created_at ?? null,
            ];
        });
    return response()->json($sms);
});

// ═══════════════════════════════════════════════════════════
//  MARQUER SMS COMME ENVOYÉ (Arduino confirm)
// ═══════════════════════════════════════════════════════════

Route::post('/sms/confirmer', function (Request $request) {
    $ids = $request->input('ids', []);
    DB::table('sms_gsm')->whereIn('id', $ids)->update(['etat' => 'ENVOYÉ', 'updated_at' => now()]);
    return response()->json(['success' => true]);
});

// ═══════════════════════════════════════════════════════════
//  ENVOI SMS MANUEL (depuis l'interface)
// ═══════════════════════════════════════════════════════════

Route::post('/sms/send', function (Request $request) {
    $numero  = trim($request->input('numero', ''));
    $message = trim($request->input('message', ''));

    if (!$numero || !$message) {
        return response()->json(['success' => false, 'message' => 'Numéro et message requis.'], 422);
    }
    if (strlen($message) > 160) {
        return response()->json(['success' => false, 'message' => 'Message trop long (max 160 caractères).'], 422);
    }

    $id = DB::table('sms_gsm')->insertGetId([
        'numero'     => $numero,
        'message'    => $message,
        'etat'       => 'EN_ATTENTE',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'SMS enregistré. Il sera envoyé par le module Arduino.',
        'id'      => $id,
    ]);
});

// ═══════════════════════════════════════════════════════════
//  STATUT GSM (depuis la base de données)
// ═══════════════════════════════════════════════════════════

Route::get('/gsm/status', function () {
    $dernier = DB::table('sms_gsm')->latest('id')->first();
    $total   = DB::table('sms_gsm')->count();
    $envoyes = DB::table('sms_gsm')->where('etat', 'ENVOYÉ')->count();

    return response()->json([
        'statut'    => 'connecte',
        'modele'    => 'SIM900',
        'signal'    => '-65',
        'operateur' => 'MTN/Orange',
        'total_sms' => $total,
        'envoyes'   => $envoyes,
        'dernier'   => $dernier ? $dernier->created_at : null,
    ]);
});

// ═══════════════════════════════════════════════════════════
//  STATISTIQUES AGRÉGÉES
// ═══════════════════════════════════════════════════════════

Route::get('/stats/resume', function () {
    try {
        $mesures   = DB::table('mesures');
        $last24h   = DB::table('mesures')->where('created_at', '>=', now()->subHours(24));
        $alertes   = DB::table('alertes');

        $avg = $mesures->selectRaw('AVG(temperature) as t, AVG(humidite) as h, AVG(gaz) as g, AVG(courant) as c, AVG(puissance) as p')->first();
        $mx  = $mesures->selectRaw('MAX(temperature) as t, MAX(humidite) as h, MAX(gaz) as g, MAX(courant) as c, MAX(puissance) as p')->first();

        return response()->json([
            'total_mesures'   => DB::table('mesures')->count(),
            'total_alertes'   => DB::table('alertes')->count(),
            'alertes_crit'    => DB::table('alertes')->where('niveau','CRITIQUE')->count(),
            'alertes_today'   => DB::table('alertes')->whereDate('created_at',today())->count(),
            'mesures_today'   => DB::table('mesures')->whereDate('created_at',today())->count(),
            'avg_temp'        => round($avg->t ?? 0, 1),
            'avg_hum'         => round($avg->h ?? 0, 1),
            'avg_gaz'         => round($avg->g ?? 0),
            'avg_courant'     => round($avg->c ?? 0, 2),
            'avg_puissance'   => round($avg->p ?? 0),
            'max_temp'        => round($mx->t ?? 0, 1),
            'max_gaz'         => round($mx->g ?? 0),
            'total_serveurs'  => DB::table('serveurs')->count(),
            'serveurs_actifs' => DB::table('serveurs')->where('statut','actif')->count(),
            'total_salles'    => DB::table('salles')->count(),
            'total_sms'       => DB::table('sms_gsm')->count(),
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// ═══════════════════════════════════════════════════════════
//  ANOMALIES (alertes critiques non résolues)
// ═══════════════════════════════════════════════════════════

Route::get('/anomalies', function (Request $request) {
    $limit = min((int) $request->input('limit', 50), 200);
    $page  = max(1, (int) $request->input('page', 1));
    $niv   = $request->input('niveau', '');
    $debut = $request->input('debut', '');
    $fin   = $request->input('fin', '');

    $q = DB::table('alertes');
    if ($niv)   $q->where('niveau', strtoupper($niv));
    if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
    if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');

    $total = $q->count();
    $data  = (clone $q)->latest('id')->skip(($page-1)*$limit)->take($limit)->get();

    return response()->json([
        'data'      => $data,
        'total'     => $total,
        'page'      => $page,
        'last_page' => (int) ceil($total / max($limit,1)),
        'stats'     => [
            'total'     => DB::table('alertes')->count(),
            'critiques' => DB::table('alertes')->where('niveau','CRITIQUE')->count(),
            'non_resolu'=> DB::table('alertes')->where('resolu',0)->count(),
            'today'     => DB::table('alertes')->whereDate('created_at',today())->count(),
        ],
    ]);
});

// ═══════════════════════════════════════════════════════════
//  DONNÉES RAPPORT (prévisualisation unifiée par type)
// ═══════════════════════════════════════════════════════════

Route::get('/report-data', function (Request $request) {
    $type   = $request->input('type', 'capteurs');
    $limit  = min((int) $request->input('limit', 50), 500);
    $page   = max(1, (int) $request->input('page', 1));
    $debut  = $request->input('debut');
    $fin    = $request->input('fin');
    $niveau = $request->input('niveau');
    $salle  = (int) $request->input('salle', 0);

    try {
        if ($type === 'utilisateurs') {
            $q = DB::table('users')
                ->select('id','nom','prenom','email','role','validation_status','telephone','organisation','created_at');
        } elseif ($type === 'salles') {
            $q = DB::table('salles');
        } elseif ($type === 'serveurs') {
            $q = DB::table('serveurs');
        } elseif ($type === 'incidents') {
            $q = DB::table('alertes')->where('niveau', 'CRITIQUE');
            if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
            if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');
        } elseif ($type === 'securite') {
            $q = DB::table('alertes')->where('type', 'intrusion');
            if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
            if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');
        } elseif (in_array($type, ['alertes', 'anomalies'])) {
            $q = DB::table('alertes');
            if ($debut)  $q->where('created_at', '>=', $debut . ' 00:00:00');
            if ($fin)    $q->where('created_at', '<=', $fin   . ' 23:59:59');
            if ($niveau) $q->where('niveau', strtoupper($niveau));
        } elseif ($type === 'energie') {
            $q = DB::table('mesures')->select('id','created_at','courant','puissance','tension','temperature','humidite');
            if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
            if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');
            if ($salle) $q->where('salle_id', $salle);
        } elseif ($type === 'maintenance') {
            $q = DB::table('historiques');
            if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
            if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');
        } else {
            // capteurs, historique, default
            $q = DB::table('mesures');
            if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
            if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');
            if ($salle) $q->where('salle_id', $salle);
        }

        $total = $q->count();
        $data  = (clone $q)->latest('id')->skip(($page - 1) * $limit)->take($limit)->get();

        return response()->json([
            'data'      => $data,
            'total'     => $total,
            'page'      => $page,
            'last_page' => (int) ceil($total / max($limit, 1)),
        ]);
    } catch (\Exception $e) {
        return response()->json(['data' => [], 'total' => 0]);
    }
});
