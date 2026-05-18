<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{

public function register(Request $request)
{

$request->validate([

'nom'=>'required',
'prenom'=>'required',
'email'=>'required|email',
'telephone'=>'required',
'password'=>'required|confirmed|min:6'

]);

if(
$request->role == "admin" &&
$request->email != env('ADMIN_EMAIL')
){

return back()->with(
'error',
'Seul l administrateur principal peut créer un compte administrateur.'
);

}

$password = $request->password;

$id = DB::table('utilisateurs')->insertGetId([

'nom'=>$request->nom,
'prenom'=>$request->prenom,
'email'=>$request->email,
'telephone'=>$request->telephone,
'profession'=>$request->profession,
'role'=>$request->role,
'pays'=>$request->pays,
'region'=>$request->region,
'departement'=>$request->departement,
'arrondissement'=>$request->arrondissement,
'ville_residence'=>$request->ville_residence,
'statut_matrimonial'=>$request->statut_matrimonial,
'mot_de_passe'=>Hash::make($password),
'statut'=>'EN_ATTENTE',
'created_at'=>now(),
'updated_at'=>now()

]);

$app = env('APP_URL');

$validate = $app.'/admin/validate/'.$id;
$reject   = $app.'/admin/reject/'.$id;
$pending  = $app.'/admin/pending/'.$id;

Mail::html('

<h2>Nouvelle inscription</h2>

<p><b>Nom:</b> '.$request->nom.'</p>

<p><b>Prénom:</b> '.$request->prenom.'</p>

<p><b>Email:</b> '.$request->email.'</p>

<p><b>Téléphone:</b> '.$request->telephone.'</p>

<p><b>Pays:</b> '.$request->pays.'</p>

<p><b>Région:</b> '.$request->region.'</p>

<p><b>Département:</b> '.$request->departement.'</p>

<p><b>Arrondissement:</b> '.$request->arrondissement.'</p>

<p><b>Ville:</b> '.$request->ville_residence.'</p>

<p><b>Profession:</b> '.$request->profession.'</p>

<p><b>Rôle:</b> '.$request->role.'</p>

<br><br>

<a href="'.$validate.'"
style="
background:green;
padding:16px 24px;
color:white;
text-decoration:none;
border-radius:8px;
display:inline-block;
margin:8px;
">

VALIDER

</a>

<a href="'.$reject.'"
style="
background:red;
padding:16px 24px;
color:white;
text-decoration:none;
border-radius:8px;
display:inline-block;
margin:8px;
">

REFUSER

</a>

<a href="'.$pending.'"
style="
background:orange;
padding:16px 24px;
color:white;
text-decoration:none;
border-radius:8px;
display:inline-block;
margin:8px;
">

EN ATTENTE

</a>

',

function($message){

$message
->to(env('ADMIN_EMAIL'))
->subject('Nouvelle demande inscription');

});

Mail::raw(

"Bonjour ".$request->nom."

Votre demande a été envoyée.

Vous devez attendre validation de l administrateur.",

function($message) use($request){

$message
->to($request->email)
->subject('Demande inscription');

}

);

return redirect('/login')
->with(
'success',
'Demande envoyée avec succès.'
);

}

public function login(Request $request)
{

$user = DB::table('utilisateurs')
->where('email',$request->email)
->first();

if(!$user){

return back()->with(
'error',
'Utilisateur introuvable'
);

}

if($user->statut == 'EN_ATTENTE'){

return back()->with(
'error',
'Compte en attente validation'
);

}

if($user->statut == 'REFUSE'){

return back()->with(
'error',
'Compte refusé'
);

}

if(!Hash::check(
$request->mot_de_passe,
$user->mot_de_passe
)){

return back()->with(
'error',
'Mot de passe incorrect'
);

}

session(['user'=>$user]);

return redirect('/dashboard');

}

}
