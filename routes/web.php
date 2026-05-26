<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServeursController;
use App\Http\Controllers\SallesController;

// ═══════════════════════════════════════════════════════════
//  HELPERS
// ═══════════════════════════════════════════════════════════

// Vérifie si l'utilisateur est connecté
function estConnecte(): bool {
    return session()->has('user') && session('user') !== null;
}

// Retourne l'utilisateur de session toujours en stdClass (array ou objet)
function getSessionUser(): ?\stdClass {
    $raw = session('user');
    if (!$raw) return null;
    return (object)(array)$raw;
}

// ═══════════════════════════════════════════════════════════
//  PAGES PUBLIQUES
//  Si déjà connecté → redirection automatique vers /dashboard
// ═══════════════════════════════════════════════════════════

Route::get('/', function () {
    if (estConnecte()) return redirect('/dashboard');
    return view('accueil');
});

Route::get('/accueil', function () {
    return view('accueil');
});

Route::get('/login', function () {
    if (estConnecte()) return redirect('/dashboard');
    return view('login');
});

Route::get('/register', function () {
    return redirect('/login');
});

// ═══════════════════════════════════════════════════════════
//  ACTIONS D'AUTHENTIFICATION
// ═══════════════════════════════════════════════════════════

Route::post('/register-user', function () { return redirect('/login'); });
Route::post('/login-user',    [AuthController::class, 'login']);
Route::get('/logout',         [AuthController::class, 'logout']);

// ═══════════════════════════════════════════════════════════
//  PAGES PROTÉGÉES
//  Si non connecté → redirection automatique vers /login
// ═══════════════════════════════════════════════════════════

Route::get('/dashboard', function () {
    if (!estConnecte()) return redirect('/login');
    return view('dashboard');
});

Route::get('/alertes', function () {
    if (!estConnecte()) return redirect('/login');
    return view('alertes');
});

Route::get('/historique', function () {
    if (!estConnecte()) return redirect('/login');
    return view('historique');
});

Route::get('/statistiques', function () {
    if (!estConnecte()) return redirect('/login');
    return view('statistiques');
});

Route::get('/sms', function () {
    if (!estConnecte()) return redirect('/login');
    return view('sms');
});

Route::get('/sms-gsm', function () {
    if (!estConnecte()) return redirect('/login');
    return view('sms-gsm');
});

Route::get('/anomalies', function () {
    if (!estConnecte()) return redirect('/login');
    return view('anomalies');
});

Route::get('/profil', function () {
    if (!estConnecte()) return redirect('/login');

    // Récupérer l'ID depuis la session (fonctionne que ce soit un objet ou un array)
    $sessionUser = session('user');
    $userId = is_array($sessionUser) ? ($sessionUser['id'] ?? null) : ($sessionUser->id ?? null);

    if (!$userId) return redirect('/login');

    // Données fraîches depuis la base — évite tout problème de désérialisation
    $user = \Illuminate\Support\Facades\DB::table('users')->where('id', $userId)->first();
    if (!$user) return redirect('/login');

    // Rafraîchit la session avec l'objet propre
    session(['user' => $user]);

    $alertes = \Illuminate\Support\Facades\DB::table('alertes')
        ->orderBy('created_at', 'desc')
        ->take(6)
        ->get();

    return view('profil', compact('alertes', 'user'));
});

Route::post('/profil/update', function () {
    if (!estConnecte()) return redirect('/login');
    return app(ProfileController::class)->update(request());
});

Route::post('/profil/password', function () {
    if (!estConnecte()) return redirect('/login');
    return app(ProfileController::class)->changePassword(request());
});

Route::post('/profil/photo', function () {
    if (!estConnecte()) return redirect('/login');
    return app(ProfileController::class)->updatePhoto(request());
});


// Redirections legacy
Route::get('/serveurs-web', function () { return redirect('/serveurs'); });
Route::get('/serveurs-bd',  function () { return redirect('/serveurs'); });

// ── Serveurs ──
Route::get('/serveurs', function () {
    if (!estConnecte()) return redirect('/login');
    return app(ServeursController::class)->index();
});
Route::post('/serveurs/store', function () {
    if (!estConnecte()) return redirect('/login');
    return app(ServeursController::class)->store(request());
});
Route::post('/serveurs/update/{id}', function ($id) {
    if (!estConnecte()) return redirect('/login');
    return app(ServeursController::class)->update(request(), $id);
});
Route::post('/serveurs/delete/{id}', function ($id) {
    if (!estConnecte()) return redirect('/login');
    return app(ServeursController::class)->destroy($id);
});

