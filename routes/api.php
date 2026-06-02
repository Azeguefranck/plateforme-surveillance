<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\GeoController;

// ── Routes géographie (proxy GeoNames) ───────────────────────────────────────
Route::get('/geo/states/{country}',               [GeoController::class, 'states']);
Route::get('/geo/cities/{country}',               [GeoController::class, 'cities']);
Route::get('/geo/state-cities/{country}/{state}', [GeoController::class, 'stateCities']);
Route::get('/geo/subcities/{country}/{city}',     [GeoController::class, 'subcities']);


// ── Métadonnées fixes des capteurs (risques & solutions) ─────────────────────
if (!function_exists('getSeuilsMeta')) :
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
    ];
}
endif;

// ── Seuils depuis storage/app/seuils.json (mis en cache par requête) ─────────
if (!function_exists('getSeuilsValeurs')) :
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

    // Valeurs par défaut si le fichier n'existe pas
    $cache = [
        'temperature' => ['warning' => 28,  'critique' => 32],
        'humidite'    => ['warning' => 75,  'critique' => 85],
        'gaz'         => ['warning' => 400, 'critique' => 600],
        'pir'         => ['actif' => 1],
    ];
    return $cache;
}
endif;

// ── Compare les valeurs mesurées aux seuils, retourne les alertes actives ─────
// $presents : clés des capteurs effectivement envoyés par l'Arduino
if (!function_exists('analyserMesures')) :
function analyserMesures(array $valeurs, array $presents = []): array
{
    $alertes = [];
    $seuils  = getSeuilsValeurs();
    $meta    = getSeuilsMeta();

    foreach ($meta as $capteur => $m) {
        // Ignorer si le capteur n'était pas dans la requête
        if (!empty($presents) && !in_array($capteur, $presents, true)) continue;

        $val  = $valeurs[$capteur] ?? null;
        if ($val === null) continue;

        $warn = $seuils[$capteur]['warning']  ?? null;
        $crit = $seuils[$capteur]['critique'] ?? null;
        if ($warn === null || $crit === null) continue;

        if ($val >= $crit) {
            $alertes[] = ['capteur' => $capteur, 'valeur' => $val, 'niveau' => 'critique',
                          'seuil' => $crit, 'unite' => $m['unite'],
                          'risque' => $m['risque'], 'solution' => $m['solution']];
        } elseif ($val >= $warn) {
            $alertes[] = ['capteur' => $capteur, 'valeur' => $val, 'niveau' => 'warning',
                          'seuil' => $warn, 'unite' => $m['unite'],
                          'risque' => $m['risque'], 'solution' => $m['solution']];
        }
    }
    return $alertes;
}
endif;

