<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;



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
Route::view('/salles','salles');
Route::view('/serveurs-web','serveurs_web');
Route::view('/serveurs-bd','serveurs_bd');
Route::view('/parametres','parametres');
Route::view('/rapports','rapports');
