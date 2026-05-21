<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\GeoController;

// ── Géographie ────────────────────────────────────────────
Route::get('/geo/states/{country}',                    [GeoController::class, 'states']);
Route::get('/geo/cities/{country}',                    [GeoController::class, 'cities']);
Route::get('/geo/state-cities/{country}/{state}',      [GeoController::class, 'stateCities']);
Route::get('/geo/subcities/{country}/{city}',          [GeoController::class, 'subcities']);


// ── Méta-données capteurs (texte fixe) ────────────────────
function getSeuilsMeta(): array
{
    return [
        'temperature' => [
            'unite'    => '°C',
            'risque'   => 'Surchauffe serveurs, défaillance matérielle',
            'solution' => 'Vérifier climatisation, ventilation, redémarrer les systèmes de refroidissement',
        ],
        'humidite' => [
            'unite'    => '%',
            'risque'   => 'Condensation, court-circuit, corrosion des équipements',
            'solution' => 'Activer le déshumidificateur, vérifier l\'étanchéité de la salle',
        ],
        'gaz' => [
            'unite'    => 'ppm',
            'risque'   => 'Fuite de gaz dangereux, risque d\'incendie ou d\'explosion',
            'solution' => 'Évacuer la salle, couper l\'alimentation, appeler les secours',
        ],
        'courant' => [
            'unite'    => 'A',
            'risque'   => 'Surcharge électrique, risque de court-circuit',
            'solution' => 'Réduire la charge, vérifier les disjoncteurs',
        ],
        'puissance' => [
            'unite'    => 'W',
            'risque'   => 'Consommation excessive, risque de coupure générale',
            'solution' => 'Éteindre les équipements non critiques, vérifier l\'alimentation',
        ],
    ];
}

// ── Seuils dynamiques depuis storage/app/seuils.json ──────
function getSeuilsValeurs(): array
{
    static $cache = null;
    if ($cache !== null) return $cache;

    $path = storage_path('app/seuils.json');
    if (file_exists($path)) {
        $data = json_decode(file_get_contents($path), true);
        if (is_array($data)) {
            $cache = $data;
            return $cache;
        }
    }

    $cache = [
        'temperature' => ['warning' => 35,   'critique' => 40],
        'humidite'    => ['warning' => 75,   'critique' => 85],
        'gaz'         => ['warning' => 300,  'critique' => 500],
        'courant'     => ['warning' => 10,   'critique' => 15],
        'puissance'   => ['warning' => 3000, 'critique' => 5000],
        'pir'         => ['actif' => 1],
    ];
    return $cache;
}

// ── Analyse des mesures selon seuils dynamiques ───────────
function analyserMesures(array $valeurs): array
{
    $alertes  = [];
    $seuils   = getSeuilsValeurs();
    $meta     = getSeuilsMeta();

    foreach ($meta as $capteur => $m) {
        $val = $valeurs[$capteur] ?? null;
        if ($val === null) continue;

        $warn = $seuils[$capteur]['warning']  ?? null;
        $crit = $seuils[$capteur]['critique'] ?? null;
        if ($warn === null || $crit === null) continue;

        if ($val >= $crit) {
            $alertes[] = [
                'capteur'  => $capteur,
                'valeur'   => $val,
                'niveau'   => 'critique',
                'seuil'    => $crit,
                'unite'    => $m['unite'],
                'risque'   => $m['risque'],
                'solution' => $m['solution'],
            ];
        } elseif ($val >= $warn) {
            $alertes[] = [
                'capteur'  => $capteur,
                'valeur'   => $val,
                'niveau'   => 'warning',
                'seuil'    => $warn,
                'unite'    => $m['unite'],
                'risque'   => $m['risque'],
                'solution' => $m['solution'],
            ];
        }
    }
    return $alertes;
}