// ── Envoi email d'alerte (warning ou critique) par fork non-bloquant ─────────
if (!function_exists('envoyerEmailAlerte')) :
function envoyerEmailAlerte(array $alerte, string $horodatage, array $mesures = [], ?int $salleId = null): void
{
    $adminEmail = 'franckazegue0007@gmail.com';
    $adminUser  = DB::table('users')->where('email', $adminEmail)->first();
    if (!$adminUser) return;

    $salleNom  = null;
    $equipNoms = [];
    if ($salleId) {
        try {
            $salle     = DB::table('salles')->where('id', $salleId)->first();
            $salleNom  = $salle ? $salle->nom : null;
            $equipNoms = DB::table('serveurs')->where('salle_id', $salleId)->pluck('nom')->toArray();
        } catch (\Exception $e) {}
    }

    $critique    = $alerte['niveau'] === 'critique';
    $capteurNom  = strtoupper($alerte['capteur']);
    $couleur     = $critique ? '#ff5733' : '#ffd633';
    $couleurRgb  = $critique ? '255,87,51' : '255,214,51';
    $niveauLabel = $critique ? 'CRITIQUE' : 'WARNING';
    $icone       = $critique ? '🚨' : '⚠️';
    $sujet       = $icone . ' [' . $niveauLabel . '] Alerte ' . $capteurNom . ' — ' . ($salleNom ?? 'Salle Serveurs');
    $platformUrl = rtrim(config('app.url'), '/') . '/dashboard';

    $seuils     = getSeuilsValeurs();
    $valCouleur = function (string $cap, $val) use ($seuils, $couleur): string {
        $crit = $seuils[$cap]['critique'] ?? null;
        return ($crit !== null && $val >= $crit) ? 'color:' . $couleur . ';font-weight:700' : '';
    };

    $html =
        '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>'
        . 'body{background:#060d1f;font-family:Arial,sans-serif;margin:0;padding:0}'
        . '.w{max-width:560px;width:100%;margin:0 auto;background:#060d1f}'
        . '.h{background:linear-gradient(135deg,#0e0a00,#060d1f);padding:28px 24px;text-align:center;border-bottom:3px solid ' . $couleur . '}'
        . '.hl{color:' . $couleur . ';font-size:20px;font-weight:900;letter-spacing:1px;margin:0;word-break:break-word}'
        . '.hs{color:#5a6a99;font-size:10px;margin-top:6px;letter-spacing:1px}'
        . '.b{background:#0a1428;padding:20px}'
        . '.badge{display:inline-block;background:rgba(' . $couleurRgb . ',.12);color:' . $couleur . ';border:1px solid ' . $couleur . '55;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:1px;margin-bottom:14px}'
        . 'table{width:100%;border-collapse:collapse;margin:10px 0;table-layout:fixed}'
        . 'td{padding:9px 10px;font-size:13px;border-bottom:1px solid #0e1c35;word-break:break-word;overflow-wrap:break-word;white-space:normal}'
        . '.k{color:#8899cc;width:38%;font-weight:600;vertical-align:top}'
        . '.v{color:#c7d2ff}'
        . '.cta{display:block;margin:18px auto 0;padding:11px 24px;background:' . $couleur . ';color:#fff;font-weight:700;font-size:13px;text-decoration:none;border-radius:8px;text-align:center;max-width:220px}'
        . '.f{background:#060d1f;padding:12px;text-align:center;color:#3a4a6a;font-size:11px;border-top:1px solid #0e1c35;margin-top:8px;word-break:break-word}'
        . '</style></head><body>'
        . '<div class="w"><div class="h">'
        . '<h1 class="hl">&#9888; SYST&Egrave;ME DE SURVEILLANCE &mdash; ALERTE ' . $niveauLabel . '</h1>'
        . '<div class="hs">PLATEFORME DE SURVEILLANCE DES PARAM&Egrave;TRES DES &Eacute;QUIPEMENTS D&rsquo;UNE SALLE SERVEURS</div>'
        . '</div><div class="b">'
        . '<div><span class="badge">&#9888; CAPTEUR : ' . $capteurNom . '</span></div>'
        . '<table>'
        . '<tr><td class="k">Capteur</td><td class="v">' . $capteurNom . '</td></tr>'
        . '<tr><td class="k">Valeur mesur&eacute;e</td><td class="v" style="color:' . $couleur . ';font-weight:700;font-size:14px">' . htmlspecialchars($alerte['valeur']) . ' ' . htmlspecialchars($alerte['unite']) . '</td></tr>'
        . '<tr><td class="k">Seuil d&eacute;pass&eacute;</td><td class="v">' . htmlspecialchars($alerte['seuil']) . ' ' . htmlspecialchars($alerte['unite']) . '</td></tr>'
        . '<tr><td class="k">Niveau</td><td class="v" style="color:' . $couleur . ';font-weight:700">' . ($critique ? '&#128308; CRITIQUE' : '&#128992; WARNING') . '</td></tr>'
        . '<tr><td class="k">Date / Heure</td><td class="v">' . htmlspecialchars($horodatage) . '</td></tr>'
        . ($salleNom ? '<tr><td class="k">Salle</td><td class="v" style="color:#33b5ff;font-weight:700">&#127970; ' . htmlspecialchars($salleNom) . '</td></tr>' : '')
        . (count($equipNoms) > 0
            ? '<tr><td class="k" style="vertical-align:top">&#128421; &Eacute;quipements</td><td class="v">' . implode('<br>', array_map('htmlspecialchars', $equipNoms)) . '</td></tr>'
            : '')
        . '</table>'
        . (empty($mesures) ? '' :
            '<div style="background:#060f28;border:1px solid #1e2f5a;border-radius:8px;padding:14px;margin:16px 0">'
          . '<div style="color:#33b5ff;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px">&#128202; Mesures au moment de l\'alerte</div>'
          . '<table style="margin:0">'
          . '<tr><td class="k" style="width:50%">&#127777; Temp&eacute;rature</td><td class="v" style="' . $valCouleur('temperature', $mesures['temperature'] ?? 0) . '">' . htmlspecialchars($mesures['temperature'] ?? '—') . ' °C</td></tr>'
          . '<tr><td class="k">&#128167; Humidit&eacute;</td><td class="v" style="' . $valCouleur('humidite', $mesures['humidite'] ?? 0) . '">' . htmlspecialchars($mesures['humidite'] ?? '—') . ' %</td></tr>'
          . '<tr><td class="k">&#128168; Gaz / Air</td><td class="v" style="' . $valCouleur('gaz', $mesures['gaz'] ?? 0) . '">' . htmlspecialchars($mesures['gaz'] ?? '—') . ' ppm</td></tr>'
          . '<tr style="border-bottom:none"><td class="k" style="border-bottom:none">&#128683; D&eacute;tecteur PIR</td><td class="v" style="border-bottom:none;' . (!empty($mesures['pir']) ? 'color:#ff5733;font-weight:700' : 'color:#33ff88') . '">' . (!empty($mesures['pir']) ? '&#128308; Mouvement d&eacute;tect&eacute;' : '&#128994; Aucun mouvement') . '</td></tr>'
          . '</table></div>'
        )
        . '<div style="background:#0e1428;border-left:3px solid ' . $couleur . ';border-radius:6px;padding:12px 14px;margin:14px 0">'
        . '<div style="color:' . $couleur . ';font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px">&#9888; Risques identifi&eacute;s</div>'
        . '<div style="color:#c7d2ff;font-size:13px">' . htmlspecialchars($alerte['risque']) . '</div>'
        . '</div>'
        . '<div style="background:#071428;border-left:3px solid #33ff88;border-radius:6px;padding:12px 14px;margin:14px 0">'
        . '<div style="color:#33ff88;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px">&#9989; Actions &agrave; entreprendre</div>'
        . '<div style="color:#c7d2ff;font-size:13px">' . htmlspecialchars($alerte['solution']) . '</div>'
        . '</div>'
        . '<a class="cta" href="' . htmlspecialchars($platformUrl) . '">&#128064; Voir le tableau de bord</a>'
        . '</div>'
        . '<div class="f">Syst&egrave;me de Surveillance &mdash; Alerte automatique &mdash; Ne pas r&eacute;pondre &agrave; cet email</div>'
        . '</div></body></html>';

    $emailTo   = $adminUser->email;
    $sujetMail = $sujet;

    // Envoi non-bloquant via fork pour ne pas retarder la réponse API
    if (function_exists('pcntl_fork')) {
        $pid = pcntl_fork();
        if ($pid === 0) {
            try { \Illuminate\Support\Facades\DB::connection()->disconnect(); } catch (\Throwable $e) {}
            try {
                Mail::html($html, function ($mail) use ($emailTo, $sujetMail) {
                    $mail->to($emailTo)->subject($sujetMail);
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Email alerte échoué: ' . $e->getMessage());
            }
            posix_kill(getmypid(), SIGKILL);
        } elseif ($pid > 0) {
            pcntl_waitpid($pid, $status, WNOHANG);
        }
    } else {
        // Fallback synchrone si pcntl non disponible
        try {
            Mail::html($html, function ($mail) use ($emailTo, $sujetMail) {
                $mail->to($emailTo)->subject($sujetMail);
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Email alerte échoué: ' . $e->getMessage());
        }
    }
}
endif;

// ── Envoi SMS via port série (module GSM) ─────────────────────────────────────
if (!function_exists('envoyerSMS')) :
function envoyerSMS(array $phones, string $msg): void
{
    if (empty($phones)) return;
    $msg = mb_substr($msg, 0, 160);

    // Cherche le port série disponible
    $port = null;
    foreach (['/dev/ttyUSB0', '/dev/ttyUSB1', '/dev/ttyACM0', '/dev/ttyACM1'] as $p) {
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
            @fwrite($fd, "AT\r\n");                             usleep(400000);
            @fwrite($fd, "AT+CMGF=1\r\n");                     usleep(400000);
            @fwrite($fd, 'AT+CMGS="' . $phone . '"' . "\r\n"); usleep(800000);
            @fwrite($fd, $msg . chr(26));                       usleep(6000000);
        }
        @fclose($fd);
    } catch (\Exception $e) {}
}
endif;

// ── Collecte les numéros validés + admin fixe ────────────────────────────────
if (!function_exists('collecterPhonesUtilisateurs')) :
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
            if (!str_starts_with($tel, '+')) {
                $tel = trim($u->indicatif_tel ?? '') . preg_replace('/\D/', '', $tel);
            }
            if (strlen(preg_replace('/\D/', '', $tel)) >= 7) {
                $phones[] = $tel;
            }
        }
    } catch (\Exception $e) {}

    if (!in_array('+237692543407', $phones)) {
        $phones[] = '+237692543407';
    }
    return array_unique($phones);
}
endif;

