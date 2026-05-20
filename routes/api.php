<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\GeoController;

// ── Géographie ────────────────────────────────────────────
Route::get('/geo/states/{country}',               [GeoController::class, 'states']);
Route::get('/geo/cities/{country}',               [GeoController::class, 'cities']);
Route::get('/geo/state-cities/{country}/{state}', [GeoController::class, 'stateCities']);


// ── Seuils d'alerte ───────────────────────────────────────
const SEUILS = [
    'temperature' => ['warning' => 35,   'critique' => 40,   'unite' => '°C',  'risque' => 'Surchauffe serveurs, défaillance matérielle',     'solution' => 'Vérifier climatisation, ventilation, redémarrer les systèmes de refroidissement'],
    'humidite'    => ['warning' => 75,   'critique' => 85,   'unite' => '%',   'risque' => 'Condensation, court-circuit, corrosion équipements', 'solution' => 'Activer déshumidificateur, vérifier étanchéité'],
    'gaz'         => ['warning' => 300,  'critique' => 500,  'unite' => 'ppm', 'risque' => 'Fuite de gaz dangereux, risque d\'incendie/explosion', 'solution' => 'Évacuer la salle, couper l\'alimentation, appeler les secours'],
    'courant'     => ['warning' => 10,   'critique' => 15,   'unite' => 'A',   'risque' => 'Surcharge électrique, risque de court-circuit',       'solution' => 'Réduire la charge, vérifier les disjoncteurs'],
    'puissance'   => ['warning' => 3000, 'critique' => 5000, 'unite' => 'W',   'risque' => 'Consommation excessive, risque de coupure générale',  'solution' => 'Éteindre les équipements non critiques, vérifier l\'alimentation'],
];

function analyserMesures(array $valeurs): array
{
    $alertes = [];
    foreach (SEUILS as $capteur => $seuils) {
        $val = $valeurs[$capteur] ?? null;
        if ($val === null) continue;

        if ($val >= $seuils['critique']) {
            $alertes[] = [
                'capteur' => $capteur,
                'valeur'  => $val,
                'niveau'  => 'critique',
                'seuil'   => $seuils['critique'],
                'unite'   => $seuils['unite'],
                'risque'  => $seuils['risque'],
                'solution'=> $seuils['solution'],
            ];
        } elseif ($val >= $seuils['warning']) {
            $alertes[] = [
                'capteur' => $capteur,
                'valeur'  => $val,
                'niveau'  => 'warning',
                'seuil'   => $seuils['warning'],
                'unite'   => $seuils['unite'],
                'risque'  => $seuils['risque'],
                'solution'=> $seuils['solution'],
            ];
        }
    }
    return $alertes;
}

