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
    $user = session('user');
    if (!$user) return redirect('/login');

    $type   = $request->type   ?? 'mesures';
    $format = $request->format ?? 'csv';
    $debut  = $request->debut  ?? now()->subDays(7)->toDateString();
    $fin    = $request->fin    ?? now()->toDateString();

    try {
        $rows = DB::table($type)
            ->whereBetween('created_at', [$debut.' 00:00:00', $fin.' 23:59:59'])
            ->orderByDesc('created_at')
            ->limit(5000)
            ->get();
    } catch (\Exception $e) {
        $rows = collect();
    }

    $data = $rows->map(fn($r) => (array) $r)->toArray();

    $fn = $type.'_'.$debut.'_'.$fin;

    if ($format === 'json') {
        return response()->json($data)
            ->header('Content-Disposition', "attachment; filename=\"{$fn}.json\"");
    }

    if ($format === 'xml') {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<export type=\"{$type}\" debut=\"{$debut}\" fin=\"{$fin}\">\n";
        foreach ($data as $row) {
            $xml .= "  <item>\n";
            foreach ($row as $k => $v) {
                $tag = preg_replace('/[^a-z0-9_]/i','_',$k);
                $xml .= "    <{$tag}>".htmlspecialchars((string)$v)."</{$tag}>\n";
            }
            $xml .= "  </item>\n";
        }
        $xml .= "</export>";
        return response($xml, 200, ['Content-Type'=>'application/xml','Content-Disposition'=>"attachment; filename=\"{$fn}.xml\""]);
    }

    if ($format === 'xls') {
        $xls = "\xEF\xBB\xBF";
        if (!empty($data)) {
            $xls .= implode("\t", array_keys($data[0]))."\r\n";
            foreach ($data as $row) {
                $xls .= implode("\t", array_map(fn($v) => str_replace(["\t","\r","\n"],'',(string)$v), $row))."\r\n";
            }
        }
        return response($xls, 200, ['Content-Type'=>'application/vnd.ms-excel','Content-Disposition'=>"attachment; filename=\"{$fn}.xls\""]);
    }

    $csv = '';
    if (!empty($data)) {
        $csv .= implode(',', array_keys($data[0]))."\n";
        foreach ($data as $row) {
            $csv .= implode(',', array_map(fn($v) => '"'.str_replace('"','""',(string)$v).'"', $row))."\n";
        }
    }
    return response($csv, 200, ['Content-Type'=>'text/csv','Content-Disposition'=>"attachment; filename=\"{$fn}.csv\""]);
});