if (!function_exists('envoyerSMSAlerte')) :
function envoyerSMSAlerte(array $alerte, string $horodatage, ?int $salleId = null): void
{
    $niv   = $alerte['niveau'] === 'critique' ? 'CRITIQUE' : 'WARNING';
    $equip = '';
    if ($salleId) {
        try {
            $noms = DB::table('serveurs')->where('salle_id', $salleId)->pluck('nom')->take(2)->toArray();
            if ($noms) $equip = ' Equip:' . implode(',', $noms);
        } catch (\Exception $e) {}
    }
    $msg = 'SUPSERVER ' . $niv . ': ' . strtoupper($alerte['capteur'])
         . '=' . $alerte['valeur'] . $alerte['unite']
         . ' Seuil=' . $alerte['seuil'] . $alerte['unite']
         . $equip . ' ' . $horodatage;
    envoyerSMS(collecterPhonesUtilisateurs(), $msg);
}
endif;

if (!function_exists('envoyerSMSMouvement')) :
function envoyerSMSMouvement(string $horodatage): void
{
    $msg = 'SUPSERVER ALERTE SECURITE: Mouvement detecte dans la salle serveurs! ' . $horodatage;
    envoyerSMS(collecterPhonesUtilisateurs(), $msg);
}
endif;

// ── Charge/sauvegarde l'état des alertes (anti-spam email) ───────────────────
if (!function_exists('chargerEtatAlertes')) :
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
        'pir_last'    => 0,
        'email_last'  => ['temperature' => 0, 'humidite' => 0, 'gaz' => 0],
    ];
}
endif;

