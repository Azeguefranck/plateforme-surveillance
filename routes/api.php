<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\GeoController;


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

    $cache = [
        'temperature' => ['warning' => 28,   'critique' => 32],
        'humidite'    => ['warning' => 75,   'critique' => 85],
        'gaz'         => ['warning' => 400,  'critique' => 600],
        'pir'         => ['actif' => 1],
    ];
    return $cache;
}
endif;

if (!function_exists('analyserMesures')) :
function analyserMesures(array $valeurs, array $presents = []): array
{
    $alertes = [];
    $seuils  = getSeuilsValeurs();
    $meta    = getSeuilsMeta();

    foreach ($meta as $capteur => $m) {
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

    // Icônes Font Awesome SVG inline (compatibles tous clients email : Gmail, Apple Mail, Outlook 2019+)
    $svgWarn  = '<svg style="display:inline-block;vertical-align:middle" width="18" height="18" viewBox="0 0 512 512"><path fill="' . $couleur . '" d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7.2 40.1S486.3 480 472 480H40c-14.3 0-27.6-7.5-34.7-19.8s-7-27.8.2-40.1l216-368C228.7 39.5 241.8 32 256 32zm0 128c-13.3 0-24 10.7-24 24V296c0 13.3 10.7 24 24 24s24-10.7 24-24V184c0-13.3-10.7-24-24-24zm32 224a32 32 0 1 0-64 0 32 32 0 1 0 64 0z"/></svg>';
    $svgXmark  = '<svg style="display:inline-block;vertical-align:middle" width="18" height="18" viewBox="0 0 512 512"><path fill="' . $couleur . '" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/></svg>';
    $svgH      = $critique ? $svgXmark : $svgWarn;
    $svgCheck  = '<svg style="display:inline-block;vertical-align:middle" width="14" height="14" viewBox="0 0 512 512"><path fill="#33ff88" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>';
    $svgTemp   = '<svg style="display:inline-block;vertical-align:middle" width="11" height="14" viewBox="0 0 320 512"><path fill="#8899cc" d="M160 64c0-17.7-14.3-32-32-32s-32 14.3-32 32V264.6C49 284.8 32 309.6 32 336c0 53 43 96 96 96s96-43 96-96c0-26.4-17-51.2-64-71.4V64zm-32 304a32 32 0 1 1 0-64 32 32 0 1 1 0 64z"/></svg>';
    $svgDrop   = '<svg style="display:inline-block;vertical-align:middle" width="11" height="14" viewBox="0 0 384 512"><path fill="#8899cc" d="M192 512C86 512 0 426 0 320C0 228.8 130.2 57.7 166.6 11.7C172.6 4.2 181.5 0 191 0h1.4c9.5 0 18.4 4.2 24.4 11.7C253.8 57.7 384 228.8 384 320c0 106-86 192-192 192z"/></svg>';
    $svgWind   = '<svg style="display:inline-block;vertical-align:middle" width="15" height="12" viewBox="0 0 512 512"><path fill="#8899cc" d="M288 32c0 17.7 14.3 32 32 32h32c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32 14.3-32 32s14.3 32 32 32H352c53 0 96-43 96-96s-43-96-96-96H320c-17.7 0-32 14.3-32 32zm64 352c0 17.7 14.3 32 32 32h32c53 0 96-43 96-96s-43-96-96-96H32c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H384c-17.7 0-32 14.3-32 32zM128 512h32c53 0 96-43 96-96s-43-96-96-96H32c-17.7 0-32 14.3-32 32s14.3 32 32 32H160c17.7 0 32 14.3 32 32s-14.3 32-32 32H128c-17.7 0-32 14.3-32 32s14.3 32 32 32z"/></svg>';
    $svgPIR    = '<svg style="display:inline-block;vertical-align:middle" width="11" height="14" viewBox="0 0 320 512"><path fill="#8899cc" d="M160 48a48 48 0 1 1 96 0 48 48 0 1 1-96 0zM126.5 199.3c-1 .4-1.9 .8-2.9 1.2l-8 3.5c-16.4 7.3-29 21.2-34.7 38.2l-2.6 7.8c-5.6 16.8-23.7 25.8-40.5 20.2s-25.8-23.7-20.2-40.5l2.6-7.8c11.4-34.1 36.6-61.9 69.4-76.5l8-3.5c20.8-9.2 43.3-14 66.1-14c44.6 0 84.8 26.8 101.9 67.9L281 232.7l21.4 10.7c15.8 7.9 22.2 27.1 14.3 42.9s-27.1 22.2-42.9 14.3L247 287.3c-10.3-5.2-18.4-13.8-22.8-24.5l-9.6-23-19.3 65.5 49.5 54c5.4 5.9 9.2 13 11.1 20.8l23 92.1c4.3 17.1-6.1 34.5-23.3 38.8s-34.5-6.1-38.8-23.3l-22-88.1-70.7-77.1c-14.8-16.1-20.3-38.6-14.7-59.7l16.9-63.5zM68.7 398l25-62.4c2.1 3 4.5 5.8 7 8.6l40.7 44.4-14.5 36.2c-2.4 6-6 11.5-10.6 16.1L54.6 502.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L68.7 398z"/></svg>';
    $svgGauge  = '<svg style="display:inline-block;vertical-align:middle" width="15" height="15" viewBox="0 0 512 512"><path fill="' . ($critique ? '#fff' : '#000') . '" d="M0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm320 96c0-26.9-16.5-49.9-40-59.3V88c0-13.3-10.7-24-24-24s-24 10.7-24 24V292.7c-23.5 9.5-40 32.5-40 59.3c0 35.3 28.7 64 64 64s64-28.7 64-64zM144 176a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm-16 80a32 32 0 1 0-64 0 32 32 0 1 0 64 0zm288 32a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM400 144a32 32 0 1 0-64 0 32 32 0 1 0 64 0z"/></svg>';
    $svgChart  = '<svg style="display:inline-block;vertical-align:middle" width="14" height="14" viewBox="0 0 512 512"><path fill="#33b5ff" d="M32 32c17.7 0 32 14.3 32 32V400c0 8.8 7.2 16 16 16H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H80c-44.2 0-80-35.8-80-80V64C0 46.3 14.3 32 32 32zm96 96c0-17.7 14.3-32 32-32l192 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-192 0c-17.7 0-32-14.3-32-32zm32 64H288c17.7 0 32 14.3 32 32s-14.3 32-32 32H160c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 96H352c17.7 0 32 14.3 32 32s-14.3 32-32 32H160c-17.7 0-32-14.3-32-32s14.3-32 32-32z"/></svg>';
    $svgSrv    = '<svg style="display:inline-block;vertical-align:middle" width="14" height="12" viewBox="0 0 512 512"><path fill="#8899cc" d="M64 0C28.7 0 0 28.7 0 64V224c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H64zM448 160c17.7 0 32 14.3 32 32s-14.3 32-32 32-32-14.3-32-32 14.3-32 32-32zm32 160c0-35.3-28.7-64-64-64H64c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V320zM448 416c17.7 0 32 14.3 32 32s-14.3 32-32 32-32-14.3-32-32 14.3-32 32-32z"/></svg>';
    $svgClock  = '<svg style="display:inline-block;vertical-align:middle" width="13" height="13" viewBox="0 0 512 512"><path fill="#8899cc" d="M256 0a256 256 0 1 1 0 512A256 256 0 1 1 256 0zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.5 33.3-6.5s4.5-25.9-6.5-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/></svg>';
    $svgDot    = function(string $c): string { return '<svg style="display:inline-block;vertical-align:middle" width="9" height="9" viewBox="0 0 512 512"><path fill="' . $c . '" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512z"/></svg>'; };

    $ctaBg  = $couleur;
    $ctaFg  = $critique ? '#fff' : '#000';

    $html =
        '<!DOCTYPE html><html lang="fr"><head>'
        . '<meta charset="UTF-8">'
        . '<meta name="viewport" content="width=device-width,initial-scale=1.0">'
        . '<meta name="x-apple-disable-message-reformatting">'
        . '<style>'
        . 'body{margin:0;padding:0;background:#060d1f;font-family:Arial,sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}'
        . '.ow{width:100%;background:#060d1f}'
        . '.iw{max-width:560px;margin:0 auto;background:#060d1f}'
        . '.hd{background:linear-gradient(135deg,#0e0a00,#0a1428);padding:22px 18px;text-align:center;border-bottom:3px solid ' . $couleur . '}'
        . '.ht{color:' . $couleur . ';font-size:16px;font-weight:900;letter-spacing:.5px;margin:0;word-break:break-word}'
        . '.hs{color:#5a6a99;font-size:10px;margin-top:5px;letter-spacing:1px;text-transform:uppercase;word-break:break-word}'
        . '.bd{background:#0a1428;padding:16px 18px}'
        . '.badge{display:inline-block;background:rgba(' . $couleurRgb . ',.12);color:' . $couleur . ';border:1px solid ' . $couleur . '55;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:.8px;margin-bottom:12px}'
        . 'table.dt{width:100%;border-collapse:collapse;margin:8px 0;table-layout:fixed}'
        . 'table.dt td{padding:8px 9px;font-size:13px;border-bottom:1px solid #0e1c35;word-break:break-word;overflow-wrap:break-word}'
        . '.k{color:#8899cc;width:40%;font-weight:600;vertical-align:top}'
        . '.v{color:#c7d2ff}'
        . '.sec{background:#060f28;border:1px solid #1e2f5a;border-radius:8px;padding:12px 13px;margin:12px 0}'
        . '.sec-t{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px}'
        . '.blk{border-left:3px solid ' . $couleur . ';border-radius:4px;padding:10px 12px;margin:10px 0;background:#0e1428}'
        . '.blk-g{border-left:3px solid #33ff88;border-radius:4px;padding:10px 12px;margin:10px 0;background:#071428}'
        . '.blk-t{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:5px}'
        . '.blk-c{color:#c7d2ff;font-size:13px;line-height:1.5}'
        . '.cta{display:block;margin:14px auto 0;padding:11px 22px;background:' . $ctaBg . ';color:' . $ctaFg . ';font-weight:700;font-size:13px;text-decoration:none;border-radius:8px;text-align:center;max-width:220px}'
        . '.ft{background:#060d1f;padding:12px 18px;text-align:center;color:#3a4a6a;font-size:11px;border-top:1px solid #0e1c35;word-break:break-word}'
        . '@media only screen and (max-width:600px){'
        . '.iw{width:100%!important}'
        . '.hd,.bd{padding:14px!important}'
        . 'table.dt td{font-size:12px!important;padding:7px 8px!important}'
        . '.ht{font-size:14px!important}'
        . '}'
        . '</style></head><body>'
        . '<table class="ow" cellpadding="0" cellspacing="0" role="presentation"><tr><td align="center">'
        . '<table class="iw" cellpadding="0" cellspacing="0" role="presentation"><tr><td>'

        . '<div class="hd">'
        . '<div style="font-size:0;margin-bottom:8px">' . $svgH . '</div>'
        . '<h1 class="ht">SYST&Egrave;ME DE SURVEILLANCE &mdash; ALERTE ' . $niveauLabel . '</h1>'
        . '<div class="hs">Plateforme de Surveillance &mdash; Param&egrave;tres des &Eacute;quipements</div>'
        . '</div>'

        . '<div class="bd">'
        . '<div><span class="badge">' . $svgWarn . '&thinsp;CAPTEUR&nbsp;: ' . $capteurNom . '</span></div>'

        . '<table class="dt">'
        . '<tr><td class="k">Capteur</td><td class="v">' . $capteurNom . '</td></tr>'
        . '<tr><td class="k">Valeur mesur&eacute;e</td><td class="v" style="color:' . $couleur . ';font-weight:700;font-size:14px">' . htmlspecialchars($alerte['valeur']) . '&thinsp;' . htmlspecialchars($alerte['unite']) . '</td></tr>'
        . '<tr><td class="k">Seuil d&eacute;pass&eacute;</td><td class="v">' . htmlspecialchars($alerte['seuil']) . '&thinsp;' . htmlspecialchars($alerte['unite']) . '</td></tr>'
        . '<tr><td class="k">Niveau</td><td class="v" style="color:' . $couleur . ';font-weight:700">' . $svgDot($couleur) . '&nbsp;' . $niveauLabel . '</td></tr>'
        . '<tr><td class="k">' . $svgClock . '&nbsp;Date&nbsp;/&nbsp;Heure</td><td class="v">' . htmlspecialchars($horodatage) . '</td></tr>'
        . ($salleNom ? '<tr><td class="k">Salle</td><td class="v" style="color:#33b5ff;font-weight:700">' . htmlspecialchars($salleNom) . '</td></tr>' : '')
        . (count($equipNoms) > 0 ? '<tr><td class="k" style="vertical-align:top">' . $svgSrv . '&nbsp;&Eacute;quipements</td><td class="v">' . implode('<br>', array_map('htmlspecialchars', $equipNoms)) . '</td></tr>' : '')
        . '</table>'

        . (empty($mesures) ? '' :
              '<div class="sec">'
            . '<div class="sec-t" style="color:#33b5ff">' . $svgChart . '&nbsp;Mesures au moment de l\'alerte</div>'
            . '<table class="dt" style="margin:0">'
            . '<tr><td class="k" style="width:48%">' . $svgTemp . '&nbsp;Temp&eacute;rature</td><td class="v" style="' . $valCouleur('temperature', $mesures['temperature'] ?? 0) . '">' . htmlspecialchars($mesures['temperature'] ?? '—') . '&thinsp;&deg;C</td></tr>'
            . '<tr><td class="k">' . $svgDrop . '&nbsp;Humidit&eacute;</td><td class="v" style="' . $valCouleur('humidite', $mesures['humidite'] ?? 0) . '">' . htmlspecialchars($mesures['humidite'] ?? '—') . '&thinsp;%</td></tr>'
            . '<tr><td class="k">' . $svgWind . '&nbsp;Gaz&nbsp;/&nbsp;Air</td><td class="v" style="' . $valCouleur('gaz', $mesures['gaz'] ?? 0) . '">' . htmlspecialchars($mesures['gaz'] ?? '—') . '&thinsp;ppm</td></tr>'
            . '<tr style="border-bottom:none"><td class="k" style="border-bottom:none">' . $svgPIR . '&nbsp;D&eacute;tecteur PIR</td><td class="v" style="border-bottom:none;' . (!empty($mesures['pir']) ? 'color:#ff5733;font-weight:700' : 'color:#33ff88') . '">' . $svgDot(!empty($mesures['pir']) ? '#ff5733' : '#33ff88') . '&nbsp;' . (!empty($mesures['pir']) ? 'Mouvement d&eacute;tect&eacute;' : 'Aucun mouvement') . '</td></tr>'
            . '</table></div>'
        )

        . '<div class="blk">'
        . '<div class="blk-t" style="color:' . $couleur . '">' . $svgWarn . '&nbsp;Risques identifi&eacute;s</div>'
        . '<div class="blk-c">' . htmlspecialchars($alerte['risque']) . '</div>'
        . '</div>'

        . '<div class="blk-g">'
        . '<div class="blk-t" style="color:#33ff88">' . $svgCheck . '&nbsp;Actions &agrave; entreprendre</div>'
        . '<div class="blk-c">' . htmlspecialchars($alerte['solution']) . '</div>'
        . '</div>'

        . '<a class="cta" href="' . htmlspecialchars($platformUrl) . '">' . $svgGauge . '&nbsp;Voir le tableau de bord</a>'
        . '</div>'

        . '<div class="ft">Syst&egrave;me de Surveillance &mdash; Alerte automatique &mdash; Ne pas r&eacute;pondre &agrave; cet email</div>'

        . '</td></tr></table>'
        . '</td></tr></table>'
        . '</body></html>';

    $emailTo   = $adminUser->email;
    $sujetMail = $sujet;

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

if (!function_exists('envoyerSMS')) :
function envoyerSMS(array $phones, string $msg): void
{
    if (empty($phones)) return;
    $msg = mb_substr($msg, 0, 160);

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
    $msg = 'PS ' . $niv . ': ' . strtoupper($alerte['capteur'])
         . '=' . $alerte['valeur'] . $alerte['unite']
         . ' Seuil=' . $alerte['seuil'] . $alerte['unite']
         . $equip . ' ' . $horodatage;
    envoyerSMS(collecterPhonesUtilisateurs(), $msg);
}
endif;

if (!function_exists('envoyerSMSMouvement')) :
function envoyerSMSMouvement(string $horodatage): void
{
    $msg = 'PS ALERTE SECURITE: Mouvement detecte dans la salle serveurs! ' . $horodatage;
    envoyerSMS(collecterPhonesUtilisateurs(), $msg);
}
endif;

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
        . '<div class="f">Plateforme Surveillance &mdash; Alerte automatique</div>'
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

Route::post('/capteurs', function (Request $request) {

    $temperature  = (float) ($request->temperature   ?? 0);
    $humidite     = (float) ($request->humidite      ?? 0);
    $gaz          = (float) ($request->gaz           ?? 0);
    $pir     = (bool) ($request->pir      ?? false);
    $salleId = $request->salle_id ? (int) $request->salle_id : null;

    $capteursPresents = array_keys(array_filter([
        'temperature' => $request->has('temperature') || $request->temperature !== null,
        'humidite'    => $request->has('humidite')    || $request->humidite    !== null,
        'gaz'         => $request->has('gaz')         || $request->gaz         !== null,
    ]));

    DB::table('mesures')->insert([
        'temperature'   => $temperature,
        'humidite'      => $humidite,
        'gaz'           => $gaz,
        'pir_detecte' => $pir ? 1 : 0,
        'salle_id'    => $salleId,
        'created_at'  => now(),
        'updated_at'    => now(),
    ]);

    $horodatage = now()->format('d/m/Y H:i:s');
    $mesValeurs = compact('temperature', 'humidite', 'gaz');
    $alertes    = analyserMesures($mesValeurs, $capteursPresents);
    $seuils     = getSeuilsValeurs();

    $prevState = chargerEtatAlertes();
    $newState  = $prevState;
    $alertMap  = [];
    foreach ($alertes as $a) { $alertMap[$a['capteur']] = $a; }

    $nbAlertes      = 0;
    $smsPending     = [];
    $poids          = ['normal' => 0, 'warning' => 1, 'critique' => 2];
    $EMAIL_COOLDOWN = 720;

    foreach (['temperature', 'humidite', 'gaz'] as $cap) {
        $newNiveau = isset($alertMap[$cap]) ? $alertMap[$cap]['niveau'] : 'normal';
        $oldNiveau = $prevState[$cap] ?? 'normal';
        $newState[$cap] = $newNiveau;

        if ($newNiveau === 'normal') continue;

        $isEscalade    = ($poids[$newNiveau] ?? 0) > ($poids[$oldNiveau] ?? 0);
        $lastEmail     = $prevState['email_last'][$cap] ?? 0;
        $cooldownPasse = (time() - $lastEmail) >= $EMAIL_COOLDOWN;

        if (!$isEscalade && !$cooldownPasse) continue;

        if ($isEscalade) {
            DB::table('alertes')->insert([
                'type'          => $alertMap[$cap]['capteur'],
                'message'       => 'Dépassement seuil ' . $newNiveau . ' — ' . $cap
                                 . ' : ' . $alertMap[$cap]['valeur'] . $alertMap[$cap]['unite'],
                'niveau'        => $newNiveau,
                'valeur'        => $alertMap[$cap]['valeur'] . $alertMap[$cap]['unite'],
                'salle_id' => $salleId,
                'resolu'   => 0,
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
            'PS ' . ($newNiveau === 'critique' ? 'CRITIQUE' : 'WARNING')
            . ': ' . strtoupper($cap)
            . '=' . $alertMap[$cap]['valeur'] . $alertMap[$cap]['unite']
            . ' Seuil=' . $alertMap[$cap]['seuil'] . $alertMap[$cap]['unite']
            . ' ' . $horodatage,
        ];
    }

    $PIR_EMAIL_COOLDOWN = 600;
    if ($pir && ($seuils['pir']['actif'] ?? 1)) {
        $lastPirEmail = $prevState['pir_last'] ?? 0;
        if (time() - $lastPirEmail >= $PIR_EMAIL_COOLDOWN) {
            DB::table('alertes')->insert([
                'type'       => 'pir',
                'message'    => 'Mouvement détecté dans la salle serveurs',
                'niveau'     => 'critique',
                'valeur'     => 'Détecté',
                'salle_id'   => $salleId,
                'resolu'     => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            envoyerEmailMouvement($horodatage, $salleId);
            $smsPending[] = ['msg' => 'PS ALERTE SECURITE: Mouvement detecte ! ' . $horodatage];
            $newState['pir_last'] = time();
            $nbAlertes++;
        }
    }

    sauvegarderEtatAlertes($newState);

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


Route::get('/live-data', function () {
    $file     = '/tmp/latest_sensor.json';
    $seuilAge = 6;

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


Route::get('/mesures-live', function () {
    $seuilAge = 6;
    $liveFile = '/tmp/latest_sensor.json';

    try {
        $salles   = DB::table('salles')->get()->keyBy('id');
        $serveurs = DB::table('serveurs')->get()->groupBy('salle_id');

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

        return response()->json($result ?: (object) []);
    } catch (\Exception $e) {
        return response()->json((object) [], 500);
    }
});


Route::get('/alertes-mails', function (Request $request) {
    $niveau  = $request->niveau   ?? '';
    $debut   = $request->debut    ?? '';
    $fin     = $request->fin      ?? '';
    $page    = max(1, (int) ($request->page     ?? 1));
    $parPage = min(100, (int) ($request->par_page ?? 20));

    $q = DB::table('alertes');
    if ($niveau) $q->where('niveau', $niveau);
    if ($debut)  $q->where('created_at', '>=', $debut . ' 00:00:00');
    if ($fin)    $q->where('created_at', '<=', $fin   . ' 23:59:59');

    $total = $q->count();
    $rows  = (clone $q)->orderByDesc('created_at')->skip(($page - 1) * $parPage)->take($parPage)->get();
    $today = date('Y-m-d');

    $stats = [
        'total'    => DB::table('alertes')->count(),
        'critique' => DB::table('alertes')->where('niveau', 'critique')->count(),
        'warning'  => DB::table('alertes')->where('niveau', 'warning')->count(),
        'today'    => DB::table('alertes')->whereDate('created_at', $today)->count(),
    ];

    return response()->json(['data' => $rows, 'total' => $total, 'stats' => $stats]);
});

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


Route::get('/dashboard-data', function () {
    $mesure = DB::table('mesures')->latest()->first();
    if (!$mesure) {
        return response()->json(['temperature' => 0, 'humidite' => 0, 'gaz' => 0, 'pir' => false]);
    }
    $data        = (array) $mesure;
    $data['pir'] = (bool) ($mesure->pir_detecte ?? false);
    return response()->json($data);
});


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


Route::get('/seuils', function () {
    return response()->json(getSeuilsValeurs());
});


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


Route::get('/alertes-recentes', function (Request $request) {
    $limit = min((int) ($request->limit ?? 30), 500);
    try {
        return response()->json(DB::table('alertes')->latest()->limit($limit)->get());
    } catch (\Exception $e) {
        return response()->json([]);
    }
});


Route::post('/alertes/lire', function (Request $request) {
    $id = $request->id;
    if ($id === 'all') {
        DB::table('alertes')->update(['resolu' => 1]);
    } else {
        DB::table('alertes')->where('id', $id)->update(['resolu' => 1]);
    }
    return response()->json(['success' => true]);
});


Route::get('/salles-list', function () {
    try {
        return response()->json(DB::table('salles')->select('id', 'nom')->orderBy('nom')->get());
    } catch (\Exception $e) {
        return response()->json([]);
    }
});


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
        $q = DB::table('mesures')
            ->select(['id','temperature','humidite','gaz','pir_detecte','salle_id','created_at'])
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


Route::delete('/alerte/{id}', function (int $id) {
    if (!session('user')) return response()->json(['error' => 'Non autorisé'], 401);
    DB::table('alertes')->where('id', $id)->delete();
    return response()->json(['success' => true]);
});
