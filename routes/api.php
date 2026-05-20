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
        . '<div class="f">SupServer IoT &mdash; Alerte automatique &mdash; Ne pas r&eacute;pondre</div>'
        . '</div></body></html>';

    foreach ($users as $user) {
        try {
            Mail::html($html, function ($mail) use ($user, $sujet) {
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
    $rssi        = $request->rssi    ? (int) $request->rssi    : null;
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
    $alertes    = analyserMesures(compact('temperature', 'humidite', 'gaz', 'courant', 'puissance'));
    $seuils     = getSeuilsValeurs();

    foreach ($alertes as $alerte) {
        DB::table('alertes')->insert([
            'type_alerte' => $alerte['capteur'],
            'message'     => 'Dépassement seuil ' . $alerte['niveau'] . ' — ' . $alerte['capteur'] . ' : ' . $alerte['valeur'] . $alerte['unite'],
            'niveau'      => $alerte['niveau'],
            'valeur'      => $alerte['valeur'] . $alerte['unite'],
            'salle_id'    => $salleId,
            'lu'          => false,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        envoyerEmailAlerte($alerte, $horodatage);
    }

    // PIR actif selon seuils
    if ($pir && ($seuils['pir']['actif'] ?? 1)) {
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


// ── GET /api/dashboard-data ───────────────────────────────
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
        DB::table('alertes')->update(['lu' => true]);
    } else {
        DB::table('alertes')->where('id', $id)->update(['lu' => true]);
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