// ── Salles ──
Route::get('/salles', function () {
    if (!estConnecte()) return redirect('/login');
    return app(SallesController::class)->index();
});
Route::post('/salles/store', function () {
    if (!estConnecte()) return redirect('/login');
    return app(SallesController::class)->store(request());
});
Route::post('/salles/update/{id}', function ($id) {
    if (!estConnecte()) return redirect('/login');
    return app(SallesController::class)->update(request(), $id);
});
Route::post('/salles/delete/{id}', function ($id) {
    if (!estConnecte()) return redirect('/login');
    return app(SallesController::class)->destroy($id);
});

Route::get('/parametres', function () {
    if (!estConnecte()) return redirect('/login');

    \Illuminate\Support\Facades\DB::statement("CREATE TABLE IF NOT EXISTS `settings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `cle` varchar(100) NOT NULL,
        `valeur` varchar(255) NOT NULL,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `cle` (`cle`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $defaults = [
        'seuil_temp_warn' => 30,  'seuil_temp_crit' => 40,
        'seuil_hum_min'   => 30,  'seuil_hum_max'   => 80,
        'seuil_gaz_warn'  => 300, 'seuil_gaz_crit'  => 500,
        'seuil_cur_warn'  => 10,  'seuil_cur_crit'  => 15,
        'seuil_pwr_warn'  => 1500,'seuil_pwr_crit'  => 2000,
    ];

    $settings = [];
    foreach ($defaults as $key => $def) {
        $row = \Illuminate\Support\Facades\DB::table('settings')->where('cle', $key)->first();
        $settings[$key] = $row ? $row->valeur : $def;
    }

    return view('parametres', compact('settings'));
});

Route::post('/parametres/save', function () {
    if (!estConnecte()) return redirect('/login');

    $keys = [
        'seuil_temp_warn','seuil_temp_crit',
        'seuil_hum_min','seuil_hum_max',
        'seuil_gaz_warn','seuil_gaz_crit',
        'seuil_cur_warn','seuil_cur_crit',
        'seuil_pwr_warn','seuil_pwr_crit',
    ];

    foreach ($keys as $key) {
        if (request()->has($key)) {
            \Illuminate\Support\Facades\DB::table('settings')->updateOrInsert(
                ['cle' => $key],
                ['valeur' => request()->input($key), 'updated_at' => now()]
            );
        }
    }

    return back()->with('success', 'Seuils sauvegardés avec succès.');
});

Route::get('/rapports', function () {
    if (!estConnecte()) return redirect('/login');
    $salles = [];
    try { $salles = \Illuminate\Support\Facades\DB::table('salles')->orderBy('nom')->get(); } catch(\Exception $e){}
    return view('rapports', compact('salles'));
});

Route::get('/rapports/export/csv', function () {
    if (!estConnecte()) return redirect('/login');
    $debut = request('debut');
    $fin   = request('fin');
    $q = \Illuminate\Support\Facades\DB::table('mesures')->orderBy('created_at', 'desc');
    if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
    if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');
    $mesures = $q->take(5000)->get();

    $headers = ['ID','Date','Temp_C','Humidite_pct','Gaz_ppm','Courant_A','Puissance_W','Tension_V','PIR'];
    $rows    = $mesures->map(fn($m) => implode(',', [
        $m->id,
        '"' . ($m->created_at ?? '') . '"',
        $m->temperature ?? '',
        $m->humidite    ?? '',
        $m->gaz         ?? '',
        $m->courant     ?? '',
        $m->puissance   ?? '',
        $m->tension     ?? 220,
        ($m->pir_detecte ?? false) ? 'OUI' : 'NON',
    ]));

    $csv      = implode("\n", array_merge([implode(',', $headers)], $rows->toArray()));
    $filename = 'mesures_' . date('Y-m-d') . '.csv';

    return response($csv, 200, [
        'Content-Type'        => 'text/csv; charset=utf-8',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ]);
});

Route::get('/rapports/export/json', function () {
    if (!estConnecte()) return redirect('/login');
    $debut = request('debut');
    $fin   = request('fin');
    $q = \Illuminate\Support\Facades\DB::table('mesures')->orderBy('created_at','desc');
    if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
    if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');
    $mesures  = $q->take(5000)->get();
    $filename = 'mesures_' . date('Y-m-d') . '.json';
    return response(json_encode($mesures, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 200, [
        'Content-Type'        => 'application/json',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ]);
});

Route::get('/rapports/export/alertes', function () {
    if (!estConnecte()) return redirect('/login');
    $alertes  = \Illuminate\Support\Facades\DB::table('alertes')->orderBy('created_at','desc')->take(2000)->get();
    $headers  = ['ID','Date','Type','Niveau','Valeur','Message','Resolu','SMS'];
    $rows     = $alertes->map(fn($a) => implode(',', [
        $a->id,
        '"' . ($a->created_at ?? '') . '"',
        '"' . ($a->type    ?? '') . '"',
        $a->niveau  ?? '',
        '"' . ($a->valeur  ?? '') . '"',
        '"' . str_replace('"','""', $a->message ?? '') . '"',
        ($a->resolu ?? 0) ? 'OUI' : 'NON',
        ($a->envoi_sms ?? 0) ? 'OUI' : 'NON',
    ]));
    $csv = implode("\n", array_merge([implode(',', $headers)], $rows->toArray()));
    return response($csv, 200, [
        'Content-Type'        => 'text/csv; charset=utf-8',
        'Content-Disposition' => 'attachment; filename="alertes_' . date('Y-m-d') . '.csv"',
    ]);
});

// ── Génération de rapport (tous types, tous formats) ──
Route::get('/rapports/generer', function () {
    if (!estConnecte()) return redirect('/login');

    $type   = request('type',   'capteurs');
    $format = strtolower(request('format', 'csv'));
    $debut  = request('debut');
    $fin    = request('fin');
    $niveau = request('niveau');
    $salle  = (int) request('salle', 0);
    $limit  = min((int) request('limit', 5000), 10000);

    $rows  = collect();
    $table = 'mesures';
    $DB    = \Illuminate\Support\Facades\DB::class;

    try {
        if ($type === 'utilisateurs') {
            $rows  = \Illuminate\Support\Facades\DB::table('users')
                ->select('id','nom','prenom','email','role','validation_status','telephone','organisation','adresse','created_at')
                ->latest('id')->take($limit)->get();
            $table = 'users';
        } elseif ($type === 'salles') {
            $rows  = \Illuminate\Support\Facades\DB::table('salles')->latest('id')->take($limit)->get();
            $table = 'salles';
        } elseif ($type === 'serveurs') {
            $rows  = \Illuminate\Support\Facades\DB::table('serveurs')->latest('id')->take($limit)->get();
            $table = 'serveurs';
        } elseif ($type === 'incidents') {
            $q = \Illuminate\Support\Facades\DB::table('alertes')->where('niveau', 'CRITIQUE');
            if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
            if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');
            $rows = $q->latest('id')->take($limit)->get();
            $table = 'alertes';
        } elseif ($type === 'securite') {
            $q = \Illuminate\Support\Facades\DB::table('alertes')->where('type', 'intrusion');
            if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
            if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');
            $rows = $q->latest('id')->take($limit)->get();
            $table = 'alertes';
        } elseif (in_array($type, ['alertes', 'anomalies'])) {
            $q = \Illuminate\Support\Facades\DB::table('alertes');
            if ($debut)  $q->where('created_at', '>=', $debut . ' 00:00:00');
            if ($fin)    $q->where('created_at', '<=', $fin   . ' 23:59:59');
            if ($niveau) $q->where('niveau', strtoupper($niveau));
            $rows = $q->latest('id')->take($limit)->get();
            $table = 'alertes';
        } elseif ($type === 'energie') {
            $q = \Illuminate\Support\Facades\DB::table('mesures')
                ->select('id','created_at','courant','puissance','tension','temperature','humidite');
            if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
            if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');
            if ($salle) $q->where('salle_id', $salle);
            $rows = $q->latest('id')->take($limit)->get();
        } elseif ($type === 'maintenance') {
            $q = \Illuminate\Support\Facades\DB::table('historiques');
            if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
            if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');
            $rows = $q->latest('id')->take($limit)->get();
            $table = 'historiques';
        } else {
            // capteurs, historique, default
            $q = \Illuminate\Support\Facades\DB::table('mesures');
            if ($debut) $q->where('created_at', '>=', $debut . ' 00:00:00');
            if ($fin)   $q->where('created_at', '<=', $fin   . ' 23:59:59');
            if ($salle) $q->where('salle_id', $salle);
            $rows = $q->latest('id')->take($limit)->get();
        }
    } catch (\Exception $e) {
        $rows = collect();
    }

    $date     = date('Y-m-d');
    $filename = "rapport_{$type}_{$date}";
    $rowsArr  = $rows->map(fn($r) => (array) $r)->toArray();

    // CSV
    if ($format === 'csv') {
        $lines = [];
        if (!empty($rowsArr)) {
            $lines[] = implode(',', array_keys($rowsArr[0]));
            foreach ($rowsArr as $r) {
                $lines[] = implode(',', array_map(
                    fn($v) => '"' . str_replace('"', '""', (string)($v ?? '')) . '"',
                    array_values($r)
                ));
            }
        }
        return response("\xEF\xBB\xBF" . implode("\n", $lines), 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ]);
    }

    // JSON
    if ($format === 'json') {
        return response(json_encode([
            'type'      => $type,
            'generated' => date('Y-m-d H:i:s'),
            'total'     => count($rowsArr),
            'data'      => $rowsArr,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 200, [
            'Content-Type'        => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}.json\"",
        ]);
    }

    // XML
    if ($format === 'xml') {
        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<rapport type=\"{$type}\" generated=\"" . date('Y-m-d H:i:s') . "\" total=\"" . count($rowsArr) . "\">\n";
        foreach ($rowsArr as $row) {
            $xml .= "  <item>\n";
            foreach ($row as $k => $v) {
                $tag  = preg_replace('/[^a-zA-Z0-9_]/', '_', $k);
                $xml .= "    <{$tag}>" . htmlspecialchars((string)($v ?? ''), ENT_XML1, 'UTF-8') . "</{$tag}>\n";
            }
            $xml .= "  </item>\n";
        }
        $xml .= "</rapport>";
        return response($xml, 200, [
            'Content-Type'        => 'application/xml; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xml\"",
        ]);
    }

    // TXT
    if ($format === 'txt') {
        $txt  = str_repeat('═', 50) . "\n";
        $txt .= "  RAPPORT " . strtoupper($type) . "\n";
        $txt .= "  Généré le " . date('d/m/Y à H:i:s') . "\n";
        $txt .= "  Total : " . count($rowsArr) . " enregistrement(s)\n";
        $txt .= str_repeat('═', 50) . "\n\n";
        foreach ($rowsArr as $i => $row) {
            $txt .= "── Enregistrement " . ($i + 1) . " ──\n";
            foreach ($row as $k => $v) {
                $txt .= sprintf("  %-26s: %s\n", $k, (string)($v ?? '—'));
            }
            $txt .= "\n";
        }
        return response($txt, 200, [
            'Content-Type'        => 'text/plain; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.txt\"",
        ]);
    }

    // Excel (XLS via HTML table)
    if ($format === 'excel' || $format === 'xls') {
        $html  = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head><meta charset="UTF-8"><style>th{background:#2fa84f;color:#fff;padding:6px 10px;font-size:12px;}td{padding:5px 10px;border:1px solid #ccc;font-size:12px;}</style></head>';
        $html .= '<body><table border="1" cellspacing="0">';
        if (!empty($rowsArr)) {
            $html .= '<tr>' . implode('', array_map(fn($h) => '<th>' . htmlspecialchars($h) . '</th>', array_keys($rowsArr[0]))) . '</tr>';
            foreach ($rowsArr as $row) {
                $html .= '<tr>' . implode('', array_map(fn($v) => '<td>' . htmlspecialchars((string)($v ?? '')) . '</td>', array_values($row))) . '</tr>';
            }
        }
        $html .= '</table></body></html>';
        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
        ]);
    }

    // SQL
    if ($format === 'sql') {
        $sql  = "-- Rapport {$type} — généré le " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Total : " . count($rowsArr) . " enregistrements\n\n";
        foreach ($rowsArr as $row) {
            $cols = implode(', ', array_map(fn($c) => "`{$c}`", array_keys($row)));
            $vals = implode(', ', array_map(fn($v) => $v === null ? 'NULL' : "'" . addslashes((string)$v) . "'", array_values($row)));
            $sql .= "INSERT INTO `{$table}` ({$cols}) VALUES ({$vals});\n";
        }
        return response($sql, 200, [
            'Content-Type'        => 'application/sql; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.sql\"",
        ]);
    }

    // ── PDF (DomPDF) ──
    if ($format === 'pdf') {
        $typeLabel = [
            'capteurs'=>'Capteurs','anomalies'=>'Anomalies','securite'=>'Sécurité',
            'utilisateurs'=>'Utilisateurs','salles'=>'Salles','serveurs'=>'Serveurs',
            'energie'=>'Énergie','historique'=>'Historique','alertes'=>'Alertes',
            'incidents'=>'Incidents','maintenance'=>'Maintenance',
        ][$type] ?? ucfirst($type);

        $colKeys    = !empty($rowsArr) ? array_keys($rowsArr[0]) : [];
        $colHeaders = array_map(fn($k) => ucwords(str_replace('_', ' ', $k)), $colKeys);
        $xh         = fn($v) => htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');

        $thHtml = implode('', array_map(fn($h) => '<th>' . $xh($h) . '</th>', $colHeaders));
        $trHtml = '';
        foreach ($rowsArr as $i => $row) {
            $bg     = ($i % 2 === 0) ? '#ffffff' : '#f2f6fb';
            $cells  = '';
            foreach (array_values($row) as $v) {
                $txt    = strlen((string)($v ?? '')) > 80 ? mb_substr((string)$v, 0, 80, 'UTF-8') . '…' : (string)($v ?? '');
                $cells .= '<td style="background:' . $bg . '">' . $xh($txt) . '</td>';
            }
            $trHtml .= '<tr>' . $cells . '</tr>';
        }

        $exportDate = date('d/m/Y à H:i:s');
        $total      = count($rowsArr);
        $periode    = ($debut || $fin) ? ('Période : ' . ($debut ?: '…') . ' → ' . ($fin ?: '…')) : '';

        $pdfHtml  = '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">';
        $pdfHtml .= '<style>';
        $pdfHtml .= '* { font-family: DejaVu Sans, Arial, sans-serif; margin:0; padding:0; box-sizing:border-box; }';
        $pdfHtml .= 'body { background:#fff; color:#1a2740; font-size:9px; padding:8px; }';
        $pdfHtml .= '.hdr { background:#1a3a5c; color:#fff; padding:10px 14px; border-radius:5px; margin-bottom:10px; }';
        $pdfHtml .= '.hdr h1 { font-size:14px; margin-bottom:2px; }';
        $pdfHtml .= '.hdr p  { font-size:8px; opacity:.85; }';
        $pdfHtml .= 'table { width:100%; border-collapse:collapse; font-size:8px; }';
        $pdfHtml .= 'th { background:#1a3a5c; color:#fff; padding:5px 6px; text-align:left; font-weight:bold; white-space:nowrap; }';
        $pdfHtml .= 'td { padding:4px 6px; border-bottom:1px solid #dce6f0; color:#2a3a5a; word-break:break-word; }';
        $pdfHtml .= '.foot { text-align:center; color:#9aa5b4; font-size:7px; margin-top:10px; border-top:1px solid #dce6f0; padding-top:5px; }';
        $pdfHtml .= '</style></head><body>';
        $pdfHtml .= '<div class="hdr"><h1>Rapport ' . $xh($typeLabel) . '</h1>';
        $pdfHtml .= '<p>Généré le ' . $xh($exportDate) . ' &nbsp;|&nbsp; ' . $total . ' enregistrement(s)';
        if ($periode) $pdfHtml .= ' &nbsp;|&nbsp; ' . $xh($periode);
        $pdfHtml .= '</p></div>';

        if (!empty($rowsArr)) {
            $pdfHtml .= '<table><thead><tr>' . $thHtml . '</tr></thead><tbody>' . $trHtml . '</tbody></table>';
        } else {
            $pdfHtml .= '<p style="color:#888;padding:20px 0;text-align:center">Aucune donnée disponible.</p>';
        }
        $pdfHtml .= '<div class="foot">Plateforme Surveillance IoT — Rapport généré automatiquement</div>';
        $pdfHtml .= '</body></html>';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($pdfHtml)->setPaper('a4', 'landscape');
        return $pdf->download($filename . '.pdf');
    }

    // ── Word (.docx via ZipArchive + OOXML) ──
    if ($format === 'word' || $format === 'docx') {
        $typeLabel = [
            'capteurs'=>'Capteurs','anomalies'=>'Anomalies','securite'=>'Sécurité',
            'utilisateurs'=>'Utilisateurs','salles'=>'Salles','serveurs'=>'Serveurs',
            'energie'=>'Énergie','historique'=>'Historique','alertes'=>'Alertes',
            'incidents'=>'Incidents','maintenance'=>'Maintenance',
        ][$type] ?? ucfirst($type);

        $colKeys    = !empty($rowsArr) ? array_keys($rowsArr[0]) : [];
        $colHeaders = array_map(fn($k) => ucwords(str_replace('_', ' ', $k)), $colKeys);
        $exportDate = date('d/m/Y à H:i:s');
        $total      = count($rowsArr);
        $xe         = fn($v) => htmlspecialchars((string)($v ?? ''), ENT_XML1, 'UTF-8');

        // ── Table header row ──
        $headCells = '';
        foreach ($colHeaders as $h) {
            $headCells .= '<w:tc>'
                . '<w:tcPr><w:shd w:val="clear" w:color="auto" w:fill="1B3A5C"/></w:tcPr>'
                . '<w:p><w:r>'
                . '<w:rPr><w:color w:val="FFFFFF"/><w:b/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr>'
                . '<w:t xml:space="preserve">' . $xe($h) . '</w:t>'
                . '</w:r></w:p></w:tc>';
        }
        $headerRow = '<w:tr><w:trPr><w:tblHeader/><w:trHeight w:val="450"/></w:trPr>' . $headCells . '</w:tr>';

        // ── Data rows ──
        $dataRows = '';
        foreach ($rowsArr as $i => $row) {
            $fill  = ($i % 2 === 0) ? 'FFFFFF' : 'EDF2FB';
            $cells = '';
            foreach ($row as $v) {
                $txt    = mb_substr((string)($v ?? ''), 0, 200, 'UTF-8');
                $cells .= '<w:tc>'
                    . '<w:tcPr><w:shd w:val="clear" w:color="auto" w:fill="' . $fill . '"/></w:tcPr>'
                    . '<w:p><w:r>'
                    . '<w:rPr><w:color w:val="2A3A5A"/><w:sz w:val="16"/><w:szCs w:val="16"/></w:rPr>'
                    . '<w:t xml:space="preserve">' . $xe($txt) . '</w:t>'
                    . '</w:r></w:p></w:tc>';
            }
            $dataRows .= '<w:tr><w:trPr><w:trHeight w:val="350"/></w:trPr>' . $cells . '</w:tr>';
        }

        $ns = 'xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"';

        // ── document.xml ──
        $docXml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $docXml .= '<w:document ' . $ns . '><w:body>';

        // Title
        $docXml .= '<w:p><w:r>'
            . '<w:rPr><w:b/><w:color w:val="1B3A5C"/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr>'
            . '<w:t>Rapport ' . $xe($typeLabel) . '</w:t>'
            . '</w:r></w:p>';

        // Metadata
        $metaTxt = 'Généré le ' . $xe($exportDate) . '   |   ' . $total . ' enregistrement(s)';
        if ($debut || $fin) $metaTxt .= '   |   Période : ' . $xe($debut ?: '…') . ' → ' . $xe($fin ?: '…');
        $docXml .= '<w:p><w:r>'
            . '<w:rPr><w:color w:val="6B7FA0"/><w:sz w:val="18"/><w:szCs w:val="18"/></w:rPr>'
            . '<w:t xml:space="preserve">' . $metaTxt . '</w:t>'
            . '</w:r></w:p>';

        // Blank line
        $docXml .= '<w:p><w:r><w:t></w:t></w:r></w:p>';

        // Table
        if (!empty($rowsArr)) {
            $docXml .= '<w:tbl>'
                . '<w:tblPr>'
                .   '<w:tblStyle w:val="TableGrid"/>'
                .   '<w:tblW w:w="9638" w:type="dxa"/>'
                .   '<w:tblBorders>'
                .     '<w:top    w:val="single" w:sz="6" w:space="0" w:color="1B3A5C"/>'
                .     '<w:left   w:val="single" w:sz="6" w:space="0" w:color="1B3A5C"/>'
                .     '<w:bottom w:val="single" w:sz="6" w:space="0" w:color="1B3A5C"/>'
                .     '<w:right  w:val="single" w:sz="6" w:space="0" w:color="1B3A5C"/>'
                .     '<w:insideH w:val="single" w:sz="4" w:space="0" w:color="C8D8EA"/>'
                .     '<w:insideV w:val="single" w:sz="4" w:space="0" w:color="C8D8EA"/>'
                .   '</w:tblBorders>'
                . '</w:tblPr>'
                . $headerRow
                . $dataRows
                . '</w:tbl>';
        } else {
            $docXml .= '<w:p><w:r>'
                . '<w:rPr><w:color w:val="999999"/></w:rPr>'
                . '<w:t>Aucune donnée disponible.</w:t>'
                . '</w:r></w:p>';
        }

        // Footer + section (landscape A4)
        $docXml .= '<w:p><w:r>'
            . '<w:rPr><w:color w:val="9AA5B4"/><w:sz w:val="14"/><w:szCs w:val="14"/></w:rPr>'
            . '<w:t>Plateforme Surveillance IoT — Rapport généré automatiquement</w:t>'
            . '</w:r></w:p>';
        $docXml .= '<w:sectPr>'
            . '<w:pgSz w:w="16838" w:h="11906" w:orient="landscape"/>'
            . '<w:pgMar w:top="720" w:right="720" w:bottom="720" w:left="720"/>'
            . '</w:sectPr>';
        $docXml .= '</w:body></w:document>';

        // ── ZIP assembly ──
        $contentTypes  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml"  ContentType="application/xml"/>'
            . '<Override PartName="/word/document.xml"'
            .   ' ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
            . '</Types>';

        $pkgRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1"'
            .   ' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument"'
            .   ' Target="word/document.xml"/>'
            . '</Relationships>';

        $docRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"></Relationships>';

        $tmpFile = tempnam(sys_get_temp_dir(), 'rpt_docx_');
        $zip     = new \ZipArchive();
        if ($zip->open($tmpFile, \ZipArchive::OVERWRITE) !== true) {
            return redirect('/rapports')->with('error', 'Erreur création fichier Word.');
        }
        $zip->addFromString('[Content_Types].xml',       $contentTypes);
        $zip->addFromString('_rels/.rels',               $pkgRels);
        $zip->addFromString('word/_rels/document.xml.rels', $docRels);
        $zip->addFromString('word/document.xml',         $docXml);
        $zip->close();

        $content = file_get_contents($tmpFile);
        @unlink($tmpFile);

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => "attachment; filename=\"{$filename}.docx\"",
            'Content-Length'      => strlen($content),
        ]);
    }

    return redirect('/rapports')->with('error', 'Format non supporté.');
});

// Validation admin supprimée — toutes les routes redirigent vers le dashboard
Route::get('/admin/utilisateurs',                function () { return redirect('/dashboard'); });
Route::post('/admin/valider/{id}',               function ()  { return redirect('/dashboard'); });
Route::post('/admin/refuser/{id}',               function ()  { return redirect('/dashboard'); });
Route::post('/admin/attente/{id}',               function ()  { return redirect('/dashboard'); });
Route::get('/admin/valider-mail/{id}/{token}',   function ()  { return redirect('/dashboard'); });
Route::get('/admin/refuser-mail/{id}/{token}',   function ()  { return redirect('/dashboard'); });
Route::get('/admin/attente-mail/{id}/{token}',   function ()  { return redirect('/dashboard'); });

// ═══════════════════════════════════════════════════════════
//  RÉINITIALISATION MOT DE PASSE (accessible sans connexion)
// ═══════════════════════════════════════════════════════════

Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm']);
Route::post('/forgot-password', [AuthController::class, 'forgotPasswordPost']);
Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm']);
Route::post('/reset-password', [AuthController::class, 'resetPasswordPost']);
