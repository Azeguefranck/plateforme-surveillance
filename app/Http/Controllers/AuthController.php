<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Base de données inaccessible. Démarrez MySQL : sudo /opt/lampp/lampp startmysql');
        }

        $request->validate([
            'nom'      => 'required|string|max:100',
            'prenom'   => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ], [
            'nom.required'          => 'Le nom est obligatoire.',
            'prenom.required'       => 'Le prénom est obligatoire.',
            'email.required'        => 'L\'adresse email est obligatoire.',
            'email.email'           => 'L\'adresse email n\'est pas valide.',
            'email.unique'          => 'Cette adresse email est déjà utilisée.',
            'password.required'     => 'Le mot de passe est obligatoire.',
            'password.confirmed'    => 'Les mots de passe ne correspondent pas.',
            'password.min'          => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        DB::table('users')->insert([
            'name'             => $request->prenom . ' ' . $request->nom,
            'nom'              => $request->nom,
            'prenom'           => $request->prenom,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'role'             => 'utilisateur',
            'validation_status'=> 'valide',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return redirect('/login')->with('success', 'Compte créé avec succès. Vous pouvez maintenant vous authentifier.');
    }



    public function login(Request $request)
    {
        try {
            $user = DB::table('users')->where('email', $request->email)->first();
        } catch (\Exception $e) {
            return back()->with('error', '⚠️ Base de données inaccessible. Démarrez MySQL avec : sudo /opt/lampp/lampp startmysql');
        }

        if (!$user) {
            return back()->with('error', 'Aucun compte trouvé avec cet email.');
        }

        if (!Hash::check($request->mot_de_passe, $user->password)) {
            return back()->with('error', 'Mot de passe incorrect.');
        }

        if (($user->validation_status ?? '') === 'en_attente') {
            return back()->with('error', 'Votre compte est en attente de validation.');
        }

        if (($user->validation_status ?? '') === 'refuse') {
            return back()->with('error', 'Votre accès a été refusé. Contactez l\'administrateur.');
        }

        if (($user->validation_status ?? '') === 'bloque') {
            return back()->with('error', 'Votre compte a été bloqué. Contactez l\'administrateur.');
        }

        session(['user' => $user]);

        return redirect('/dashboard');
    }

}