// ── Envoi email alerte HTML ────────────────────────────────
function envoyerEmailAlerte(array $alerte, string $horodatage): void
{
    $users = DB::table('users')->where('validation_status', 'valide')->get();
    if ($users->isEmpty()) return;

    $capteurNom  = strtoupper($alerte['capteur']);
    $niveauLabel = $alerte['niveau'] === 'critique' ? 'CRITIQUE' : 'AVERTISSEMENT';
    $couleur     = $alerte['niveau'] === 'critique' ? '#ff5733' : '#ffd633';
    $sujet       = '[' . $niveauLabel . '] Alerte ' . $capteurNom . ' — Salle Serveurs';

    $html =
        '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>'
        . 'body{background:#060d1f;font-family:Arial,sans-serif;margin:0}'
        . '.w{max-width:560px;margin:0 auto;background:#060d1f}'
        . '.h{background:linear-gradient(135deg,#0e1a38,#060d1f);padding:24px;text-align:center;border-bottom:3px solid ' . $couleur . '}'
        . '.hl{color:' . $couleur . ';font-size:20px;font-weight:900;letter-spacing:2px;margin:0}'
        . '.hs{color:#5a6a99;font-size:10px;margin-top:4px;letter-spacing:1.5px}'
        . '.b{background:#0a1428;padding:24px}'
        . '.badge{display:inline-block;background:rgba(255,255,255,.04);color:' . $couleur . ';border:1px solid ' . $couleur . '33;padding:5px 14px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:1px;margin-bottom:14px}'
        . 'table{width:100%;border-collapse:collapse;margin:10px 0}'
        . 'td{padding:9px 10px;font-size:13px;border-bottom:1px solid #0e1c35}'
        . '.k{color:#8899cc;width:42%;font-weight:600}'
        . '.v{color:#c7d2ff}'
        . '.f{background:#060d1f;padding:14px;text-align:center;color:#3a4a6a;font-size:11px;border-top:1px solid #0e1c35}'
        . '</style></head><body>'
        . '<div class="w">'
        . '<div class="h"><h1 class="hl">&#9889; SUPSERVER &mdash; ' . $niveauLabel . '</h1><div class="hs">PLATEFORME IoT &mdash; SURVEILLANCE SALLES SERVEURS</div></div>'
        . '<div class="b">'
        . '<div><span class="badge">&#9888; ALERTE ' . $capteurNom . '</span></div>'
        . '<table>'
        . '<tr><td class="k">Capteur</td><td class="v">' . $capteurNom . '</td></tr>'
        . '<tr><td class="k">Valeur mesur&eacute;e</td><td class="v" style="color:' . $couleur . ';font-weight:700">' . $alerte['valeur'] . ' ' . htmlspecialchars($alerte['unite']) . '</td></tr>'
        . '<tr><td class="k">Seuil franchi</td><td class="v">' . $alerte['seuil'] . ' ' . htmlspecialchars($alerte['unite']) . '</td></tr>'
        . '<tr><td class="k">Niveau</td><td class="v" style="color:' . $couleur . '">' . $niveauLabel . '</td></tr>'
        . '<tr><td class="k">Date / Heure</td><td class="v">' . $horodatage . '</td></tr>'
        . '<tr><td class="k">Risques</td><td class="v">' . htmlspecialchars($alerte['risque']) . '</td></tr>'
        . '<tr><td class="k">Solutions</td><td class="v">' . htmlspecialchars($alerte['solution']) . '</td></tr>'
        . '</table>'
        . '</div>'
        . '<div class="f">Plateforme de Surveillance &mdash; Alerte automatique &mdash; Ne pas r&eacute;pondre</div>'
        . '</div></body></html>';

    foreach ($users as $user) {
        try {
            Mail::html($html, function ($mail) use ($user, $sujet) {
                $mail->to($user->email)->subject($sujet);
            });
        } catch (\Exception $e) {}
    }
}


