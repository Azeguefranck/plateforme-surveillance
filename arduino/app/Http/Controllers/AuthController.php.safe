<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public function register(Request $request)
    {

        $request->validate([

            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required|email',
            'telephone' => 'required',
            'password' => 'required|confirmed|min:6'

        ]);

        $password = $request->password;

        DB::table('users')->insert([

            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'profession' => $request->profession,
            'role' => $request->role,
            'validation_status' => 'en_attente',
            'password' => Hash::make($password),

            'created_at' => now(),
            'updated_at' => now()

        ]);

        Mail::raw(

            "Bonjour ".$request->nom."

Votre compte a été créé avec succès.

Vous devez attendre la validation de l'administrateur.",

            function($message) use($request){

                $message
                ->to($request->email)
                ->subject('Création compte');

            }

        );

        return redirect('/login')
        ->with(
            'success',
            'Compte créé avec succès.'
        );

    }



    public function login(Request $request)
    {

        $user = DB::table('users')
        ->where('email', $request->email)
        ->first();

        if(!$user){

            return back()->with(
                'error',
                'Utilisateur introuvable'
            );

        }


        if($user->validation_status == 'bloque'){

            return back()->with(
                'error',
                'Compte bloqué'
            );

        }


        if($user->validation_status == 'en_attente'){

            return back()->with(
                'error',
                'Compte en attente de validation'
            );

        }


        if(!Hash::check(
            $request->mot_de_passe,
            $user->password
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