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
            'nom'      => 'required|string|max:100',
            'prenom'   => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'telephone'=> 'required|string|max:30',
            'password' => 'required|confirmed|min:8',
            'pays'     => 'required|string',
        ]);

        // Photo de profil
        $photoPath = null;
        if ($request->hasFile('photo_profil')) {
            $photoPath = $request->file('photo_profil')->store('photos', 'public');
        }

        DB::table('users')->insert([
            'name'             => $request->prenom . ' ' . $request->nom,
            'nom'              => $request->nom,
            'prenom'           => $request->prenom,
            'sexe'             => $request->sexe,
            'date_naissance'   => $request->date_naissance ?: null,
            'nationalite'      => $request->nationalite ?: $request->pays,
            'statut_matrimonial'=> $request->statut_matrimonial,
            'email'            => $request->email,
            'telephone'        => $request->telephone,
            'indicatif_tel'    => $request->indicatif_tel,
            'pays'             => $request->pays,
            'iso_pays'         => $request->iso_pays,
            'region'           => $request->region,
            'departement'      => $request->departement,
            'arrondissement'   => $request->arrondissement,
            'ville_residence'  => $request->ville_residence,
            'quartier'         => $request->quartier,
            'adresse'          => $request->adresse,
            'profession'       => $request->profession,
            'organisation'     => $request->organisation,
            'role'             => $request->role ?? 'utilisateur',
            'photo_profil'     => $photoPath,
            'password'         => Hash::make($request->password),
            'validation_status'=> 'en_attente',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        try {
            Mail::raw(
                "Bonjour " . $request->prenom . " " . $request->nom . ",\n\n" .
                "Votre demande d'inscription a bien été reçue.\n\n" .
                "Informations enregistrées :\n" .
                "• Email      : " . $request->email . "\n" .
                "• Téléphone  : " . $request->telephone . "\n" .
                "• Pays       : " . $request->pays . "\n\n" .
                "⏳ Votre compte est en attente de validation par l'administrateur.\n" .
                "Vous recevrez un email dès que votre compte sera activé.\n\n" .
                "SupServer — Plateforme Surveillance IoT",
                function ($message) use ($request) {
                    $message->to($request->email)->subject('Inscription reçue — En attente de validation');
                }
            );
        } catch (\Exception $e) {}

        return redirect('/login')->with('success', 'Inscription envoyée. En attente de validation administrateur.');
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