function envoyerEmailAlerte(array $alerte, string $horodatage): void
{
    $users = DB::table('users')->where('validation_status', 'valide')->get();
    if ($users->isEmpty()) return;

    $capteurNom = strtoupper($alerte['capteur']);
    $niveauLabel = $alerte['niveau'] === 'critique' ? '🔴 CRITIQUE' : '🟡 AVERTISSEMENT';
    $sujet = "[{$niveauLabel}] Alerte {$capteurNom} — Salle Serveurs";

    $corps =
        "═══════════════════════════════════════\n" .
        "   ALERTE SALLE SERVEURS — {$niveauLabel}\n" .
        "═══════════════════════════════════════\n\n" .
        "📍 Capteur     : {$capteurNom}\n" .
        "📊 Valeur      : {$alerte['valeur']}{$alerte['unite']}\n" .
        "⚠️  Seuil franchi : {$alerte['seuil']}{$alerte['unite']}\n" .
        "🕐 Date/Heure  : {$horodatage}\n\n" .
        "⚠️  RISQUES\n" .
        "{$alerte['risque']}\n\n" .
        "✅ SOLUTIONS RECOMMANDÉES\n" .
        "{$alerte['solution']}\n\n" .
        "───────────────────────────────────────\n" .
        "SupServer — Plateforme IoT Surveillance\n" .
        "Ceci est un message automatique.\n";

    foreach ($users as $user) {
        try {
            Mail::raw($corps, function ($mail) use ($user, $sujet) {
                $mail->to($user->email)->subject($sujet);
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
    $tension     = (float) ($request->tension      ?? 0);
    $pir         = (bool)  ($request->pir          ?? false);
    $rssi        = $request->rssi ? (int) $request->rssi : null;
    $salleId     = $request->salle_id ? (int) $request->salle_id : null;

    DB::table('mesures')->insert([
        'temperature' => $temperature,
        'humidite'    => $humidite,
        'gaz'         => $gaz,
        'courant'     => $courant,
        'puissance'   => $puissance,
        'tension'     => $tension ?: null,
        'pir'         => $pir,
        'rssi'        => $rssi,
        'salle_id'    => $salleId,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    $horodatage = now()->format('d/m/Y H:i:s');
    $alertes = analyserMesures(compact('temperature', 'humidite', 'gaz', 'courant', 'puissance'));

    foreach ($alertes as $alerte) {
        DB::table('alertes')->insert([
            'type_alerte' => $alerte['capteur'],
            'message'     => "Dépassement seuil {$alerte['niveau']} — {$alerte['capteur']} : {$alerte['valeur']}{$alerte['unite']}",
            'niveau'      => $alerte['niveau'],
            'valeur'      => $alerte['valeur'] . $alerte['unite'],
            'salle_id'    => $salleId,
            'lu'          => false,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        envoyerEmailAlerte($alerte, $horodatage);
    }

    if ($pir) {
        DB::table('alertes')->insert([
            'type_alerte' => 'pir',
            'message'     => 'Mouvement détecté dans la salle serveurs',
            'niveau'      => 'warning',
            'valeur'      => 'Détecté',
            'salle_id'    => $salleId,
            'lu'          => false,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    return response()->json([
        'success' => true,
        'alertes' => count($alertes) + ($pir ? 1 : 0),
    ]);
});


// ── GET /api/dashboard-data — dernière mesure ─────────────
Route::get('/dashboard-data', function () {
    $mesure = DB::table('mesures')->latest()->first();
    if (!$mesure) {
        return response()->json([
            'temperature' => 0, 'humidite' => 0, 'gaz' => 0,
            'courant' => 0, 'puissance' => 0, 'tension' => 0, 'pir' => false,
        ]);
    }
    return response()->json($mesure);
});


// ── GET /api/stats ────────────────────────────────────────
Route::get('/stats', function () {
    return response()->json([
        'totalMesures'      => DB::table('mesures')->count(),
        'alertesCritiques'  => DB::table('alertes')->where('niveau', 'critique')->count(),
        'alertesWarning'    => DB::table('alertes')->where('niveau', 'warning')->count(),
        'alertesNonLues'    => DB::table('alertes')->where('lu', false)->count(),
        'totalUtilisateurs' => DB::table('users')->where('validation_status', 'valide')->count(),
        'derniereMesure'    => DB::table('mesures')->latest()->value('created_at'),
    ]);
});


// ── GET /api/historique-data — 20 dernières mesures ───────
Route::get('/historique-data', function () {
    $mesures = DB::table('mesures')
        ->latest()
        ->limit(20)
        ->get()
        ->reverse()
        ->values();
    return response()->json($mesures);
});


// ── GET /api/alertes-recentes ─────────────────────────────
Route::get('/alertes-recentes', function () {
    return response()->json(
        DB::table('alertes')
            ->latest()
            ->limit(30)
            ->get()
    );
});


// ── POST /api/alertes/lire ────────────────────────────────
Route::post('/alertes/lire', function (Request $request) {
    $id = $request->id;
    if ($id === 'all') {
        DB::table('alertes')->update(['lu' => true]);
    } else {
        DB::table('alertes')->where('id', $id)->update(['lu' => true]);
    }
    return response()->json(['success' => true]);
});