// ── SMS via SIM900 (serial port) ──────────────────────────
function envoyerSMS(array $phones, string $msg): void
{
    if (empty($phones)) return;
    $msg = mb_substr($msg, 0, 160);

    // Detect serial port
    $port = null;
    foreach (['/dev/ttyUSB0','/dev/ttyUSB1','/dev/ttyACM0','/dev/ttyACM1'] as $p) {
        if (file_exists($p)) { $port = $p; break; }
    }
    if (!$port) return;

    try {
        exec('stty -F ' . escapeshellarg($port) . ' 9600 cs8 -cstopb -parenb raw 2>/dev/null');
        $fd = @fopen($port, 'r+b');
        if (!$fd) return;
        stream_set_blocking($fd, false);

        foreach ($phones as $phone) {
            $phone = trim($phone);
            if (strlen($phone) < 6) continue;
            @fwrite($fd, "AT\r\n");         usleep(400000);
            @fwrite($fd, "AT+CMGF=1\r\n"); usleep(400000);
            @fwrite($fd, 'AT+CMGS="' . $phone . '"' . "\r\n"); usleep(800000);
            @fwrite($fd, $msg . chr(26));   usleep(6000000);
        }
        @fclose($fd);
    } catch (\Exception $e) {}
}

function collecterPhonesUtilisateurs(): array
{
    $phones = [];
    try {
        $users = DB::table('users')
            ->where('validation_status', 'valide')
            ->whereNotNull('telephone')
            ->where('telephone', '!=', '')
            ->get();
        foreach ($users as $u) {
            $tel = trim($u->telephone ?? '');
            if ($tel === '') continue;
            // Prepend indicatif if not already international
            if (!str_starts_with($tel, '+')) {
                $ind = trim($u->indicatif_tel ?? '');
                $tel = $ind . preg_replace('/\D/', '', $tel);
            }
            if (strlen(preg_replace('/\D/', '', $tel)) >= 7) {
                $phones[] = $tel;
            }
        }
    } catch (\Exception $e) {}
    // Always include admin
    if (!in_array('+237692543407', $phones)) {
        $phones[] = '+237692543407';
    }
    return array_unique($phones);
}

function envoyerSMSAlerte(array $alerte, string $horodatage): void
{
    $niv = $alerte['niveau'] === 'critique' ? 'CRITIQUE' : 'WARNING';
    $msg = 'SUPSERVER ' . $niv . ': ' . strtoupper($alerte['capteur'])
         . '=' . $alerte['valeur'] . $alerte['unite']
         . ' Seuil=' . $alerte['seuil'] . $alerte['unite']
         . ' ' . $horodatage;
    envoyerSMS(collecterPhonesUtilisateurs(), $msg);
}

function envoyerSMSMouvement(string $horodatage): void
{
    $msg = 'SUPSERVER ALERTE SECURITE: Mouvement detecte dans la salle serveurs! ' . $horodatage;
    envoyerSMS(collecterPhonesUtilisateurs(), $msg);
}

// ── État alertes — évite les spams répétés ────────────────
function chargerEtatAlertes(): array
{
    $path = storage_path('app/alert_state.json');
    if (file_exists($path)) {
        $d = json_decode(file_get_contents($path), true);
        if (is_array($d)) return $d;
    }
    return [
        'temperature' => 'normal',
        'humidite'    => 'normal',
        'gaz'         => 'normal',
        'courant'     => 'normal',
        'puissance'   => 'normal',
        'pir_last'    => 0,
    ];
}

function sauvegarderEtatAlertes(array $state): void
{
    @file_put_contents(storage_path('app/alert_state.json'), json_encode($state));
}

