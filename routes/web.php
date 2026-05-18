<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;



Route::view('/','accueil');

Route::view('/accueil','accueil');

Route::view('/dashboard','dashboard');

Route::view('/register','register');

Route::view('/login','login');



Route::post('/register-user',
[AuthController::class,'register']);

Route::post('/login-user',
[AuthController::class,'login']);



Route::get('/valider/{id}', function($id){

    DB::table('users')
    ->where('id',$id)
    ->update([
        'validation_status'=>'valide'
    ]);

    return back();

});



Route::get('/bloquer/{id}', function($id){

    DB::table('users')
    ->where('id',$id)
    ->update([
        'validation_status'=>'bloque'
    ]);

    return back();

});



Route::get('/attente/{id}', function($id){

    DB::table('users')
    ->where('id',$id)
    ->update([
        'validation_status'=>'en_attente'
    ]);

    return back();

});



Route::view('/surveillance','surveillance');
Route::view('/alertes','alertes');
Route::view('/historique','historique');
Route::view('/statistiques','statistiques');
Route::view('/sms','sms');
Route::view('/sms-gsm','sms-gsm');
Route::view('/anomalies','anomalies');
Route::view('/profil','profil');
Route::view('/utilisateurs','utilisateurs');
Route::view('/cameras-ip','cameras-ip');
Route::view('/salles','salles');
Route::view('/serveurs-web','serveurs_web');
Route::view('/serveurs-bd','serveurs_bd');
Route::view('/parametres','parametres');
Route::view('/rapports','rapports');