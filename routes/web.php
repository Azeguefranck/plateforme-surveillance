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

Route::get('/admin/validate/{id}',function($id){

DB::table('utilisateurs')
->where('id',$id)
->update([
'statut'=>'VALIDE'
]);

return '

<h1 style="
color:green;
text-align:center;
margin-top:100px;
font-family:Arial;
">

UTILISATEUR VALIDÉ

</h1>

';

});

Route::get('/admin/reject/{id}',function($id){

DB::table('utilisateurs')
->where('id',$id)
->update([
'statut'=>'REFUSE'
]);

return '

<h1 style="
color:red;
text-align:center;
margin-top:100px;
font-family:Arial;
">

UTILISATEUR REFUSÉ

</h1>

';

});

Route::get('/admin/pending/{id}',function($id){

DB::table('utilisateurs')
->where('id',$id)
->update([
'statut'=>'EN_ATTENTE'
]);

return '

<h1 style="
color:orange;
text-align:center;
margin-top:100px;
font-family:Arial;
">

UTILISATEUR EN ATTENTE

</h1>

';

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