function envoyerEmailMouvement(string $horodatage, ?int $salleId): void
{
    $couleur = '#ffd633';
    $users   = DB::table('users')->where('validation_status', 'valide')->get();
    if ($users->isEmpty()) return;

    $esc = fn($v) => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
    $html =
        '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>'
        . 'body{background:#060d1f;font-family:Arial,sans-serif;margin:0}'
        . '.w{max-width:560px;margin:0 auto;background:#060d1f}'
        . '.h{background:linear-gradient(135deg,#0e1a38,#060d1f);padding:24px;text-align:center;border-bottom:3px solid #ffd633}'
        . '.hl{color:#ffd633;font-size:20px;font-weight:900;letter-spacing:2px;margin:0}'
        . '.b{background:#0a1428;padding:24px}'
        . 'table{width:100%;border-collapse:collapse;margin:10px 0}'
        . 'td{padding:9px 10px;font-size:13px;border-bottom:1px solid #0e1c35}'
        . '.k{color:#8899cc;width:42%;font-weight:600}'
        . '.v{color:#c7d2ff}'
        . '.f{background:#060d1f;padding:14px;text-align:center;color:#3a4a6a;font-size:11px;border-top:1px solid #0e1c35}'
        . '</style></head><body><div class="w">'
        . '<div class="h"><h1 class="hl">&#128680; INTRUSION D&Eacute;TECT&Eacute;E</h1></div>'
        . '<div class="b"><table>'
        . '<tr><td class="k">Type</td><td class="v" style="color:#ffd633;font-weight:700">Mouvement PIR d&eacute;tect&eacute;</td></tr>'
        . '<tr><td class="k">Salle ID</td><td class="v">' . (int)$salleId . '</td></tr>'
        . '<tr><td class="k">Date / Heure</td><td class="v">' . $esc($horodatage) . '</td></tr>'
        . '<tr><td class="k">Risque</td><td class="v">Intrusion possible dans la salle serveurs</td></tr>'
        . '<tr><td class="k">Action</td><td class="v">V&eacute;rifier imm&eacute;diatement les acc&egrave;s, pr&eacute;venir la s&eacute;curit&eacute;</td></tr>'
        . '</table></div>'
        . '<div class="f">Plateforme de Surveillance &mdash; Alerte automatique</div>'
        . '</div></body></html>';

    foreach ($users as $user) {
        try {
            Mail::html($html, function ($mail) use ($user) {
                $mail->to($user->email)->subject('[INTRUSION] Mouvement détecté — Salle Serveurs');
            });
        } catch (\Exception $e) {}
    }
}