if (!function_exists('sauvegarderEtatAlertes')) :
function sauvegarderEtatAlertes(array $state): void
{
    @file_put_contents(storage_path('app/alert_state.json'), json_encode($state));
}
endif;

// ── Envoi email d'intrusion PIR ───────────────────────────────────────────────
if (!function_exists('envoyerEmailMouvement')) :
function envoyerEmailMouvement(string $horodatage, ?int $salleId): void
{
    $adminEmail = 'franckazegue0007@gmail.com';
    $adminUser  = DB::table('users')->where('email', $adminEmail)->first();
    if (!$adminUser) return;

    $esc  = fn($v) => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
    $html =
        '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>'
        . 'body{background:#060d1f;font-family:Arial,sans-serif;margin:0}'
        . '.w{max-width:560px;width:100%;margin:0 auto;background:#060d1f}'
        . '.h{background:linear-gradient(135deg,#0e1a38,#060d1f);padding:22px;text-align:center;border-bottom:3px solid #ffd633}'
        . '.hl{color:#ffd633;font-size:19px;font-weight:900;letter-spacing:1px;margin:0;word-break:break-word}'
        . '.b{background:#0a1428;padding:20px}'
        . 'table{width:100%;border-collapse:collapse;margin:10px 0;table-layout:fixed}'
        . 'td{padding:9px 10px;font-size:13px;border-bottom:1px solid #0e1c35;word-break:break-word;overflow-wrap:break-word;white-space:normal}'
        . '.k{color:#8899cc;width:38%;font-weight:600;vertical-align:top}'
        . '.v{color:#c7d2ff}'
        . '.f{background:#060d1f;padding:12px;text-align:center;color:#3a4a6a;font-size:11px;border-top:1px solid #0e1c35;word-break:break-word}'
        . '</style></head><body><div class="w">'
        . '<div class="h"><h1 class="hl">&#128680; INTRUSION D&Eacute;TECT&Eacute;E</h1></div>'
        . '<div class="b"><table>'
        . '<tr><td class="k">Type</td><td class="v" style="color:#ffd633;font-weight:700">Mouvement PIR d&eacute;tect&eacute;</td></tr>'
        . '<tr><td class="k">Salle ID</td><td class="v">' . (int) $salleId . '</td></tr>'
        . '<tr><td class="k">Date / Heure</td><td class="v">' . $esc($horodatage) . '</td></tr>'
        . '</table>'
        . '<div style="background:#1a1400;border-left:3px solid #ffd633;border-radius:6px;padding:12px 14px;margin:14px 0">'
        . '<div style="color:#ffd633;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px">&#9888; Risques identifi&eacute;s</div>'
        . '<div style="color:#c7d2ff;font-size:13px;word-break:break-word">Intrusion ou acc&egrave;s non autoris&eacute; dans la salle serveurs. Risque de vol, sabotage ou dommages sur les &eacute;quipements critiques.</div>'
        . '</div>'
        . '<div style="background:#071428;border-left:3px solid #33ff88;border-radius:6px;padding:12px 14px;margin:14px 0">'
        . '<div style="color:#33ff88;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px">&#9989; Actions &agrave; entreprendre</div>'
        . '<div style="color:#c7d2ff;font-size:13px;word-break:break-word">V&eacute;rifier imm&eacute;diatement les acc&egrave;s physiques, visionner les cam&eacute;ras de surveillance, pr&eacute;venir le responsable de s&eacute;curit&eacute; et consigner l\'incident.</div>'
        . '</div></div>'
        . '<div class="f">Plateforme de Surveillance &mdash; Alerte automatique</div>'
        . '</div></body></html>';

    $emailTo = $adminUser->email;

    if (function_exists('pcntl_fork')) {
        $pid = pcntl_fork();
        if ($pid === 0) {
            try { \Illuminate\Support\Facades\DB::connection()->disconnect(); } catch (\Throwable $e) {}
            try {
                Mail::html($html, function ($mail) use ($emailTo) {
                    $mail->to($emailTo)->subject('[INTRUSION] Mouvement détecté — Salle Serveurs');
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Email intrusion échoué: ' . $e->getMessage());
            }
            posix_kill(getmypid(), SIGKILL);
        } elseif ($pid > 0) {
            pcntl_waitpid($pid, $status, WNOHANG);
        }
    } else {
        try {
            Mail::html($html, function ($mail) use ($emailTo) {
                $mail->to($emailTo)->subject('[INTRUSION] Mouvement détecté — Salle Serveurs');
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Email intrusion échoué: ' . $e->getMessage());
        }
    }
}
endif;

// ── POST /api/capteurs — reçoit les données de l'Arduino ─────────────────────
Route::post('/capteurs', function (Request $request) {

    $temperature  = (float) ($request->temperature   ?? 0);
    $humidite     = (float) ($request->humidite      ?? 0);
    $gaz          = (float) ($request->gaz           ?? 0);
    $pir          = (bool)  ($request->pir           ?? false);
    $salleId      = $request->salle_id      ? (int) $request->salle_id      : null;
    $equipementId = $request->equipement_id ? (int) $request->equipement_id : null;

    // Liste des capteurs présents dans la requête (non null = connecté)
    $capteursPresents = array_keys(array_filter([
        'temperature' => $request->has('temperature') || $request->temperature !== null,
        'humidite'    => $request->has('humidite')    || $request->humidite    !== null,
        'gaz'         => $request->has('gaz')         || $request->gaz         !== null,
    ]));

    // Enregistrer la mesure en base
    DB::table('mesures')->insert([
        'temperature'   => $temperature,
        'humidite'      => $humidite,
        'gaz'           => $gaz,
        'pir_detecte'   => $pir ? 1 : 0,
        'salle_id'      => $salleId,
        'equipement_id' => $equipementId,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    $horodatage = now()->format('d/m/Y H:i:s');
    $alertes    = analyserMesures(compact('temperature', 'humidite', 'gaz'), $capteursPresents);
    $seuils     = getSeuilsValeurs();

    $prevState = chargerEtatAlertes();
    $newState  = $prevState;
    $alertMap  = [];
    foreach ($alertes as $a) { $alertMap[$a['capteur']] = $a; }

    $nbAlertes      = 0;
    $smsPending     = [];
    $poids          = ['normal' => 0, 'warning' => 1, 'critique' => 2];
    $EMAIL_COOLDOWN = 720; // 12 min entre deux rappels par capteur

    foreach (['temperature', 'humidite', 'gaz'] as $cap) {
        $newNiveau = isset($alertMap[$cap]) ? $alertMap[$cap]['niveau'] : 'normal';
        $oldNiveau = $prevState[$cap] ?? 'normal';
        $newState[$cap] = $newNiveau;

        if ($newNiveau === 'normal') continue;

        $isEscalade    = ($poids[$newNiveau] ?? 0) > ($poids[$oldNiveau] ?? 0);
        $lastEmail     = $prevState['email_last'][$cap] ?? 0;
        $cooldownPasse = (time() - $lastEmail) >= $EMAIL_COOLDOWN;

        if (!$isEscalade && !$cooldownPasse) continue;

        // Insère en base uniquement si escalade de niveau
        if ($isEscalade) {
            DB::table('alertes')->insert([
                'type'          => $alertMap[$cap]['capteur'],
                'message'       => 'Dépassement seuil ' . $newNiveau . ' — ' . $cap
                                 . ' : ' . $alertMap[$cap]['valeur'] . $alertMap[$cap]['unite'],
                'niveau'        => $newNiveau,
                'valeur'        => $alertMap[$cap]['valeur'] . $alertMap[$cap]['unite'],
                'salle_id'      => $salleId,
                'equipement_id' => $equipementId,
                'resolu'        => 0,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            $nbAlertes++;
        }

        envoyerEmailAlerte($alertMap[$cap], $horodatage, [
            'temperature' => $temperature,
            'humidite'    => $humidite,
            'gaz'         => $gaz,
            'pir'         => $pir,
        ], $salleId);

        $newState['email_last'][$cap] = time();

        $smsPending[] = ['msg' =>
            'SUPSERVER ' . ($newNiveau === 'critique' ? 'CRITIQUE' : 'WARNING')
            . ': ' . strtoupper($cap)
            . '=' . $alertMap[$cap]['valeur'] . $alertMap[$cap]['unite']
            . ' Seuil=' . $alertMap[$cap]['seuil'] . $alertMap[$cap]['unite']
            . ' ' . $horodatage,
        ];
    }

    // PIR : cooldown 10 min
    $PIR_EMAIL_COOLDOWN = 600;
    if ($pir && ($seuils['pir']['actif'] ?? 1)) {
        $lastPirEmail = $prevState['pir_last'] ?? 0;
        if (time() - $lastPirEmail >= $PIR_EMAIL_COOLDOWN) {
            DB::table('alertes')->insert([
                'type'       => 'pir',
                'message'    => 'Mouvement détecté dans la salle serveurs',
                'niveau'     => 'warning',
                'valeur'     => 'Détecté',
                'salle_id'   => $salleId,
                'resolu'     => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            envoyerEmailMouvement($horodatage, $salleId);
            $smsPending[] = ['msg' => 'SUPSERVER ALERTE SECURITE: Mouvement detecte ! ' . $horodatage];
            $newState['pir_last'] = time();
            $nbAlertes++;
        }
    }

    sauvegarderEtatAlertes($newState);

    // Envoi SMS en arrière-plan (ne bloque pas la réponse JSON)
    if (!empty($smsPending)) {
        $phones = collecterPhonesUtilisateurs();
        if (!empty($phones)) {
            $messages = array_column($smsPending, 'msg');
            $pid = function_exists('pcntl_fork') ? pcntl_fork() : -1;
            if ($pid === 0) {
                foreach ($messages as $msg) { envoyerSMS($phones, $msg); }
                exit(0);
            }
        }
    }

    return response()->json([
        'success' => true,
        'alertes' => $nbAlertes,
        'seuils'  => getSeuilsValeurs(),
    ]);
});


// ── GET /api/live-data — données brutes du fichier relay ─────────────────────
Route::get('/live-data', function () {
    $file     = '/tmp/latest_sensor.json';
    $seuilAge = 6; // secondes — au-delà = Arduino déconnecté

    if (!file_exists($file) || (time() - filemtime($file)) > $seuilAge) {
        return response()->json(['error' => 'no_data'], 204);
    }

    $data           = json_decode(file_get_contents($file), true) ?? [];
    $data['pir']    = (bool) ($data['pir'] ?? false);
    $data['source'] = 'live';

    try {
        $salle = $data['salle_id']
            ? DB::table('salles')->where('id', $data['salle_id'])->first()
            : null;
        $data['salle_nom']   = $salle ? $salle->nom : null;
        $data['equipements'] = $data['salle_id']
            ? DB::table('serveurs')->where('salle_id', $data['salle_id'])->pluck('nom')->toArray()
            : [];
    } catch (\Exception $e) {}

    return response()->json($data);
});


// ── GET /api/mesures-live — données temps réel par salle ─────────────────────
Route::get('/mesures-live', function () {
    $seuilAge = 6;
    $liveFile = '/tmp/latest_sensor.json';

    try {
        $salles   = DB::table('salles')->get()->keyBy('id');
        $serveurs = DB::table('serveurs')->get()->groupBy('salle_id');

        // Enrichit une entrée salle avec son nom et ses équipements
        $enrichir = function (int|string|null $sid) use ($salles, $serveurs): array {
            $salle  = $salles[$sid] ?? null;
            $equips = ($serveurs[$sid] ?? collect())
                ->map(fn($e) => ['id' => $e->id, 'nom' => $e->nom, 'statut' => $e->statut])
                ->values()->toArray();
            return [
                'salle_nom'   => $salle ? $salle->nom : ($sid ? 'Salle #' . $sid : 'Salle serveurs'),
                'equipements' => $equips,
            ];
        };

        $result = [];

        // Lit le fichier relay si récent (Arduino connecté via USB)
        if (file_exists($liveFile) && (time() - filemtime($liveFile)) <= $seuilAge) {
            $d   = json_decode(file_get_contents($liveFile), true) ?? [];
            $sid = $d['salle_id'] ?? null;
            $key = (string) ($sid ?? '0');
            $result[$key] = array_merge([
                'salle_id'    => $sid,
                'temperature' => (float) ($d['temperature'] ?? 0),
                'humidite'    => (float) ($d['humidite']    ?? 0),
                'gaz'         => (int)   ($d['gaz']         ?? 0),
                'pir'         => (bool)  ($d['pir']         ?? false),
                'ts'          => $d['ts'] ?? now()->toDateTimeString(),
                'source'      => 'live',
            ], $enrichir($sid));
        }

        // Si Arduino déconnecté → objet vide → dashboard affiche "déconnecté"
        return response()->json($result ?: (object) []);
    } catch (\Exception $e) {
        return response()->json((object) [], 500);
    }
});


// ── GET /api/alertes-mails — alertes paginées avec stats ─────────────────────
Route::get('/alertes-mails', function (Request $request) {
    $niveau  = $request->niveau   ?? '';
    $debut   = $request->debut    ?? '';
    $fin     = $request->fin      ?? '';
    $page    = max(1, (int) ($request->page     ?? 1));
    $parPage = min(100, (int) ($request->par_page ?? 20));

    $q = DB::table('alertes')->where('niveau', 'critique');
    if ($niveau) $q->where('niveau', $niveau);
    if ($debut)  $q->where('created_at', '>=', $debut . ' 00:00:00');
    if ($fin)    $q->where('created_at', '<=', $fin   . ' 23:59:59');

    $total = $q->count();
    $rows  = (clone $q)->orderByDesc('created_at')->skip(($page - 1) * $parPage)->take($parPage)->get();
    $today = date('Y-m-d');

    $stats = [
        'total'    => DB::table('alertes')->where('niveau', 'critique')->count(),
        'critique' => DB::table('alertes')->where('niveau', 'critique')->count(),
        'warning'  => DB::table('alertes')->where('niveau', 'warning')->count(),
        'today'    => DB::table('alertes')->where('niveau', 'critique')->whereDate('created_at', $today)->count(),
    ];

    return response()->json(['data' => $rows, 'total' => $total, 'stats' => $stats]);
});

// ── GET /api/mesures-recentes — dernières N mesures pour les graphiques ───────
Route::get('/mesures-recentes', function (Request $request) {
    $n       = min((int) ($request->n ?? 30), 100);
    $mesures = DB::table('mesures')->latest()->limit($n)->get()->reverse()->values();
    return response()->json($mesures->map(function ($m) {
        return [
            'temperature' => (float) $m->temperature,
            'humidite'    => (float) $m->humidite,
            'gaz'         => (int)   $m->gaz,
            'pir'         => (bool)  ($m->pir_detecte ?? false),
            'ts'          => $m->created_at,
        ];
    }));
});


// ── GET /api/dashboard-data — dernière mesure pour le dashboard ──────────────
Route::get('/dashboard-data', function () {
    $mesure = DB::table('mesures')->latest()->first();
    if (!$mesure) {
        return response()->json(['temperature' => 0, 'humidite' => 0, 'gaz' => 0, 'pir' => false]);
    }
    $data        = (array) $mesure;
    $data['pir'] = (bool) ($mesure->pir_detecte ?? false);
    return response()->json($data);
});


// ── GET /api/stats — compteurs globaux ───────────────────────────────────────
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


// ── GET /api/seuils — seuils actifs en JSON ───────────────────────────────────
Route::get('/seuils', function () {
    return response()->json(getSeuilsValeurs());
});


// ── GET /api/historique-data — historique filtré mesures ou alertes ───────────
Route::get('/historique-data', function (Request $request) {
    $type    = $request->type    ?? 'mesures';
    $debut   = $request->debut   ?? now()->subDays(7)->toDateString();
    $fin     = $request->fin     ?? now()->toDateString();
    $limit   = min((int) ($request->limit  ?? 100), 2000);
    $niveau  = $request->niveau  ?? '';
    $salleId = $request->salle_id ?? '';
    $tMin    = $request->temp_min !== null && $request->temp_min !== '' ? (float) $request->temp_min : null;
    $tMax    = $request->temp_max !== null && $request->temp_max !== '' ? (float) $request->temp_max : null;
    $hMin    = $request->hum_min  !== null && $request->hum_min  !== '' ? (float) $request->hum_min  : null;
    $hMax    = $request->hum_max  !== null && $request->hum_max  !== '' ? (float) $request->hum_max  : null;
    $gMin    = $request->gaz_min  !== null && $request->gaz_min  !== '' ? (float) $request->gaz_min  : null;
    $gMax    = $request->gaz_max  !== null && $request->gaz_max  !== '' ? (float) $request->gaz_max  : null;

    try {
        if ($type === 'alertes') {
            $q = DB::table('alertes')
                ->whereBetween('created_at', [$debut . ' 00:00:00', $fin . ' 23:59:59'])
                ->orderByDesc('created_at');
            if ($niveau)  $q->where('niveau', $niveau);
            if ($salleId) $q->where('salle_id', (int) $salleId);
            return response()->json($q->limit($limit)->get());
        }

        $q = DB::table('mesures')
            ->whereBetween('created_at', [$debut . ' 00:00:00', $fin . ' 23:59:59'])
            ->orderByDesc('created_at');
        if ($salleId) $q->where('salle_id', (int) $salleId);
        if ($tMin !== null) $q->where('temperature', '>=', $tMin);
        if ($tMax !== null) $q->where('temperature', '<=', $tMax);
        if ($hMin !== null) $q->where('humidite', '>=', $hMin);
        if ($hMax !== null) $q->where('humidite', '<=', $hMax);
        if ($gMin !== null) $q->where('gaz', '>=', $gMin);
        if ($gMax !== null) $q->where('gaz', '<=', $gMax);
        return response()->json($q->limit($limit)->get());
    } catch (\Exception $e) {
        return response()->json([]);
    }
});


// ── GET /api/mesures-horaires — moyennes par heure du jour ───────────────────
Route::get('/mesures-horaires', function () {
    try {
        $today = now()->toDateString();
        $rows  = DB::table('mesures')
            ->selectRaw('HOUR(created_at) as heure,
                         ROUND(AVG(temperature),1) as temperature,
                         ROUND(AVG(humidite),1)    as humidite,
                         ROUND(AVG(gaz),1)         as gaz,
                         COUNT(*) as nb')
            ->whereDate('created_at', $today)
            ->groupByRaw('HOUR(created_at)')
            ->orderBy('heure')
            ->get();
        return response()->json($rows);
    } catch (\Exception $e) {
        return response()->json([]);
    }
});


// ── GET /api/alertes-recentes — dernières alertes ────────────────────────────
Route::get('/alertes-recentes', function (Request $request) {
    $limit = min((int) ($request->limit ?? 30), 500);
    try {
        return response()->json(DB::table('alertes')->latest()->limit($limit)->get());
    } catch (\Exception $e) {
        return response()->json([]);
    }
});


// ── POST /api/alertes/lire — marquer alertes comme lues ─────────────────────
Route::post('/alertes/lire', function (Request $request) {
    $id = $request->id;
    if ($id === 'all') {
        DB::table('alertes')->update(['resolu' => 1]);
    } else {
        DB::table('alertes')->where('id', $id)->update(['resolu' => 1]);
    }
    return response()->json(['success' => true]);
});


// ── GET /api/salles-list — liste légère pour les dropdowns ───────────────────
Route::get('/salles-list', function () {
    try {
        return response()->json(DB::table('salles')->select('id', 'nom')->orderBy('nom')->get());
    } catch (\Exception $e) {
        return response()->json([]);
    }
});


// ── GET /api/filter — filtrage avancé multi-types ────────────────────────────
Route::get('/filter', function (Request $request) {
    $allowed = ['mesures', 'alertes', 'salles', 'serveurs'];
    $type    = in_array($request->type, $allowed) ? $request->type : 'mesures';
    $debut   = $request->debut    ?? now()->subDays(7)->toDateString();
    $fin     = $request->fin      ?? now()->toDateString();
    $limit   = min((int) ($request->limit ?? 500), 10000);
    $niveau  = $request->niveau   ?? '';
    $salleId = $request->salle_id ?? '';
    $tMin    = $request->temp_min !== null && $request->temp_min !== '' ? (float) $request->temp_min : null;
    $tMax    = $request->temp_max !== null && $request->temp_max !== '' ? (float) $request->temp_max : null;
    $hMin    = $request->hum_min  !== null && $request->hum_min  !== '' ? (float) $request->hum_min  : null;
    $hMax    = $request->hum_max  !== null && $request->hum_max  !== '' ? (float) $request->hum_max  : null;
    $gMin    = $request->gaz_min  !== null && $request->gaz_min  !== '' ? (float) $request->gaz_min  : null;
    $gMax    = $request->gaz_max  !== null && $request->gaz_max  !== '' ? (float) $request->gaz_max  : null;

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
                ->whereBetween('created_at', [$debut . ' 00:00:00', $fin . ' 23:59:59'])
                ->orderByDesc('created_at');
            if ($niveau)  $q->where('niveau', $niveau);
            if ($salleId) $q->where('salle_id', (int) $salleId);
            $total = $q->count();
            $data  = $q->limit($limit)->get();
            return response()->json(['data' => $data, 'total' => $total]);
        }
        // Type mesures
        $q = DB::table('mesures')
            ->whereBetween('created_at', [$debut . ' 00:00:00', $fin . ' 23:59:59'])
            ->orderByDesc('created_at');
        if ($salleId) $q->where('salle_id', (int) $salleId);
        if ($tMin !== null) $q->where('temperature', '>=', $tMin);
        if ($tMax !== null) $q->where('temperature', '<=', $tMax);
        if ($hMin !== null) $q->where('humidite', '>=', $hMin);
        if ($hMax !== null) $q->where('humidite', '<=', $hMax);
        if ($gMin !== null) $q->where('gaz', '>=', $gMin);
        if ($gMax !== null) $q->where('gaz', '<=', $gMax);
        $total = $q->count();
        $data  = $q->limit($limit)->get();
        return response()->json(['data' => $data, 'total' => $total]);
    } catch (\Exception $e) {
        return response()->json(['data' => [], 'total' => 0, 'error' => $e->getMessage()]);
    }
});


// ── GET /api/seuils-arduino — CSV pour l'Arduino ─────────────────────────────
// Format : tw,tc,hw,hc,gw,gc,pir
Route::get('/seuils-arduino', function () {
    $s   = getSeuilsValeurs();
    $csv = implode(',', [
        (int) ($s['temperature']['warning']  ?? 28),
        (int) ($s['temperature']['critique'] ?? 32),
        (int) ($s['humidite']['warning']     ?? 75),
        (int) ($s['humidite']['critique']    ?? 85),
        (int) ($s['gaz']['warning']          ?? 400),
        (int) ($s['gaz']['critique']         ?? 600),
        ($s['pir']['actif'] ?? 1) ? 1 : 0,
    ]);
    return response($csv, 200)->header('Content-Type', 'text/plain');
});


// ── GET /api/phones — numéros pour les SMS ───────────────────────────────────
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
                $tel = trim($u->indicatif_tel ?? '') . preg_replace('/\D/', '', $tel);
            }
            if (strlen(preg_replace('/\D/', '', $tel)) >= 7) {
                $phones[] = $tel;
            }
        }
    } catch (\Exception $e) {}

    if (!in_array('+237692543407', $phones)) {
        $phones[] = '+237692543407';
    }

    return response(implode(',', array_unique($phones)), 200)
        ->header('Content-Type', 'text/plain');
});


// ── DELETE /api/alerte/{id} — supprime une alerte ────────────────────────────
Route::delete('/alerte/{id}', function (int $id) {
    DB::table('alertes')->where('id', $id)->delete();
    return response()->json(['success' => true]);
});
