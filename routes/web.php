<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ParametresController;
use App\Http\Controllers\ServeursController;
use App\Http\Controllers\SallesController;



Route::view('/','accueil');
Route::view('/accueil','accueil');
Route::view('/dashboard','dashboard');
Route::view('/register','register');
Route::view('/login','login');



Route::post('/register-user', [AuthController::class, 'register']);
Route::post('/login-user',    [AuthController::class, 'login']);



Route::get('/valider/{id}', function ($id) {

    $user = DB::table('users')->where('id', $id)->first();
    if (!$user) return response('Utilisateur introuvable', 404);

    DB::table('users')->where('id', $id)->update(['validation_status' => 'valide']);

    try {
        Mail::raw(
            "Bonjour {$user->prenom} {$user->nom},\n\n" .
            "✅ Votre compte SupServer a été VALIDÉ par l'administrateur.\n\n" .
            "Vous pouvez maintenant vous connecter à la plateforme :\n" .
            url('/login') . "\n\n" .
            "Email     : {$user->email}\n\n" .
            "Bienvenue sur la plateforme de surveillance IoT !\n\n" .
            "SupServer — Plateforme Surveillance IoT",
            function ($mail) use ($user) {
                $mail->to($user->email)->subject('✅ Compte activé — SupServer');
            }
        );
    } catch (\Exception $e) {}

    return response('<html><body style="font-family:Arial;background:#0b1120;color:#33ff88;text-align:center;padding:60px">
        <h1>✅ Compte validé</h1>
        <p style="color:#fff">' . $user->prenom . ' ' . $user->nom . ' peut maintenant se connecter.</p>
        <p style="color:#aaa">Email de confirmation envoyé.</p>
    </body></html>');

});



Route::get('/bloquer/{id}', function ($id) {

    $user = DB::table('users')->where('id', $id)->first();
    if (!$user) return response('Utilisateur introuvable', 404);

    DB::table('users')->where('id', $id)->update(['validation_status' => 'bloque']);

    try {
        Mail::raw(
            "Bonjour {$user->prenom} {$user->nom},\n\n" .
            "🚫 Votre compte SupServer a été SUSPENDU par l'administrateur.\n\n" .
            "Pour toute demande de réactivation, contactez l'administrateur.\n\n" .
            "SupServer — Plateforme Surveillance IoT",
            function ($mail) use ($user) {
                $mail->to($user->email)->subject('🚫 Compte suspendu — SupServer');
            }
        );
    } catch (\Exception $e) {}

    return response('<html><body style="font-family:Arial;background:#0b1120;color:#ff5733;text-align:center;padding:60px">
        <h1>🚫 Compte suspendu</h1>
        <p style="color:#fff">' . $user->prenom . ' ' . $user->nom . ' ne peut plus se connecter.</p>
    </body></html>');

});



Route::get('/refuser/{id}', function ($id) {

    $user = DB::table('users')->where('id', $id)->first();
    if (!$user) return response('Utilisateur introuvable', 404);

    DB::table('users')->where('id', $id)->update(['validation_status' => 'refuse']);

    try {
        Mail::raw(
            "Bonjour {$user->prenom} {$user->nom},\n\n" .
            "❌ Votre demande d'inscription sur SupServer a été REFUSÉE.\n\n" .
            "Votre demande d'accès à la plateforme de surveillance n'a pas été approuvée.\n\n" .
            "Si vous pensez qu'il s'agit d'une erreur, veuillez contacter l'administrateur.\n\n" .
            "SupServer — Plateforme Surveillance IoT",
            function ($mail) use ($user) {
                $mail->to($user->email)->subject('❌ Inscription refusée — SupServer');
            }
        );
    } catch (\Exception $e) {}

    return response('<html><body style="font-family:Arial;background:#0b1120;color:#ff5733;text-align:center;padding:60px">
        <h1>❌ Compte refusé</h1>
        <p style="color:#fff">' . $user->prenom . ' ' . $user->nom . ' ne peut pas accéder à la plateforme.</p>
        <p style="color:#aaa">Email de notification envoyé.</p>
    </body></html>');

});



Route::get('/attente/{id}', function ($id) {

    $user = DB::table('users')->where('id', $id)->first();
    if (!$user) return response('Utilisateur introuvable', 404);

    DB::table('users')->where('id', $id)->update(['validation_status' => 'en_attente']);

    try {
        Mail::raw(
            "Bonjour {$user->prenom} {$user->nom},\n\n" .
            "⏳ Votre compte SupServer a été remis EN ATTENTE de validation.\n\n" .
            "Vous recevrez un email dès que votre compte sera activé.\n\n" .
            "SupServer — Plateforme Surveillance IoT",
            function ($mail) use ($user) {
                $mail->to($user->email)->subject('⏳ Compte en attente — SupServer');
            }
        );
    } catch (\Exception $e) {}

    return response('<html><body style="font-family:Arial;background:#0b1120;color:#ffd633;text-align:center;padding:60px">
        <h1>⏳ Compte remis en attente</h1>
        <p style="color:#fff">' . $user->prenom . ' ' . $user->nom . '</p>
    </body></html>');

});



Route::view('/surveillance','surveillance');
Route::view('/alertes','alertes');
Route::view('/historique','historique');
Route::view('/statistiques','statistiques');
Route::view('/sms','sms');
Route::view('/sms-gsm','sms-gsm');
Route::view('/anomalies','anomalies');
Route::get('/profil',           [ProfilController::class, 'show']);
Route::post('/profil/update',   [ProfilController::class, 'update']);
Route::post('/profil/password', [ProfilController::class, 'changePassword']);
Route::post('/profil/photo',    [ProfilController::class, 'uploadPhoto']);
Route::view('/utilisateurs','utilisateurs');
Route::view('/cameras-ip','cameras-ip');
Route::get('/salles',          [SallesController::class, 'index']);
Route::post('/salles',         [SallesController::class, 'store']);
Route::delete('/salles/{id}',  [SallesController::class, 'destroy']);
Route::post('/salles/{id}',    [SallesController::class, 'update']);

Route::get('/serveurs',        [ServeursController::class, 'index']);
Route::post('/serveurs',       [ServeursController::class, 'store']);
Route::delete('/serveurs/{id}',[ServeursController::class, 'destroy']);
Route::post('/serveurs/{id}',  [ServeursController::class, 'update']);

Route::redirect('/serveurs-web', '/serveurs', 301);
Route::redirect('/serveurs-bd',  '/serveurs', 301);
Route::get('/parametres',         [ParametresController::class, 'show']);
Route::post('/parametres/seuils', [ParametresController::class, 'saveSeuils']);
Route::view('/rapports','rapports');

// ── AJAX : modifier statut utilisateur ────────────────────────────────────
Route::post('/user/{id}/statut', function (\Illuminate\Http\Request $request, $id) {
    if (!session('user')) return response()->json(['error' => 'Non autorisé'], 401);
    $status  = $request->status;
    $allowed = ['valide', 'refuse', 'bloque', 'en_attente'];
    if (!in_array($status, $allowed))
        return response()->json(['error' => 'Statut invalide'], 422);

    $target = DB::table('users')->where('id', $id)->first();
    if (!$target) return response()->json(['error' => 'Utilisateur introuvable'], 404);

    DB::table('users')->where('id', $id)->update(['validation_status' => $status]);

    $subj = ['valide'=>'✅ Compte activé','refuse'=>'❌ Inscription refusée','bloque'=>'🚫 Compte suspendu','en_attente'=>'⏳ Compte en attente'];
    $body = ['valide'=>'✅ Votre compte a été VALIDÉ. Connectez-vous sur : '.url('/login'),
             'refuse'=>'❌ Votre inscription a été REFUSÉE.',
             'bloque'=>'🚫 Votre compte a été SUSPENDU par l\'administrateur.',
             'en_attente'=>'⏳ Votre compte a été remis EN ATTENTE de validation.'];
    try {
        Mail::raw("Bonjour {$target->prenom} {$target->nom},\n\n{$body[$status]}\n\nSupServer — Surveillance IoT",
            fn($m) => $m->to($target->email)->subject($subj[$status].' — SupServer'));
    } catch (\Exception $e) {}

    $labels = ['valide'=>'Validé','refuse'=>'Refusé','bloque'=>'Bloqué','en_attente'=>'En attente'];
    return response()->json(['success'=>true,'status'=>$status,'label'=>$labels[$status],'message'=>"Compte {$labels[$status]} avec succès."]);
});

// ── AJAX : supprimer utilisateur ───────────────────────────────────────────
Route::delete('/user/{id}', function ($id) {
    if (!session('user')) return response()->json(['error' => 'Non autorisé'], 401);
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

// ── Impression / PDF ───────────────────────────────────────────────────────
Route::get('/rapports/print', function (\Illuminate\Http\Request $request) {
    if (!session('user')) return redirect('/login');
    $type  = $request->type  ?? 'mesures';
    $debut = $request->debut ?? now()->subDays(7)->toDateString();
    $fin   = $request->fin   ?? now()->toDateString();
    try {
        $rows = DB::table($type)->whereBetween('created_at',[$debut.' 00:00:00',$fin.' 23:59:59'])
                    ->orderByDesc('created_at')->limit(2000)->get();
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
        $out  = "=== SupServer — Export : {$type} ===\n";
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
        $out .= "\n".str_repeat('─', 70)."\nSupServer — Plateforme de Surveillance IoT\n";
        return response($out,200,['Content-Type'=>'text/plain; charset=UTF-8','Content-Disposition'=>"attachment; filename=\"{$fn}.txt\""]);
    }

    // ── SQL ───────────────────────────────────────────────────────────────
    if ($format === 'sql') {
        $out  = "-- SupServer SQL Export\n-- Table   : {$type}\n";
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
             . '<w:t>Rapport SupServer — '.htmlspecialchars($type).'</w:t></w:r></w:p>'
             . '<w:p><w:r><w:rPr><w:color w:val="888888"/><w:sz w:val="18"/></w:rPr>'
             . '<w:t>Période : '.htmlspecialchars($debut).' au '.htmlspecialchars($fin).' | Total : '.count($data).' | Généré : '.date('d/m/Y H:i').'</w:t></w:r></w:p>'
             . '<w:p/>'.$tbl.'<w:p/>'
             . '<w:p><w:r><w:rPr><w:color w:val="555555"/><w:sz w:val="14"/></w:rPr>'
             . '<w:t>SupServer — Plateforme de Surveillance IoT</w:t></w:r></w:p>'
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
              . '<div class="ft">SupServer — Plateforme IoT &nbsp;·&nbsp; '.date('d/m/Y H:i').'</div></body></html>';
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
        $zip->addFromString('README.txt', "SupServer Bundle Export\nType: {$type}\nPeriode: {$debut} - {$fin}\nTotal: ".count($data)." records\nGenerated: ".date('Y-m-d H:i:s')."\n");
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
    $summary = "=== SupServer Full Backup ===\nDate: ".date('Y-m-d H:i:s')."\n\n";

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