// ── POST /api/capteurs — réception données Arduino ────────
Route::post('/capteurs', function (Request $request) {

    $temperature = (float) ($request->temperature ?? 0);
    $humidite    = (float) ($request->humidite    ?? 0);
    $gaz         = (float) ($request->gaz         ?? 0);
    $courant     = (float) ($request->courant      ?? 0);
    $puissance   = (float) ($request->puissance   ?? 0);
    $tension     = $request->tension !== null ? (float) $request->tension : 220.0;
    $pir         = (bool)  ($request->pir          ?? false);
    $rssi        = $request->rssi     ? (int) $request->rssi     : null;
    $salleId     = $request->salle_id ? (int) $request->salle_id : null;

    // Toujours enregistrer la mesure
    DB::table('mesures')->insert([
        'temperature'  => $temperature,
        'humidite'     => $humidite,
        'gaz'          => $gaz,
        'courant'      => $courant,
        'puissance'    => $puissance,
        'tension'      => $tension,
        'pir_detecte'  => $pir ? 1 : 0,
        'salle_id'     => $salleId,
        'created_at'   => now(),
        'updated_at'   => now(),
    ]);

    $horodatage = now()->format('d/m/Y H:i:s');
    $alertes    = analyserMesures(compact('temperature', 'humidite', 'gaz', 'courant', 'puissance'));
    $seuils     = getSeuilsValeurs();

    // Charger l'état précédent des alertes
    $prevState = chargerEtatAlertes();
    $newState  = $prevState;

    // Construire map capteur → alerte
    $alertMap = [];
    foreach ($alertes as $a) { $alertMap[$a['capteur']] = $a; }

    $nbAlertes = 0;

    // Pour chaque capteur : notifier uniquement si état change ou s'aggrave
    foreach (['temperature', 'humidite', 'gaz', 'courant', 'puissance'] as $cap) {
        $newNiveau = isset($alertMap[$cap]) ? $alertMap[$cap]['niveau'] : 'normal';
        $oldNiveau = $prevState[$cap] ?? 'normal';
        $newState[$cap] = $newNiveau;

        if ($newNiveau === 'normal') continue;

        // Insérer l'alerte dans la table (pour le dashboard)
        DB::table('alertes')->insert([
            'type'       => $alertMap[$cap]['capteur'],
            'message'    => 'Dépassement seuil ' . $newNiveau . ' — ' . $cap
                          . ' : ' . $alertMap[$cap]['valeur'] . $alertMap[$cap]['unite'],
            'niveau'     => $newNiveau,
            'valeur'     => $alertMap[$cap]['valeur'] . $alertMap[$cap]['unite'],
            'salle_id'   => $salleId,
            'resolu'     => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $nbAlertes++;

        // Email uniquement si l'état change (normal→warning, normal→critique, warning→critique)
        $stateChange = ($newNiveau !== $oldNiveau)
            && !($oldNiveau === 'critique' && $newNiveau === 'warning');

        if ($stateChange) {
            envoyerEmailAlerte($alertMap[$cap], $horodatage);
            // SMS géré côté Arduino — pas de doublon
        }
    }

    // PIR : cooldown 5 minutes pour éviter les fausses alertes
    if ($pir && ($seuils['pir']['actif'] ?? 1)) {
        $pirCooldown = 300; // secondes
        if (time() - ($prevState['pir_last'] ?? 0) >= $pirCooldown) {
            DB::table('alertes')->insert([
                'type'       => 'pir',
                'message'    => 'Mouvement détecté dans la salle serveurs (salle ' . $salleId . ')',
                'niveau'     => 'warning',
                'valeur'     => 'Détecté',
                'salle_id'   => $salleId,
                'resolu'     => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            envoyerEmailMouvement($horodatage, $salleId);
            $newState['pir_last'] = time();
            $nbAlertes++;
        }
    }

    sauvegarderEtatAlertes($newState);

    return response()->json([
        'success' => true,
        'alertes' => $nbAlertes,
        'seuils'  => getSeuilsValeurs(),
    ]);
});


// ── GET /api/live-data ────────────────────────────────────
// Lit le fichier JSON écrit par le relay toutes les 2s (sans requête SQL)
Route::get('/live-data', function () {
    $file = '/tmp/latest_sensor.json';
    if (!file_exists($file) || (time() - filemtime($file)) > 30) {
        // Fichier absent ou trop vieux (>30s) → fallback DB
        $mesure = DB::table('mesures')->latest()->first();
        if (!$mesure) {
            return response()->json(['error' => 'no_data', 'pir' => false], 404);
        }
        return response()->json([
            'temperature' => (float)$mesure->temperature,
            'humidite'    => (float)$mesure->humidite,
            'gaz'         => (int)$mesure->gaz,
            'courant'     => (float)$mesure->courant,
            'puissance'   => (float)$mesure->puissance,
            'pir'         => (bool)($mesure->pir_detecte ?? false),
            'ts'          => $mesure->created_at,
            'source'      => 'db',
        ]);
    }
    $data = json_decode(file_get_contents($file), true) ?? [];
    $data['pir'] = (bool)($data['pir'] ?? false);
    $data['source'] = 'live';
    return response()->json($data);
});


// ── GET /api/mesures-recentes ─────────────────────────────
// Dernières N mesures pour le graphique historique
Route::get('/mesures-recentes', function (Request $request) {
    $n = min((int)($request->n ?? 30), 100);
    $mesures = DB::table('mesures')->latest()->limit($n)->get()->reverse()->values();
    return response()->json($mesures->map(function($m) {
        return [
            'temperature' => (float)$m->temperature,
            'humidite'    => (float)$m->humidite,
            'gaz'         => (int)$m->gaz,
            'courant'     => (float)$m->courant,
            'puissance'   => (float)$m->puissance,
            'pir'         => (bool)($m->pir_detecte ?? false),
            'ts'          => $m->created_at,
        ];
    }));
});


// ── GET /api/dashboard-data ───────────────────────────────
Route::get('/dashboard-data', function () {
    $mesure = DB::table('mesures')->latest()->first();
    if (!$mesure) {
        return response()->json([
            'temperature' => 0, 'humidite' => 0, 'gaz' => 0,
            'courant' => 0, 'puissance' => 0, 'tension' => 0, 'pir' => false,
        ]);
    }
    $data = (array) $mesure;
    $data['pir'] = (bool) ($mesure->pir_detecte ?? false);
    return response()->json($data);
});


// ── GET /api/stats ────────────────────────────────────────
Route::get('/stats', function () {
    return response()->json([
        'totalMesures'      => DB::table('mesures')->count(),
        'alertesCritiques'  => DB::table('alertes')->where('niveau', 'critique')->count(),
        'alertesWarning'    => DB::table('alertes')->where('niveau', 'warning')->count(),
        'alertesNonLues'    => DB::table('alertes')->where('resolu', 0)->count(),
        'totalUtilisateurs' => DB::table('users')->where('validation_status', 'valide')->count(),
        'derniereMesure'    => DB::table('mesures')->latest()->value('created_at'),
    ]);
});


// ── GET /api/seuils — seuils actifs ───────────────────────
Route::get('/seuils', function () {
    return response()->json(getSeuilsValeurs());
});


// ── GET /api/historique-data ──────────────────────────────
Route::get('/historique-data', function (Request $request) {
    $type    = $request->type    ?? 'mesures';
    $debut   = $request->debut   ?? now()->subDays(7)->toDateString();
    $fin     = $request->fin     ?? now()->toDateString();
    $limit   = min((int) ($request->limit ?? 100), 2000);
    $niveau  = $request->niveau  ?? '';
    $salleId = $request->salle_id ?? '';
    $tMin    = $request->temp_min  !== null && $request->temp_min  !== '' ? (float)$request->temp_min  : null;
    $tMax    = $request->temp_max  !== null && $request->temp_max  !== '' ? (float)$request->temp_max  : null;
    $hMin    = $request->hum_min   !== null && $request->hum_min   !== '' ? (float)$request->hum_min   : null;
    $hMax    = $request->hum_max   !== null && $request->hum_max   !== '' ? (float)$request->hum_max   : null;
    $gMin    = $request->gaz_min   !== null && $request->gaz_min   !== '' ? (float)$request->gaz_min   : null;
    $gMax    = $request->gaz_max   !== null && $request->gaz_max   !== '' ? (float)$request->gaz_max   : null;
    $cMin    = $request->courant_min !== null && $request->courant_min !== '' ? (float)$request->courant_min : null;
    $cMax    = $request->courant_max !== null && $request->courant_max !== '' ? (float)$request->courant_max : null;
    $pMin    = $request->pwr_min   !== null && $request->pwr_min   !== '' ? (float)$request->pwr_min   : null;
    $pMax    = $request->pwr_max   !== null && $request->pwr_max   !== '' ? (float)$request->pwr_max   : null;

    try {
        if ($type === 'alertes') {
            $q = DB::table('alertes')
                ->whereBetween('created_at', [$debut.' 00:00:00', $fin.' 23:59:59'])
                ->orderByDesc('created_at');
            if ($niveau)  $q->where('niveau', $niveau);
            if ($salleId) $q->where('salle_id', (int)$salleId);
            return response()->json($q->limit($limit)->get());
        }

        $q = DB::table('mesures')
            ->whereBetween('created_at', [$debut.' 00:00:00', $fin.' 23:59:59'])
            ->orderByDesc('created_at');
        if ($salleId) $q->where('salle_id', (int)$salleId);
        if ($tMin !== null) $q->where('temperature', '>=', $tMin);
        if ($tMax !== null) $q->where('temperature', '<=', $tMax);
        if ($hMin !== null) $q->where('humidite', '>=', $hMin);
        if ($hMax !== null) $q->where('humidite', '<=', $hMax);
        if ($gMin !== null) $q->where('gaz', '>=', $gMin);
        if ($gMax !== null) $q->where('gaz', '<=', $gMax);
        if ($cMin !== null) $q->where('courant', '>=', $cMin);
        if ($cMax !== null) $q->where('courant', '<=', $cMax);
        if ($pMin !== null) $q->where('puissance', '>=', $pMin);
        if ($pMax !== null) $q->where('puissance', '<=', $pMax);
        return response()->json($q->limit($limit)->get());
    } catch (\Exception $e) {
        return response()->json([]);
    }
});


// ── GET /api/alertes-recentes ─────────────────────────────
Route::get('/alertes-recentes', function (Request $request) {
    $limit = min((int) ($request->limit ?? 30), 500);
    try {
        return response()->json(DB::table('alertes')->latest()->limit($limit)->get());
    } catch (\Exception $e) {
        return response()->json([]);
    }
});


// ── POST /api/alertes/lire ────────────────────────────────
Route::post('/alertes/lire', function (Request $request) {
    $id = $request->id;
    if ($id === 'all') {
        DB::table('alertes')->update(['resolu' => 1]);
    } else {
        DB::table('alertes')->where('id', $id)->update(['resolu' => 1]);
    }
    return response()->json(['success' => true]);
});


// ── GET /api/salles-list — liste légère pour dropdowns ────
Route::get('/salles-list', function () {
    try {
        return response()->json(DB::table('salles')->select('id','nom')->orderBy('nom')->get());
    } catch (\Exception $e) {
        return response()->json([]);
    }
});


// ── GET /api/filter — filtrage avancé multi-paramètres ────
Route::get('/filter', function (Request $request) {
    $allowed = ['mesures','alertes','salles','serveurs'];
    $type    = in_array($request->type, $allowed) ? $request->type : 'mesures';
    $debut   = $request->debut    ?? now()->subDays(7)->toDateString();
    $fin     = $request->fin      ?? now()->toDateString();
    $limit   = min((int)($request->limit ?? 500), 10000);
    $niveau  = $request->niveau   ?? '';
    $salleId = $request->salle_id ?? '';
    $tMin    = $request->temp_min    !== null && $request->temp_min    !== '' ? (float)$request->temp_min    : null;
    $tMax    = $request->temp_max    !== null && $request->temp_max    !== '' ? (float)$request->temp_max    : null;
    $hMin    = $request->hum_min     !== null && $request->hum_min     !== '' ? (float)$request->hum_min     : null;
    $hMax    = $request->hum_max     !== null && $request->hum_max     !== '' ? (float)$request->hum_max     : null;
    $gMin    = $request->gaz_min     !== null && $request->gaz_min     !== '' ? (float)$request->gaz_min     : null;
    $gMax    = $request->gaz_max     !== null && $request->gaz_max     !== '' ? (float)$request->gaz_max     : null;
    $cMin    = $request->courant_min !== null && $request->courant_min !== '' ? (float)$request->courant_min : null;
    $cMax    = $request->courant_max !== null && $request->courant_max !== '' ? (float)$request->courant_max : null;
    $pMin    = $request->pwr_min     !== null && $request->pwr_min     !== '' ? (float)$request->pwr_min     : null;
    $pMax    = $request->pwr_max     !== null && $request->pwr_max     !== '' ? (float)$request->pwr_max     : null;

    try {
        if ($type === 'salles') {
            $data = DB::table('salles')->get();
            return response()->json(['data' => $data, 'total' => $data->count()]);
        }
        if ($type === 'serveurs') {
            $data = DB::table('serveurs')->get();
            return response()->json(['data' => $data, 'total' => $data->count()]);
        }
        if ($type === 'alertes') {
            $q = DB::table('alertes')
                ->whereBetween('created_at', [$debut.' 00:00:00', $fin.' 23:59:59'])
                ->orderByDesc('created_at');
            if ($niveau)  $q->where('niveau', $niveau);
            if ($salleId) $q->where('salle_id', (int)$salleId);
            $total = $q->count();
            $data  = $q->limit($limit)->get();
            return response()->json(['data' => $data, 'total' => $total]);
        }
        // mesures
        $q = DB::table('mesures')
            ->whereBetween('created_at', [$debut.' 00:00:00', $fin.' 23:59:59'])
            ->orderByDesc('created_at');
        if ($salleId) $q->where('salle_id', (int)$salleId);
        if ($tMin !== null) $q->where('temperature', '>=', $tMin);
        if ($tMax !== null) $q->where('temperature', '<=', $tMax);
        if ($hMin !== null) $q->where('humidite', '>=', $hMin);
        if ($hMax !== null) $q->where('humidite', '<=', $hMax);
        if ($gMin !== null) $q->where('gaz', '>=', $gMin);
        if ($gMax !== null) $q->where('gaz', '<=', $gMax);
        if ($cMin !== null) $q->where('courant', '>=', $cMin);
        if ($cMax !== null) $q->where('courant', '<=', $cMax);
        if ($pMin !== null) $q->where('puissance', '>=', $pMin);
        if ($pMax !== null) $q->where('puissance', '<=', $pMax);
        $total = $q->count();
        $data  = $q->limit($limit)->get();
        return response()->json(['data' => $data, 'total' => $total]);
    } catch (\Exception $e) {
        return response()->json(['data' => [], 'total' => 0, 'error' => $e->getMessage()]);
    }
});


// ── GET /api/seuils-arduino — format CSV pour Arduino ─────
// Retourne: tw,tc,hw,hc,gw,gc,cw,cc,pw,pc,pir
// ex: 35,40,75,85,300,500,10,15,3000,5000,1
Route::get('/seuils-arduino', function () {
    $s = getSeuilsValeurs();
    $csv = implode(',', [
        (int) ($s['temperature']['warning']  ?? 35),
        (int) ($s['temperature']['critique'] ?? 40),
        (int) ($s['humidite']['warning']     ?? 75),
        (int) ($s['humidite']['critique']    ?? 85),
        (int) ($s['gaz']['warning']          ?? 300),
        (int) ($s['gaz']['critique']         ?? 500),
        (int) ($s['courant']['warning']      ?? 10),
        (int) ($s['courant']['critique']     ?? 15),
        (int) ($s['puissance']['warning']    ?? 3000),
        (int) ($s['puissance']['critique']   ?? 5000),
        ($s['pir']['actif'] ?? 1) ? 1 : 0,
    ]);
    return response($csv, 200)->header('Content-Type', 'text/plain');
});


// ── GET /api/phones — numéros téléphone pour Arduino ──────
// Retourne: +237692543407,+237699001122,...
Route::get('/phones', function () {
    $phones = [];
    try {
        $users = DB::table('users')
            ->where('validation_status', 'valide')
            ->whereNotNull('telephone')
            ->where('telephone', '!=', '')
            ->get();
        foreach ($users as $u) {
            $tel = trim($u->telephone ?? '');
            if ($tel === '') continue;
            if (!str_starts_with($tel, '+')) {
                $ind = trim($u->indicatif_tel ?? '');
                $tel = $ind . preg_replace('/\D/', '', $tel);
            }
            if (strlen(preg_replace('/\D/', '', $tel)) >= 7) {
                $phones[] = $tel;
            }
        }
    } catch (\Exception $e) {}

    // Admin principal toujours inclus
    if (!in_array('+237692543407', $phones)) {
        $phones[] = '+237692543407';
    }

    return response(implode(',', array_unique($phones)), 200)
        ->header('Content-Type', 'text/plain');
});


// ── DELETE /api/alerte/{id} ───────────────────────────────
Route::delete('/alerte/{id}', function (int $id) {
    DB::table('alertes')->where('id', $id)->delete();
    return response()->json(['success' => true]);
});
