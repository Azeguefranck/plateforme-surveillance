<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function show()
    {
        $user = session('user');
        if (!$user) return redirect('/login');
        if (is_array($user)) $user = (object) $user;

        $alertes = [];
        try {
            $alertes = DB::table('alertes')->latest()->limit(8)->get();
        } catch (\Exception $e) {}

        return view('profil', compact('user', 'alertes'));
    }

    public function update(Request $request)
    {
        $user = session('user');
        if (!$user) return redirect('/login');
        if (is_array($user)) $user = (object) $user;

        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'telephone' => 'required|string|max:30',
        ]);

        DB::table('users')->where('id', $user->id)->update([
            'nom'              => $request->nom,
            'prenom'           => $request->prenom,
            'name'             => $request->prenom . ' ' . $request->nom,
            'telephone'        => $request->telephone,
            'pays'             => $request->pays,
            'region'           => $request->region,
            'departement'      => $request->departement,
            'arrondissement'   => $request->arrondissement,
            'ville_residence'  => $request->ville_residence,
            'quartier'         => $request->quartier,
            'adresse'          => $request->adresse,
            'profession'       => $request->profession,
            'organisation'     => $request->organisation,
            'updated_at'       => now(),
        ]);

        session(['user' => DB::table('users')->where('id', $user->id)->first()]);

        return back()->with('success_profil', 'Profil mis à jour avec succès.');
    }

    public function changePassword(Request $request)
    {
        $user = session('user');
        if (!$user) return redirect('/login');
        if (is_array($user)) $user = (object) $user;

        $request->validate([
            'ancien_mdp'    => 'required',
            'nouveau_mdp'   => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->ancien_mdp, $user->password)) {
            return back()->with('error_password', 'Mot de passe actuel incorrect.');
        }

        DB::table('users')->where('id', $user->id)->update([
            'password'   => Hash::make($request->nouveau_mdp),
            'updated_at' => now(),
        ]);

        session(['user' => DB::table('users')->where('id', $user->id)->first()]);

        return back()->with('success_password', 'Mot de passe changé avec succès.');
    }

    public function uploadPhoto(Request $request)
    {
        $user = session('user');
        if (!$user) return redirect('/login');
        if (is_array($user)) $user = (object) $user;

        $request->validate([
            'photo_profil' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = $request->file('photo_profil')->store('photos', 'public');

        DB::table('users')->where('id', $user->id)->update([
            'photo_profil' => $path,
            'updated_at'   => now(),
        ]);

        session(['user' => DB::table('users')->where('id', $user->id)->first()]);

        return back()->with('success_photo', 'Photo de profil mise à jour.');
    }
}
