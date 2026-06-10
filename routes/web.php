<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ParametresController;
use App\Http\Controllers\ServeursController;
use App\Http\Controllers\SallesController;
use App\Http\Controllers\GeoController;



Route::view('/','accueil');
Route::view('/accueil','accueil');
Route::view('/dashboard','dashboard');
Route::view('/login','login');


Route::post('/login-user',    [AuthController::class, 'login']);

Route::get('/profil',          [ProfilController::class, 'show']);
Route::post('/profil/update',  [ProfilController::class, 'update']);
Route::post('/profil/password',[ProfilController::class, 'changePassword']);
Route::post('/profil/photo',   [ProfilController::class, 'uploadPhoto']);






Route::view('/alertes','alertes');
Route::view('/historique','historique');
Route::view('/statistiques','statistiques');
Route::view('/mails','mails');
Route::view('/anomalies','anomalies');
// [NOUVEAU] Routes proxy GeoNames (username jamais exposé côté client)
Route::get('/geo/pays',                        [GeoController::class, 'getPays']);
Route::get('/geo/regions/{geonameId}',         [GeoController::class, 'getRegions']);
Route::get('/geo/departements/{geonameId}',    [GeoController::class, 'getDepartements']);
Route::get('/geo/arrondissements/{geonameId}', [GeoController::class, 'getArrondissements']);
Route::get('/geo/villes/{geonameId}',          [GeoController::class, 'getVilles']);
Route::get('/salles',          [SallesController::class, 'index']);
Route::post('/salles',         [SallesController::class, 'store']);
Route::delete('/salles/{id}',  [SallesController::class, 'destroy']);
Route::post('/salles/{id}',    [SallesController::class, 'update']);

Route::get('/equipements',        [ServeursController::class, 'index']);
Route::post('/equipements',       [ServeursController::class, 'store']);
Route::delete('/equipements/{id}',[ServeursController::class, 'destroy']);
Route::post('/equipements/{id}',  [ServeursController::class, 'update']);

Route::redirect('/serveurs',     '/equipements', 301);
Route::redirect('/serveurs-web', '/equipements', 301);
Route::redirect('/serveurs-bd',  '/equipements', 301);
Route::get('/parametres',         [ParametresController::class, 'show']);
Route::post('/parametres/seuils', [ParametresController::class, 'saveSeuils']);
Route::view('/rapports','rapports');


// ── AJAX : supprimer utilisateur ───────────────────────────────────────────
Route::delete('/user/{id}', function ($id) {
    if (!session('user')) return response()->json(['error' => 'Non autorisé'], 401);
    $target = DB::table('users')->where('id', $id)->first();
    if ($target && ($target->id == 1 || $target->role === 'superadmin'))
        return response()->json(['error' => 'Impossible de supprimer le compte administrateur principal.'], 403);
    DB::table('users')->where('id', $id)->delete();
    return response()->json(['success' => true, 'message' => 'Utilisateur supprimé.']);
});

// ── AJAX : supprimer une alerte ────────────────────────────────────────────
Route::delete('/alerte/{id}', function ($id) {
    if (!session('user')) return response()->json(['error' => 'Non autorisé'], 401);
    DB::table('alertes')->where('id', $id)->delete();
    return response()->json(['success' => true]);
});

// ── AJAX : vider toutes les alertes ───────────────────────────────────────
Route::post('/alertes/vider', function () {
    if (!session('user')) return response()->json(['error' => 'Non autorisé'], 401);
    $n = DB::table('alertes')->count();
    DB::table('alertes')->truncate();
    return response()->json(['success' => true, 'message' => "$n alertes supprimées."]);
});

// ── AJAX : ping serveur ────────────────────────────────────────────────────
Route::get('/serveur/{id}/ping', function ($id) {
    if (!session('user')) return response()->json(['error' => 'Non autorisé'], 401);
    try {
        $srv = DB::table('serveurs')->where('id', $id)->first();
        if (!$srv) return response()->json(['reachable' => false, 'msg' => 'Serveur introuvable']);
        $ip = $srv->adresse_ip;
        if (!$ip) return response()->json(['reachable' => false, 'ip' => null, 'msg' => 'Aucune IP configurée']);

        $start = microtime(true);
        $conn  = @fsockopen($ip, 80, $e, $es, 2);
        $ms    = round((microtime(true) - $start) * 1000, 1);
        if ($conn) { fclose($conn); return response()->json(['reachable'=>true,'ip'=>$ip,'time'=>$ms,'msg'=>"En ligne — {$ms}ms"]); }

        $out = []; $ret = 1;
        exec('ping -c 1 -W 2 '.escapeshellarg($ip).' 2>&1', $out, $ret);
        $t = null;
        foreach ($out as $line) { if (preg_match('/time[<=](\d+\.?\d*)\s*ms/i',$line,$m)){$t=$m[1];break;} }
        return response()->json(['reachable'=>$ret===0,'ip'=>$ip,'time'=>$t,'msg'=>$ret===0?"En ligne — {$t}ms":"Inaccessible ({$ip})"]);
    } catch (\Exception $e) {
        return response()->json(['reachable' => false, 'msg' => 'Erreur: '.$e->getMessage()]);
    }
});

// ── Ping caméra IP (fsockopen) ──────────────────────────────────────────────
Route::get('/camera/ping', function (\Illuminate\Http\Request $request) {
    if (!session('user')) return response()->json(['reachable' => false, 'msg' => 'Non autorisé']);
    $ip   = $request->ip_addr ?? '';
    $port = max(1, min(65535, (int)($request->port ?? 80)));
    if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP))
        return response()->json(['reachable' => false, 'msg' => 'Adresse IP invalide']);
    $start = microtime(true);
    $conn  = @fsockopen($ip, $port, $errno, $errstr, 2);
    $ms    = round((microtime(true) - $start) * 1000, 1);
    if ($conn) { fclose($conn); return response()->json(['reachable' => true, 'time' => $ms]); }
    return response()->json(['reachable' => false, 'time' => $ms, 'msg' => "Inaccessible ({$ip}:{$port})"]);
});

// ── Impression / PDF ───────────────────────────────────────────────────────
Route::get('/rapports/rapport-72h', function () {
    if (!session('user')) return redirect('/login');

    $latest = DB::table('mesures')->orderByDesc('created_at')->value('created_at');
    if (!$latest) {
        return view('rapport_72h', ['rows' => collect(), 'debut' => now()->subHours(72), 'fin' => now()]);
    }
    $fin   = \Carbon\Carbon::parse($latest);
    $debut = $fin->copy()->subHours(72);

    $rows = DB::table('mesures')
        ->select(['id','temperature','humidite','gaz','pir_detecte','salle_id','created_at'])
        ->whereBetween('created_at', [$debut, $fin])
        ->orderBy('created_at')
        ->get();

    return view('rapport_72h', compact('rows', 'debut', 'fin'));
});

// ── Fonction partagée de génération Word (24h ou 72h) ──────────────────────
$genWordRapport = function (int $heures) {
    if (!session('user')) return redirect('/login');

    $latest = DB::table('mesures')->orderByDesc('created_at')->value('created_at');
    $fin    = $latest ? \Carbon\Carbon::parse($latest) : now();
    $debut  = $fin->copy()->subHours($heures);

    $rows = DB::table('mesures')
        ->select(['id','temperature','humidite','gaz','pir_detecte','salle_id','created_at'])
        ->whereBetween('created_at', [$debut, $fin])
        ->orderBy('created_at')
        ->get();

    $n         = $rows->count();
    $critiques = $rows->filter(fn($r) => $r->temperature >= 32 || $r->humidite >= 85 || $r->gaz >= 600)->count();
    $warnings  = $rows->filter(fn($r) =>
        !($r->temperature >= 32 || $r->humidite >= 85 || $r->gaz >= 600) &&
        ($r->temperature >= 28 || $r->humidite >= 75 || $r->gaz >= 400)
    )->count();
    $pirOui    = $rows->filter(fn($r) => $r->pir_detecte)->count();

    $word = new \PhpOffice\PhpWord\PhpWord();
    $word->setDefaultFontName('Times New Roman');
    $word->setDefaultFontSize(12);
    $word->addTitleStyle(1,
        ['name'=>'Times New Roman','size'=>16,'bold'=>true,'color'=>'0D47A1'],
        ['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER,'spaceAfter'=>200]);
    $word->addTitleStyle(2,
        ['name'=>'Times New Roman','size'=>13,'bold'=>true,'color'=>'1565C0'],
        ['spaceAfter'=>120]);

    $section = $word->addSection([
        'marginTop'    => 1200,
        'marginBottom' => 1200,
        'marginLeft'   => 1200,
        'marginRight'  => 1200,
        'orientation'  => 'landscape',
        'pageSizeW'    => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(29.7),
        'pageSizeH'    => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(21),
    ]);

    $section->addTitle('Rapport '.$heures.'h — Mesures capteurs IoT', 1);

    $pStyle   = ['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER,'spaceAfter'=>160];
    $metaPara = $section->addTextRun($pStyle);
    $metaPara->addText('Période : ',          ['name'=>'Times New Roman','size'=>12,'bold'=>true]);
    $metaPara->addText($debut->format('d/m/Y H:i').' -> '.$fin->format('d/m/Y H:i'),
                                              ['name'=>'Times New Roman','size'=>12]);
    $metaPara->addText('     Généré le : ',   ['name'=>'Times New Roman','size'=>12,'bold'=>true]);
    $metaPara->addText(now()->format('d/m/Y a H:i'), ['name'=>'Times New Roman','size'=>12]);
    $section->addTextBreak(1);

    $section->addTitle('Résumé de la période', 2);
    $statTable = $section->addTable([
        'borderSize'=>6,'borderColor'=>'1565C0',
        'cellMarginLeft'=>80,'cellMarginRight'=>80,'cellMarginTop'=>80,'cellMarginBottom'=>80,
    ]);
    $statTable->addRow(600);
    $lblFont  = ['name'=>'Times New Roman','size'=>11,'bold'=>true,'color'=>'FFFFFF'];
    $valFont  = ['name'=>'Times New Roman','size'=>22,'bold'=>true];
    $unitFont = ['name'=>'Times New Roman','size'=>10,'color'=>'555555'];
    $hdrAlign = ['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER,'spaceAfter'=>0];
    $statCols = [
        ['Total mesures', $n,         '0D47A1','enreg.'],
        ['Critiques',     $critiques, 'C62828','mesures'],
        ['Warnings',      $warnings,  'E65100','mesures'],
        ['PIR détecté',   $pirOui,    '1B5E20','détections'],
    ];
    $bgColors = ['BBDEFB','FFCDD2','FFE0B2','C8E6C9'];
    foreach ($statCols as $si => $sc) {
        $cell = $statTable->addCell(3600, ['bgColor'=>$bgColors[$si],'vAlign'=>'center']);
        $cell->addText($sc[0],         array_merge($lblFont, ['color'=>$sc[2]]), array_merge($hdrAlign,['spaceBefore'=>40]));
        $cell->addText((string)$sc[1], array_merge($valFont, ['color'=>$sc[2]]), $hdrAlign);
        $cell->addText($sc[3],         $unitFont,                                array_merge($hdrAlign,['spaceAfter'=>40]));
    }

    $section->addTextBreak(1);
    $section->addTitle('Détail des mesures', 2);

    $dataTable = $section->addTable([
        'borderSize'=>4,'borderColor'=>'AAAAAA',
        'cellMarginLeft'=>60,'cellMarginRight'=>60,'cellMarginTop'=>50,'cellMarginBottom'=>50,
        'width'=>100,'unit'=>'pct',
    ]);
    $dataTable->addRow(500, ['tblHeader'=>true]);
    $hBg    = ['bgColor'=>'1565C0'];
    $hFont  = ['name'=>'Times New Roman','size'=>12,'bold'=>true,'color'=>'FFFFFF'];
    $hAlign = ['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER,'spaceAfter'=>0];
    $colDef = [
        ['#',700],['Date / Heure',2800],['Temp. (°C)',1800],
        ['Humidité (%)',1800],['Gaz (ppm)',1800],['PIR',1200],['Niveau',1800],
    ];
    foreach ($colDef as [$lbl, $w]) {
        $dataTable->addCell($w, $hBg)->addText($lbl, $hFont, $hAlign);
    }

    foreach ($rows as $ri => $r) {
        $isCrit = $r->temperature >= 32 || $r->humidite >= 85 || $r->gaz >= 600;
        $isWarn = !$isCrit && ($r->temperature >= 28 || $r->humidite >= 75 || $r->gaz >= 400);
        $dataTable->addRow(400);
        $bg    = ['bgColor' => $isCrit ? 'FFEBEE' : ($isWarn ? 'FFF3E0' : ($ri%2===0 ? 'FFFFFF' : 'F5F5F5'))];
        $nTxt  = $isCrit ? 'CRITIQUE' : ($isWarn ? 'WARNING' : 'NORMAL');
        $nClr  = $isCrit ? 'C62828'   : ($isWarn ? 'E65100'  : '1B5E20');
        $tClr  = $r->temperature >= 32 ? 'C62828' : ($r->temperature >= 28 ? 'E65100' : '1B1B1B');
        $hClr  = $r->humidite    >= 85 ? 'C62828' : ($r->humidite    >= 75 ? 'E65100' : '1B1B1B');
        $gClr  = $r->gaz         >= 600? 'C62828' : ($r->gaz         >= 400? 'E65100' : '1B1B1B');
        $pClr  = $r->pir_detecte ? 'C62828' : '555555';
        $fN    = ['name'=>'Times New Roman','size'=>12];
        $fC    = ['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER,'spaceAfter'=>0];
        $fR    = ['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::RIGHT, 'spaceAfter'=>0];
        $dataTable->addCell($colDef[0][1],$bg)->addText((string)$r->id, array_merge($fN,['color'=>'888888']), $fC);
        $dataTable->addCell($colDef[1][1],$bg)->addText(\Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i:s'), $fN, ['spaceAfter'=>0]);
        $dataTable->addCell($colDef[2][1],$bg)->addText(number_format((float)($r->temperature??0),1), array_merge($fN,['color'=>$tClr,'bold'=>$tClr!='1B1B1B']), $fR);
        $dataTable->addCell($colDef[3][1],$bg)->addText(number_format((float)($r->humidite??0),1),    array_merge($fN,['color'=>$hClr,'bold'=>$hClr!='1B1B1B']), $fR);
        $dataTable->addCell($colDef[4][1],$bg)->addText(number_format((float)($r->gaz??0),0),         array_merge($fN,['color'=>$gClr,'bold'=>$gClr!='1B1B1B']), $fR);
        $dataTable->addCell($colDef[5][1],$bg)->addText($r->pir_detecte?'OUI':'NON', array_merge($fN,['color'=>$pClr,'bold'=>(bool)$r->pir_detecte]), $fC);
        $dataTable->addCell($colDef[6][1],$bg)->addText($nTxt, array_merge($fN,['color'=>$nClr,'bold'=>true]), $fC);
    }

    $footer = $section->addFooter();
    $footer->addPreserveText(
        'Plateforme de Surveillance  ·  Rapport '.$heures.'h  ·  Généré le '.now()->format('d/m/Y H:i').'  ·  Page {PAGE}/{NUMPAGES}',
        ['name'=>'Times New Roman','size'=>10,'color'=>'888888'],
        ['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER]
    );

    $filename = 'rapport_'.$heures.'h_'.date('Y-m-d_H-i').'.docx';
    $tmpFile  = tempnam(sys_get_temp_dir(), 'rapport_');
    \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007')->save($tmpFile);
    return response()->download($tmpFile, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ])->deleteFileAfterSend(true);
};

Route::get('/rapports/rapport-24h/word', function () {
    if (!session('user')) return redirect('/login');

    // ── Données ───────────────────────────────────────────────────────────
    $latest = DB::table('mesures')->orderByDesc('created_at')->value('created_at');
    $fin    = $latest ? \Carbon\Carbon::parse($latest) : now();
    $debut  = $fin->copy()->subHours(24);

    $allRows = DB::table('mesures')
        ->select(['id','temperature','humidite','gaz','pir_detecte','salle_id','created_at'])
        ->whereBetween('created_at', [$debut, $fin])
        ->orderBy('created_at')
        ->get();

    $totalAll  = $allRows->count();
    $critiques = $allRows->filter(fn($r) => $r->temperature >= 32 || $r->humidite >= 85 || $r->gaz >= 600)->count();
    $warnings  = $allRows->filter(fn($r) =>
        !($r->temperature >= 32 || $r->humidite >= 85 || $r->gaz >= 600) &&
        ($r->temperature >= 28 || $r->humidite >= 75 || $r->gaz >= 400)
    )->count();
    $pirOui    = $allRows->filter(fn($r) => $r->pir_detecte)->count();

    // Filtrer : WARNING / CRITIQUE / PIR uniquement
    $rows = $allRows->filter(fn($r) =>
        $r->temperature >= 28 || $r->humidite >= 75 || $r->gaz >= 400 || $r->pir_detecte
    )->values();

    // Si trop de lignes, garder CRITIQUE + PIR seulement
    $PAGE1   = 25;  // lignes sur page 1 (après entête)
    $PAGE2   = 30;  // lignes sur page 2
    $PAGE3   = 30;  // lignes sur page 3
    $MAX     = $PAGE1 + $PAGE2 + $PAGE3; // = 85
    $tronque = false;
    if ($rows->count() > $MAX) {
        $rows = $rows->filter(fn($r) =>
            $r->temperature >= 32 || $r->humidite >= 85 || $r->gaz >= 600 || $r->pir_detecte
        )->values();
    }
    if ($rows->count() > $MAX) {
        $rows    = $rows->take($MAX);
        $tronque = true;
    }

    // ── PhpWord ───────────────────────────────────────────────────────────
    $word = new \PhpOffice\PhpWord\PhpWord();
    $word->setDefaultFontName('Times New Roman');
    $word->setDefaultFontSize(10);
    $word->addTitleStyle(1,
        ['name'=>'Times New Roman','size'=>15,'bold'=>true,'color'=>'0D47A1'],
        ['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER,'spaceAfter'=>140]);
    $word->addTitleStyle(2,
        ['name'=>'Times New Roman','size'=>11,'bold'=>true,'color'=>'1565C0'],
        ['spaceAfter'=>80]);

    $section = $word->addSection([
        'marginTop'    => 850,
        'marginBottom' => 850,
        'marginLeft'   => 900,
        'marginRight'  => 900,
        'orientation'  => 'landscape',
        'pageSizeW'    => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(29.7),
        'pageSizeH'    => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(21),
    ]);

    // ── Page 1 : entête ───────────────────────────────────────────────────
    $section->addTitle('Rapport 24h condensé — Alertes capteurs IoT', 1);

    $metaPara = $section->addTextRun(['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER,'spaceAfter'=>100]);
    $metaPara->addText('Période : ',          ['name'=>'Times New Roman','size'=>10,'bold'=>true]);
    $metaPara->addText($debut->format('d/m/Y H:i').' → '.$fin->format('d/m/Y H:i'), ['name'=>'Times New Roman','size'=>10]);
    $metaPara->addText('   |   Généré le : ', ['name'=>'Times New Roman','size'=>10,'bold'=>true]);
    $metaPara->addText(now()->format('d/m/Y H:i'), ['name'=>'Times New Roman','size'=>10]);
    $metaPara->addText('   |   '.$totalAll.' mesures totales', ['name'=>'Times New Roman','size'=>9,'color'=>'888888']);

    $section->addTitle('Résumé de la période', 2);

    $hdrAlign = ['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER,'spaceAfter'=>0];
    $statTable = $section->addTable(['borderSize'=>5,'borderColor'=>'1565C0','cellMarginLeft'=>50,'cellMarginRight'=>50,'cellMarginTop'=>50,'cellMarginBottom'=>50]);
    $statTable->addRow(480);
    foreach ([
        ['Total mesures', $totalAll,  '0D47A1','BBDEFB','enreg.'],
        ['Critiques',     $critiques, 'C62828','FFCDD2','mesures'],
        ['Warnings',      $warnings,  'E65100','FFE0B2','mesures'],
        ['PIR détecté',   $pirOui,    '1B5E20','C8E6C9','détections'],
    ] as $sc) {
        $cell = $statTable->addCell(3600, ['bgColor'=>$sc[3],'vAlign'=>'center']);
        $cell->addText($sc[0], ['name'=>'Times New Roman','size'=>9,'bold'=>true,'color'=>$sc[2]], array_merge($hdrAlign,['spaceBefore'=>20]));
        $cell->addText((string)$sc[1], ['name'=>'Times New Roman','size'=>16,'bold'=>true,'color'=>$sc[2]], $hdrAlign);
        $cell->addText($sc[4], ['name'=>'Times New Roman','size'=>8,'color'=>'666666'], array_merge($hdrAlign,['spaceAfter'=>20]));
    }

    $section->addTextBreak(1);

    $noteStr = 'Affichage : lignes WARNING / CRITIQUE / PIR uniquement ('.$rows->count().' alertes';
    $noteStr .= $tronque ? ', limité à '.$MAX.')' : ')';
    $section->addTextRun(['spaceAfter'=>80])
            ->addText($noteStr, ['name'=>'Times New Roman','size'=>8,'italic'=>true,'color'=>'888888']);

    $section->addTitle('Détail des alertes', 2);

    // ── Styles tableau partagés ────────────────────────────────────────────
    $tblStyle = ['borderSize'=>3,'borderColor'=>'BBBBBB','cellMarginLeft'=>25,'cellMarginRight'=>25,'cellMarginTop'=>25,'cellMarginBottom'=>25,'width'=>100,'unit'=>'pct'];
    $colDef   = [
        ['N°',600],['Date / Heure',2600],['Temp. (°C)',1700],
        ['Humidité (%)',1700],['Gaz (ppm)',1700],['PIR',1000],['Niveau',1600],
    ];
    $hBg    = ['bgColor'=>'1565C0'];
    $hFont  = ['name'=>'Times New Roman','size'=>9,'bold'=>true,'color'=>'FFFFFF'];
    $hAlgn  = ['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER,'spaceAfter'=>0];

    $addHeader = function($tbl) use ($hBg, $hFont, $hAlgn, $colDef) {
        $tbl->addRow(360, ['tblHeader'=>true]);
        foreach ($colDef as [$lbl, $w]) {
            $tbl->addCell($w, $hBg)->addText($lbl, $hFont, $hAlgn);
        }
    };

    $addRow = function($tbl, $r, $num) use ($colDef) {
        $isCrit = $r->temperature >= 32 || $r->humidite >= 85 || $r->gaz >= 600;
        $isWarn = !$isCrit && ($r->temperature >= 28 || $r->humidite >= 75 || $r->gaz >= 400);
        $tbl->addRow(280);
        $bg   = ['bgColor' => $isCrit ? 'FFEBEE' : ($isWarn ? 'FFF3E0' : ($num%2===0 ? 'FFFFFF' : 'F5F5F5'))];
        $nTxt = $isCrit ? 'CRITIQUE' : ($isWarn ? 'WARNING' : 'NORMAL');
        $nClr = $isCrit ? 'C62828'   : ($isWarn ? 'E65100'  : '1B5E20');
        $tClr = $r->temperature >= 32 ? 'C62828' : ($r->temperature >= 28 ? 'E65100' : '1B1B1B');
        $hClr = $r->humidite    >= 85 ? 'C62828' : ($r->humidite    >= 75 ? 'E65100' : '1B1B1B');
        $gClr = $r->gaz         >= 600? 'C62828' : ($r->gaz         >= 400? 'E65100' : '1B1B1B');
        $pClr = $r->pir_detecte ? 'C62828' : '777777';
        $fN   = ['name'=>'Times New Roman','size'=>9];
        $fC   = ['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER,'spaceAfter'=>0];
        $fR   = ['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::RIGHT, 'spaceAfter'=>0];
        $tbl->addCell($colDef[0][1],$bg)->addText((string)$num, array_merge($fN,['color'=>'999999']), $fC);
        $tbl->addCell($colDef[1][1],$bg)->addText(\Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i'), array_merge($fN,['color'=>'222222']), ['spaceAfter'=>0]);
        $tbl->addCell($colDef[2][1],$bg)->addText(number_format((float)($r->temperature??0),1), array_merge($fN,['color'=>$tClr,'bold'=>$tClr!='1B1B1B']), $fR);
        $tbl->addCell($colDef[3][1],$bg)->addText(number_format((float)($r->humidite??0),1),    array_merge($fN,['color'=>$hClr,'bold'=>$hClr!='1B1B1B']), $fR);
        $tbl->addCell($colDef[4][1],$bg)->addText(number_format((float)($r->gaz??0),0),         array_merge($fN,['color'=>$gClr,'bold'=>$gClr!='1B1B1B']), $fR);
        $tbl->addCell($colDef[5][1],$bg)->addText($r->pir_detecte?'OUI':'NON', array_merge($fN,['color'=>$pClr,'bold'=>(bool)$r->pir_detecte]), $fC);
        $tbl->addCell($colDef[6][1],$bg)->addText($nTxt, array_merge($fN,['color'=>$nClr,'bold'=>true]), $fC);
    };

    // ── Tableau page 1 ───────────────────────────────────────────────────
    $chunk1 = $rows->slice(0,       $PAGE1);
    $chunk2 = $rows->slice($PAGE1,  $PAGE2);
    $chunk3 = $rows->slice($PAGE1 + $PAGE2);

    $t1 = $section->addTable($tblStyle);
    $addHeader($t1);
    foreach ($chunk1 as $i => $r) { $addRow($t1, $r, $i + 1); }

    // ── Tableau page 2 ───────────────────────────────────────────────────
    if ($chunk2->count() > 0) {
        $section->addPageBreak();
        $t2 = $section->addTable($tblStyle);
        $addHeader($t2);
        foreach ($chunk2->values() as $i => $r) { $addRow($t2, $r, $PAGE1 + $i + 1); }
    }

    // ── Tableau page 3 ───────────────────────────────────────────────────
    if ($chunk3->count() > 0) {
        $section->addPageBreak();
        $t3 = $section->addTable($tblStyle);
        $addHeader($t3);
        foreach ($chunk3->values() as $i => $r) { $addRow($t3, $r, $PAGE1 + $PAGE2 + $i + 1); }
    }

    // ── Pied de page ─────────────────────────────────────────────────────
    $footer = $section->addFooter();
    $footer->addPreserveText(
        'Plateforme de Surveillance  ·  Rapport 24h condensé  ·  Généré le '.now()->format('d/m/Y H:i').'  ·  Page {PAGE}/{NUMPAGES}',
        ['name'=>'Times New Roman','size'=>9,'color'=>'999999'],
        ['alignment'=>\PhpOffice\PhpWord\SimpleType\Jc::CENTER]
    );

    $filename = 'rapport_24h_'.date('Y-m-d_H-i').'.docx';
    $tmpFile  = tempnam(sys_get_temp_dir(), 'rapport_');
    \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007')->save($tmpFile);
    return response()->download($tmpFile, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ])->deleteFileAfterSend(true);
});

Route::get('/rapports/rapport-72h/word', function () use ($genWordRapport) {
    return $genWordRapport(72);
});

Route::get('/rapports/rapport-72h/png', function () {
    if (!session('user')) return redirect('/login');

    $latest = DB::table('mesures')->orderByDesc('created_at')->value('created_at');
    $fin    = $latest ? \Carbon\Carbon::parse($latest) : now();
    $debut  = $fin->copy()->subHours(72);

    $rows = DB::table('mesures')
        ->select(['id','temperature','humidite','gaz','pir_detecte','salle_id','created_at'])
        ->whereBetween('created_at', [$debut, $fin])
        ->orderBy('created_at')
        ->get()->toArray();

    // Liberation Serif = équivalent métrique exact de Times New Roman
    $fontR = '/usr/share/fonts/truetype/liberation/LiberationSerif-Regular.ttf';
    $fontB = '/usr/share/fonts/truetype/liberation/LiberationSerif-Bold.ttf';
    $fs    = 12;  // taille de police corps tableau (pt)

    // ── Dimensions haute résolution (fond blanc — compatible Word) ────────
    $W       = 1600;
    $padX    = 50;
    $rowH    = 38;    // hauteur ligne tableau (12pt → légèrement plus grand)
    $headH   = 115;   // bandeau titre
    $statsH  = 115;   // blocs statistiques
    $chartH  = 270;   // graphique
    $tblHead = 44;    // en-tête colonnes tableau
    $n       = count($rows);
    $H       = $headH + $statsH + 20 + $chartH + 20 + $tblHead + ($n * $rowH) + 60;

    $img = imagecreatetruecolor($W, max($H, 600));

    // ── Palette fond blanc ────────────────────────────────────────────────
    $cWhite   = imagecolorallocate($img, 255, 255, 255);
    $cBlack   = imagecolorallocate($img, 20,  20,  20);
    $cGray    = imagecolorallocate($img, 100, 100, 100);
    $cLGray   = imagecolorallocate($img, 220, 220, 220);
    $cXLGray  = imagecolorallocate($img, 245, 245, 245);
    $cNavy    = imagecolorallocate($img, 13,  71,  161);  // bleu foncé titre
    $cNavyBg  = imagecolorallocate($img, 21,  101, 192);  // en-tête colonnes
    $cRed     = imagecolorallocate($img, 198, 40,  40);   // critique
    $cRedBg   = imagecolorallocate($img, 255, 235, 238);  // fond ligne critique
    $cOrange  = imagecolorallocate($img, 230, 81,  0);    // warning
    $cOrgBg   = imagecolorallocate($img, 255, 243, 224);  // fond ligne warning
    $cGreen   = imagecolorallocate($img, 27,  94,  32);   // normal
    $cBlueTxt = imagecolorallocate($img, 13,  71,  161);  // valeur bleue
    $cStatBg  = [
        imagecolorallocate($img, 227, 242, 253),
        imagecolorallocate($img, 255, 235, 238),
        imagecolorallocate($img, 255, 243, 224),
        imagecolorallocate($img, 232, 245, 233),
    ];
    $cStatVal = [$cNavy, $cRed, $cOrange, $cGreen];

    // Fond blanc
    imagefilledrectangle($img, 0, 0, $W-1, $H-1, $cWhite);

    // ── Bandeau titre ────────────────────────────────────────────────────
    imagefilledrectangle($img, 0, 0, $W-1, $headH-1, $cNavy);
    imagettftext($img, 22, 0, $padX, 46,  $cWhite,  $fontB, 'Rapport 72 heures — Mesures capteurs IoT');
    imagettftext($img, $fs, 0, $padX, 72,  $cXLGray, $fontR,
        'Période : '.$debut->format('d/m/Y H:i').'  →  '.$fin->format('d/m/Y H:i'));
    imagettftext($img, $fs, 0, $padX, 94,  $cXLGray, $fontR,
        'Généré le '.now()->format('d/m/Y à H:i').'   ·   '.$n.' enregistrement(s)');

    // ── Blocs statistiques ───────────────────────────────────────────────
    $critiques = 0; $warnings = 0; $pirOui = 0;
    foreach ($rows as $r) {
        if ($r->temperature >= 32 || $r->humidite >= 85 || $r->gaz >= 600) $critiques++;
        elseif ($r->temperature >= 28 || $r->humidite >= 75 || $r->gaz >= 400) $warnings++;
        if ($r->pir_detecte) $pirOui++;
    }
    $stats = [
        ['val'=>$n,         'lbl'=>'Total mesures',   'unit'=>'enreg.'],
        ['val'=>$critiques, 'lbl'=>'Niveau critique',  'unit'=>'mesures'],
        ['val'=>$warnings,  'lbl'=>'Niveau warning',   'unit'=>'mesures'],
        ['val'=>$pirOui,    'lbl'=>'Mouvement PIR',    'unit'=>'détections'],
    ];
    $sy  = $headH + 14;
    $sw  = (int)(($W - 2*$padX - 30) / 4);
    foreach ($stats as $i => $s) {
        $sx = $padX + $i * ($sw + 10);
        imagefilledrectangle($img, $sx, $sy, $sx+$sw, $sy+$statsH-16, $cStatBg[$i]);
        imagerectangle($img, $sx, $sy, $sx+$sw, $sy+$statsH-16, $cLGray);
        imagettftext($img, 30,   0, $sx+18, $sy+52, $cStatVal[$i], $fontB, (string)$s['val']);
        imagettftext($img, $fs,  0, $sx+18, $sy+72, $cBlack,       $fontB, $s['lbl']);
        imagettftext($img, $fs-1,0, $sx+18, $sy+90, $cGray,        $fontR, $s['unit']);
    }

    // ── Graphique (fond blanc) ───────────────────────────────────────────
    $gx = $padX;
    $gy = $headH + $statsH + 24;
    $gw = $W - 2*$padX;
    $gh = $chartH;
    $giX = $gx + 55;  // zone tracé (laisser espace axes gauche)
    $giW = $gw - 60;
    $giY = $gy + 30;
    $giH = $gh - 50;

    imagefilledrectangle($img, $gx, $gy, $gx+$gw, $gy+$gh, $cWhite);
    imagerectangle($img, $gx, $gy, $gx+$gw, $gy+$gh, $cLGray);
    imagettftext($img, $fs, 0, $gx+10, $gy+22, $cGray, $fontB, 'ÉVOLUTION TEMPÉRATURE (°C) / HUMIDITÉ (%) / GAZ ÷ 10');

    // Grille horizontale + valeurs axe Y
    for ($g = 0; $g <= 5; $g++) {
        $yl  = $giY + (int)($giH * (1 - $g/5));
        $val = $g * 20;
        imageline($img, $giX, $yl, $giX+$giW, $yl, $g===0 ? $cLGray : $cXLGray);
        imagettftext($img, $fs-2, 0, $gx+8, $yl+4, $cGray, $fontR, (string)$val);
    }
    // Axe X bas
    imageline($img, $giX, $giY+$giH, $giX+$giW, $giY+$giH, $cLGray);

    if ($n >= 2) {
        // Repères temporels axe X (max 8 labels)
        $step = max(1, (int)($n / 8));
        foreach ($rows as $idx => $r) {
            if ($idx % $step === 0 || $idx === $n-1) {
                $px = $giX + (int)($giW * $idx / max(1,$n-1));
                imageline($img, $px, $giY+$giH, $px, $giY+$giH+4, $cGray);
                $lbl = \Carbon\Carbon::parse($r->created_at)->format('d/m H:i');
                imagettftext($img, $fs-3, 0, $px-22, $giY+$giH+18, $cGray, $fontR, $lbl);
            }
        }

        $series = [
            ['key'=>'temperature','color'=>$cRed,     'max'=>100],
            ['key'=>'humidite',   'color'=>$cBlueTxt, 'max'=>100],
            ['key'=>'gaz',        'color'=>$cOrange,  'max'=>1000, 'div'=>10],
        ];
        foreach ($series as $serie) {
            $pts = [];
            foreach ($rows as $idx => $r) {
                $val = (float)($r->{$serie['key']} ?? 0);
                if (isset($serie['div'])) $val /= $serie['div'];
                $pct = min(1.0, max(0.0, $val / $serie['max']));
                $pts[] = [
                    $giX + (int)($giW * $idx / max(1,$n-1)),
                    $giY + $giH - (int)($giH * $pct),
                ];
            }
            for ($i = 0; $i < count($pts)-1; $i++) {
                // Tracer 2 fois pour épaisseur
                imageline($img, $pts[$i][0], $pts[$i][1],   $pts[$i+1][0], $pts[$i+1][1],   $serie['color']);
                imageline($img, $pts[$i][0], $pts[$i][1]+1, $pts[$i+1][0], $pts[$i+1][1]+1, $serie['color']);
            }
        }
    }

    // Légende graphique
    $lx = $giX; $ly = $gy + $gh - 16;
    foreach ([['Température (°C)',$cRed],['Humidité (%)',$cBlueTxt],['Gaz ÷ 10',$cOrange]] as $leg) {
        imagefilledrectangle($img, $lx, $ly-10, $lx+26, $ly+2, $leg[1]);
        imagettftext($img, $fs-1, 0, $lx+32, $ly+2, $cBlack, $fontR, $leg[0]);
        $lx += 160;
    }

    // ── Tableau ───────────────────────────────────────────────────────────
    $cols    = [70, 220, 140, 140, 140, 100, 130];
    $headers = ['ID', 'Date / Heure', 'Temp. (°C)', 'Humidité (%)', 'Gaz (ppm)', 'PIR', 'Niveau'];
    $ty      = $headH + $statsH + 24 + $chartH + 24;

    // En-tête colonnes (bleu foncé)
    imagefilledrectangle($img, $padX, $ty, $W-$padX, $ty+$tblHead, $cNavyBg);
    $cx = $padX + 8;
    foreach ($headers as $hi => $hdr) {
        imagettftext($img, $fs, 0, $cx, $ty+28, $cWhite, $fontB, $hdr);
        $cx += $cols[$hi];
    }
    // Séparateurs colonnes en-tête
    $cx = $padX;
    foreach ($cols as $cw) {
        $cx += $cw;
        imageline($img, $cx, $ty, $cx, $ty+$tblHead, $cNavy);
    }
    $ty += $tblHead;

    // Lignes de données
    foreach ($rows as $ri => $r) {
        $isCrit = $r->temperature >= 32 || $r->humidite >= 85 || $r->gaz >= 600;
        $isWarn = !$isCrit && ($r->temperature >= 28 || $r->humidite >= 75 || $r->gaz >= 400);

        $rowBg = $isCrit ? $cRedBg : ($isWarn ? $cOrgBg : ($ri%2===0 ? $cWhite : $cXLGray));
        imagefilledrectangle($img, $padX, $ty, $W-$padX, $ty+$rowH-1, $rowBg);
        imageline($img, $padX, $ty+$rowH-1, $W-$padX, $ty+$rowH-1, $cLGray);

        $tClr = $r->temperature >= 32 ? $cRed    : ($r->temperature >= 28 ? $cOrange : $cBlack);
        $hClr = $r->humidite    >= 85 ? $cRed    : ($r->humidite    >= 75 ? $cOrange : $cBlack);
        $gClr = $r->gaz         >= 600? $cRed    : ($r->gaz         >= 400? $cOrange : $cBlack);
        $pClr = $r->pir_detecte        ? $cRed    : $cGray;
        $nTxt = $isCrit ? 'CRITIQUE' : ($isWarn ? 'WARNING' : 'NORMAL');
        $nClr = $isCrit ? $cRed      : ($isWarn ? $cOrange  : $cGreen);

        $cells = [
            [(string)$r->id,                                                          $cGray],
            [\Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i:s'),            $cBlack],
            [number_format((float)($r->temperature ?? 0), 1),                         $tClr],
            [number_format((float)($r->humidite    ?? 0), 1),                         $hClr],
            [number_format((float)($r->gaz         ?? 0), 0),                         $gClr],
            [$r->pir_detecte ? 'OUI' : 'NON',                                         $pClr],
            [$nTxt,                                                                    $nClr],
        ];
        $cx = $padX + 8;
        foreach ($cells as $ci => [$txt, $clr]) {
            imagettftext($img, $fs, 0, $cx, $ty+26, $clr,
                ($ci === 6 || $ci === 5) ? $fontB : $fontR, $txt);
            $cx += $cols[$ci];
        }
        // Séparateurs colonnes
        $cx = $padX;
        foreach ($cols as $cw) {
            $cx += $cw;
            imageline($img, $cx, $ty, $cx, $ty+$rowH-1, $cLGray);
        }
        $ty += $rowH;
    }
    // Bordure extérieure tableau
    imagerectangle($img, $padX, $headH+$statsH+24+$chartH+24, $W-$padX, $ty, $cLGray);

    // ── Pied de page ──────────────────────────────────────────────────────
    imageline($img, $padX, $ty+14, $W-$padX, $ty+14, $cLGray);
    imagettftext($img, $fs-1, 0, $padX, $ty+36, $cGray, $fontR,
        'Plateforme de Surveillance  ·  Rapport automatique 72h  ·  Généré le '.now()->format('d/m/Y à H:i'));

    // ── Sortie PNG ────────────────────────────────────────────────────────
    $filename = 'rapport_72h_'.date('Y-m-d_H-i').'.png';
    return response()->stream(function() use ($img) {
        imagepng($img, null, 6); // compression 6/9 — bon ratio taille/qualité
        imagedestroy($img);
    }, 200, [
        'Content-Type'        => 'image/png',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"',
    ]);
});

Route::get('/rapports/print', function (\Illuminate\Http\Request $request) {
    if (!session('user')) return redirect('/login');
    $type  = $request->type  ?? 'mesures';
    $debut = $request->debut ?? now()->subDays(7)->toDateString();
    $fin   = $request->fin   ?? now()->toDateString();
    try {
        $q = DB::table($type)->whereBetween('created_at',[$debut.' 00:00:00',$fin.' 23:59:59'])
                    ->orderByDesc('created_at')->limit(2000);
        if ($type === 'mesures') $q->select(['id','temperature','humidite','gaz','pir_detecte','salle_id','created_at']);
        $rows = $q->get();
    } catch (\Exception $e) { $rows = collect(); }
    $data  = $rows->map(fn($r) => (array) $r)->toArray();
    $label = ['mesures'=>'Mesures capteurs','alertes'=>'Alertes','salles'=>'Salles','serveurs'=>'Serveurs'][$type] ?? $type;
    $title = $label.' — du '.$debut.' au '.$fin;
    return view('print_rapport', compact('data','title','type','debut','fin'));
});

Route::get('/rapports/export', function(\Illuminate\Http\Request $request) {
    if (!session('user')) return redirect('/login');

    $allowed = ['mesures','alertes','salles','serveurs'];
    $type    = in_array($request->type, $allowed) ? $request->type : 'mesures';
    $format  = $request->format ?? 'csv';
    $debut   = $request->debut  ?? now()->subDays(7)->toDateString();
    $fin     = $request->fin    ?? now()->toDateString();
    $niveau  = $request->niveau ?? '';
    $salle   = $request->salle_id ?? '';
    $tempMin = $request->temp_min ?? null;
    $tempMax = $request->temp_max ?? null;
    $humMin  = $request->hum_min  ?? null;
    $humMax  = $request->hum_max  ?? null;
    $limit   = min((int)($request->limit ?? 5000), 50000);

    try {
        $q = DB::table($type)
            ->whereBetween('created_at', [$debut.' 00:00:00', $fin.' 23:59:59'])
            ->orderByDesc('created_at')->limit($limit);
        if ($niveau && $type === 'alertes') $q->where('niveau', $niveau);
        if ($salle)   $q->where('salle_id', $salle);
        if ($type === 'mesures') {
            $q->select(['id','temperature','humidite','gaz','pir_detecte','salle_id','created_at']);
            if ($tempMin !== null) $q->where('temperature', '>=', (float)$tempMin);
            if ($tempMax !== null) $q->where('temperature', '<=', (float)$tempMax);
            if ($humMin  !== null) $q->where('humidite',    '>=', (float)$humMin);
            if ($humMax  !== null) $q->where('humidite',    '<=', (float)$humMax);
        }
        $rows = $q->get();
    } catch (\Exception $e) { $rows = collect(); }

    $data = $rows->map(fn($r) => (array)$r)->toArray();
    $fn   = $type.'_'.$debut.'_'.$fin;

    // Column-letter helper (A, B, …, Z, AA, AB …)
    $xlCol = function(int $n): string {
        $s = ''; $n++;
        while ($n > 0) { $n--; $s = chr(65 + ($n % 26)).$s; $n = intdiv($n, 26); }
        return $s;
    };
    $xlEsc = fn($v) => htmlspecialchars((string)$v, ENT_XML1|ENT_SUBSTITUTE, 'UTF-8');

    // ── JSON ──────────────────────────────────────────────────────────────
    if ($format === 'json') {
        return response()->json($data)
            ->header('Content-Disposition', "attachment; filename=\"{$fn}.json\"");
    }

    // ── XML ───────────────────────────────────────────────────────────────
    if ($format === 'xml') {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<export type=\"{$type}\" debut=\"{$debut}\" fin=\"{$fin}\" total=\"".count($data)."\">\n";
        foreach ($data as $row) {
            $xml .= "  <item>\n";
            foreach ($row as $k => $v) {
                $tag  = preg_replace('/[^a-z0-9_]/i','_',$k);
                $xml .= "    <{$tag}>".$xlEsc($v)."</{$tag}>\n";
            }
            $xml .= "  </item>\n";
        }
        $xml .= "</export>";
        return response($xml, 200, ['Content-Type'=>'application/xml','Content-Disposition'=>"attachment; filename=\"{$fn}.xml\""]);
    }

    // ── XLS (Excel 97-2003 TSV) ───────────────────────────────────────────
    if ($format === 'xls') {
        $out = "\xEF\xBB\xBF";
        if (!empty($data)) {
            $out .= implode("\t", array_keys($data[0]))."\r\n";
            foreach ($data as $row)
                $out .= implode("\t", array_map(fn($v)=>str_replace(["\t","\r","\n"],'',(string)$v),$row))."\r\n";
        }
        return response($out,200,['Content-Type'=>'application/vnd.ms-excel','Content-Disposition'=>"attachment; filename=\"{$fn}.xls\""]);
    }

    // ── XLSX (Office Open XML) ────────────────────────────────────────────
    if ($format === 'xlsx') {
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
        $zip = new \ZipArchive();
        $zip->open($tmp, \ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml',
            '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'</Types>');
        $zip->addFromString('_rels/.rels',
            '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>');
        $zip->addFromString('xl/workbook.xml',
            '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="'.htmlspecialchars($type).'" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels',
            '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'</Relationships>');

        $sd = '';
        if (!empty($data)) {
            $cols = array_keys($data[0]);
            $sd  .= '<row r="1">';
            foreach ($cols as $ci=>$col)
                $sd .= '<c r="'.$xlCol($ci).'1" t="inlineStr"><is><t>'.$xlEsc($col).'</t></is></c>';
            $sd .= '</row>';
            foreach ($data as $ri=>$row) {
                $rn  = $ri + 2;
                $sd .= '<row r="'.$rn.'">';
                foreach (array_values($row) as $ci=>$val)
                    $sd .= '<c r="'.$xlCol($ci).$rn.'" t="inlineStr"><is><t>'.$xlEsc((string)$val).'</t></is></c>';
                $sd .= '</row>';
            }
        }
        $zip->addFromString('xl/worksheets/sheet1.xml',
            '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetData>'.$sd.'</sheetData></worksheet>');
        $zip->close();

        $content = file_get_contents($tmp); @unlink($tmp);
        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$fn}.xlsx\"",
        ]);
    }

    // ── TXT ───────────────────────────────────────────────────────────────
    if ($format === 'txt') {
        $out  = "=== Plateforme de Surveillance — Export : {$type} ===\n";
        $out .= "Période  : {$debut} au {$fin}\n";
        $out .= "Généré   : ".date('d/m/Y H:i:s')."\n";
        $out .= "Total    : ".count($data)." enregistrements\n";
        $out .= str_repeat('─', 70)."\n\n";
        if (!empty($data)) {
            $cols = array_keys($data[0]);
            $w = array_fill(0, count($cols), 4);
            foreach ($cols as $i=>$c)  $w[$i] = max($w[$i], mb_strlen($c));
            foreach ($data  as $row)   foreach (array_values($row) as $i=>$v) $w[$i] = max($w[$i], mb_strlen((string)$v));
            foreach ($cols  as $i=>$c) $out .= str_pad($c, $w[$i]+2);
            $out .= "\n".str_repeat('─', array_sum($w)+count($w)*2)."\n";
            foreach ($data  as $row) {
                foreach (array_values($row) as $i=>$v) $out .= str_pad((string)$v, $w[$i]+2);
                $out .= "\n";
            }
        }
        $out .= "\n".str_repeat('─', 70)."\nPlateforme de Surveillance\n";
        return response($out,200,['Content-Type'=>'text/plain; charset=UTF-8','Content-Disposition'=>"attachment; filename=\"{$fn}.txt\""]);
    }

    // ── SQL ───────────────────────────────────────────────────────────────
    if ($format === 'sql') {
        $out  = "-- Plateforme de Surveillance — SQL Export\n-- Table   : {$type}\n";
        $out .= "-- Période : {$debut} au {$fin}\n-- Généré  : ".date('Y-m-d H:i:s')."\n-- Total   : ".count($data)." lignes\n\n";
        $out .= "SET NAMES 'utf8mb4';\nSET FOREIGN_KEY_CHECKS=0;\n\n";
        foreach ($data as $row) {
            $keys = implode(', ', array_map(fn($k)=>"`{$k}`",   array_keys($row)));
            $vals = implode(', ', array_map(fn($v)=>is_null($v)?'NULL':"'".addslashes((string)$v)."'", array_values($row)));
            $out .= "INSERT INTO `{$type}` ({$keys}) VALUES ({$vals});\n";
        }
        $out .= "\nSET FOREIGN_KEY_CHECKS=1;\n";
        return response($out,200,['Content-Type'=>'application/sql','Content-Disposition'=>"attachment; filename=\"{$fn}.sql\""]);
    }

    // ── DOCX (Word Open XML) ──────────────────────────────────────────────
    if ($format === 'docx') {
        $rows500 = array_slice($data, 0, 500);
        $tbl = '';
        if (!empty($rows500)) {
            $tbl .= '<w:tbl><w:tblPr><w:tblW w:w="9000" w:type="dxa"/><w:tblBorders>'
                 .  '<w:top w:val="single" w:sz="4" w:color="1e2f5a"/><w:left w:val="single" w:sz="4" w:color="1e2f5a"/>'
                 .  '<w:bottom w:val="single" w:sz="4" w:color="1e2f5a"/><w:right w:val="single" w:sz="4" w:color="1e2f5a"/>'
                 .  '<w:insideH w:val="single" w:sz="4" w:color="1e2f5a"/><w:insideV w:val="single" w:sz="4" w:color="1e2f5a"/>'
                 .  '</w:tblBorders></w:tblPr>';
            $tbl .= '<w:tr>';
            foreach (array_keys($rows500[0]) as $col)
                $tbl .= '<w:tc><w:tcPr><w:shd w:val="clear" w:fill="0e1a38"/></w:tcPr>'
                     .  '<w:p><w:r><w:rPr><w:b/><w:color w:val="33FF88"/><w:sz w:val="16"/></w:rPr>'
                     .  '<w:t>'.htmlspecialchars($col).'</w:t></w:r></w:p></w:tc>';
            $tbl .= '</w:tr>';
            foreach ($rows500 as $row) {
                $tbl .= '<w:tr>';
                foreach ($row as $v)
                    $tbl .= '<w:tc><w:p><w:r><w:rPr><w:sz w:val="16"/></w:rPr>'
                         .  '<w:t xml:space="preserve">'.htmlspecialchars((string)$v).'</w:t></w:r></w:p></w:tc>';
                $tbl .= '</w:tr>';
            }
            $tbl .= '</w:tbl>';
        }
        $doc = '<?xml version="1.0" encoding="UTF-8"?>'
             . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
             . '<w:body>'
             . '<w:p><w:pPr><w:jc w:val="center"/></w:pPr>'
             . '<w:r><w:rPr><w:b/><w:color w:val="33FF88"/><w:sz w:val="32"/></w:rPr>'
             . '<w:t>Rapport — '.htmlspecialchars($type).'</w:t></w:r></w:p>'
             . '<w:p><w:r><w:rPr><w:color w:val="888888"/><w:sz w:val="18"/></w:rPr>'
             . '<w:t>Période : '.htmlspecialchars($debut).' au '.htmlspecialchars($fin).' | Total : '.count($data).' | Généré : '.date('d/m/Y H:i').'</w:t></w:r></w:p>'
             . '<w:p/>'.$tbl.'<w:p/>'
             . '<w:p><w:r><w:rPr><w:color w:val="555555"/><w:sz w:val="14"/></w:rPr>'
             . '<w:t>Plateforme de Surveillance</w:t></w:r></w:p>'
             . '</w:body></w:document>';

        $tmp = tempnam(sys_get_temp_dir(), 'docx_');
        $zip = new \ZipArchive();
        $zip->open($tmp, \ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml',
            '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
            .'</Types>');
        $zip->addFromString('_rels/.rels',
            '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
            .'</Relationships>');
        $zip->addFromString('word/document.xml', $doc);
        $zip->addFromString('word/_rels/document.xml.rels',
            '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"></Relationships>');
        $zip->close();

        $content = file_get_contents($tmp); @unlink($tmp);
        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => "attachment; filename=\"{$fn}.docx\"",
        ]);
    }

    // ── HTML (downloadable styled report) ────────────────────────────────
    if ($format === 'html') {
        $hdr  = !empty($data) ? '<tr>'.implode('',array_map(fn($k)=>'<th>'.htmlspecialchars($k).'</th>',array_keys($data[0]))).'</tr>' : '';
        $body = implode('',array_map(fn($row)=>'<tr>'.implode('',array_map(fn($v)=>'<td>'.htmlspecialchars((string)$v).'</td>',$row)).'</tr>',$data));
        $html = '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Rapport '.htmlspecialchars($type).'</title>'
              . '<style>*{box-sizing:border-box;margin:0;padding:0}body{font-family:Arial,sans-serif;background:#060d1f;color:#fff;padding:20px}'
              . '.hd{background:#0e1a38;border:1px solid #1e2f5a;border-radius:12px;padding:18px 20px;margin-bottom:18px}'
              . '.hd h1{color:#33ff88;font-size:18px}.hd p{color:#888;font-size:11px;margin-top:5px}'
              . 'table{width:100%;border-collapse:collapse}thead th{background:#0e1a38;border:1px solid #1e2f5a;padding:8px 10px;text-align:left;font-size:10px;color:#aaa;text-transform:uppercase}'
              . 'tbody td{padding:7px 10px;border:1px solid #1e2f5a;font-size:11px}tbody tr:nth-child(even) td{background:rgba(30,47,90,.25)}'
              . '.ft{margin-top:14px;text-align:center;color:#555;font-size:10px}</style></head><body>'
              . '<div class="hd"><h1>&#128202; Rapport — '.htmlspecialchars($type).'</h1>'
              . '<p>Période : '.htmlspecialchars($debut).' au '.htmlspecialchars($fin).' &nbsp;·&nbsp; '.count($data).' enregistrements &nbsp;·&nbsp; Généré le '.date('d/m/Y H:i').'</p></div>'
              . (!empty($data) ? '<table><thead>'.$hdr.'</thead><tbody>'.$body.'</tbody></table>' : '<p style="text-align:center;padding:40px;color:#555">Aucune donnée.</p>')
              . '<div class="ft">Plateforme de Surveillance &nbsp;·&nbsp; '.date('d/m/Y H:i').'</div></body></html>';
        return response($html,200,['Content-Type'=>'text/html;charset=UTF-8','Content-Disposition'=>"attachment; filename=\"{$fn}.html\""]);
    }

    // ── ZIP (bundle: CSV + JSON + HTML + README) ──────────────────────────
    if ($format === 'zip') {
        $csv = !empty($data) ? implode(',',array_keys($data[0]))."\n" : '';
        foreach ($data as $row)
            $csv .= implode(',',array_map(fn($v)=>'"'.str_replace('"','""',(string)$v).'"',$row))."\n";

        $hdr2  = !empty($data) ? '<tr>'.implode('',array_map(fn($k)=>'<th>'.htmlspecialchars($k).'</th>',array_keys($data[0]))).'</tr>' : '';
        $body2 = implode('',array_map(fn($r)=>'<tr>'.implode('',array_map(fn($v)=>'<td>'.htmlspecialchars((string)$v).'</td>',$r)).'</tr>',$data));
        $html2 = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{font-family:Arial;background:#060d1f;color:#fff;padding:16px}table{width:100%;border-collapse:collapse}th{background:#0e1a38;border:1px solid #1e2f5a;padding:7px;font-size:10px;color:#33ff88}td{border:1px solid #1e2f5a;padding:6px;font-size:10px}</style></head>'
               . '<body><h2 style="color:#33ff88">'.htmlspecialchars($type).' — '.$debut.' au '.$fin.'</h2>'
               . '<table><thead>'.$hdr2.'</thead><tbody>'.$body2.'</tbody></table></body></html>';

        $tmp = tempnam(sys_get_temp_dir(), 'zip_');
        $zip = new \ZipArchive();
        $zip->open($tmp, \ZipArchive::OVERWRITE);
        $zip->addFromString("{$fn}.csv",  $csv);
        $zip->addFromString("{$fn}.json", json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        $zip->addFromString("{$fn}.html", $html2);
        $zip->addFromString('README.txt', "Plateforme de Surveillance — Bundle Export\nType: {$type}\nPeriode: {$debut} - {$fin}\nTotal: ".count($data)." records\nGenerated: ".date('Y-m-d H:i:s')."\n");
        $zip->close();

        $content = file_get_contents($tmp); @unlink($tmp);
        return response($content,200,['Content-Type'=>'application/zip','Content-Disposition'=>"attachment; filename=\"{$fn}_bundle.zip\""]);
    }

    // ── CSV (default) ─────────────────────────────────────────────────────
    $csv = '';
    if (!empty($data)) {
        $csv .= implode(',', array_keys($data[0]))."\n";
        foreach ($data as $row)
            $csv .= implode(',', array_map(fn($v)=>'"'.str_replace('"','""',(string)$v).'"',$row))."\n";
    }
    return response($csv,200,['Content-Type'=>'text/csv','Content-Disposition'=>"attachment; filename=\"{$fn}.csv\""]);
});

// ── Backup complet ZIP ─────────────────────────────────────────────────────
Route::get('/rapports/backup', function () {
    if (!session('user')) return redirect('/login');

    $tmp = tempnam(sys_get_temp_dir(), 'bkp_');
    $zip = new \ZipArchive();
    $zip->open($tmp, \ZipArchive::OVERWRITE);

    $tables = ['mesures', 'alertes', 'salles', 'serveurs'];
    $today  = date('Y-m-d');
    $summary = "=== Plateforme de Surveillance — Backup complet ===\nDate: ".date('Y-m-d H:i:s')."\n\n";

    foreach ($tables as $tbl) {
        try {
            $rows = DB::table($tbl)->orderByDesc('created_at')->limit(20000)->get();
            $data = $rows->map(fn($r)=>(array)$r)->toArray();
            $summary .= ucfirst($tbl).": ".count($data)." enregistrements\n";

            $csv = !empty($data) ? implode(',',array_keys($data[0]))."\n" : '';
            foreach ($data as $row)
                $csv .= implode(',',array_map(fn($v)=>'"'.str_replace('"','""',(string)$v).'"',$row))."\n";
            $zip->addFromString("{$tbl}.csv", $csv);
            $zip->addFromString("{$tbl}.json", json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

            $sql = "-- Table: {$tbl} | ".count($data)." rows\nSET NAMES 'utf8mb4';\n";
            foreach ($data as $row) {
                $k = implode(',',array_map(fn($k)=>"`{$k}`",array_keys($row)));
                $v = implode(',',array_map(fn($v)=>is_null($v)?'NULL':"'".addslashes((string)$v)."'",array_values($row)));
                $sql .= "INSERT INTO `{$tbl}` ({$k}) VALUES ({$v});\n";
            }
            $zip->addFromString("{$tbl}.sql", $sql);
        } catch (\Exception $e) {
            $zip->addFromString("{$tbl}_error.txt", "Erreur: ".$e->getMessage());
            $summary .= ucfirst($tbl).": erreur — ".$e->getMessage()."\n";
        }
    }

    try {
        $users = DB::table('users')->select('id','prenom','nom','email','telephone','role','validation_status','pays','region','profession','created_at')->get();
        $ud = $users->map(fn($r)=>(array)$r)->toArray();
        $summary .= "Utilisateurs: ".count($ud)." (sans mots de passe)\n";
        $csv = !empty($ud) ? implode(',',array_keys($ud[0]))."\n" : '';
        foreach ($ud as $row)
            $csv .= implode(',',array_map(fn($v)=>'"'.str_replace('"','""',(string)$v).'"',$row))."\n";
        $zip->addFromString('utilisateurs.csv', $csv);
    } catch (\Exception $e) {}

    $zip->addFromString('BACKUP_INFO.txt', $summary);
    $zip->close();

    $content = file_get_contents($tmp); @unlink($tmp);
    return response($content, 200, [
        'Content-Type'        => 'application/zip',
        'Content-Disposition' => "attachment; filename=\"supserver_backup_{$today}.zip\"",
    ]);
});
