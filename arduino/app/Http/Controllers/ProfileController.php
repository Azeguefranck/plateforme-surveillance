<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    private function getUser(): ?\stdClass
    {
        $raw = session('user');
        if (!$raw) return null;
        return (object)(array)$raw;
    }

    public function update(Request $request)
    {
        $user = $this->getUser();
        if (!$user) return redirect('/login');

        $request->validate([
            'nom'        => 'required|string|max:100',
            'prenom'     => 'required|string|max:100',
            'telephone'  => 'nullable|string|max:30',
            'profession' => 'nullable|string|max:200',
            'organisation'=> 'nullable|string|max:200',
            'adresse'    => 'nullable|string|max:500',
            'quartier'   => 'nullable|string|max:200',
        ]);

        DB::table('users')->where('id', $user->id)->update([
            'nom'          => $request->nom,
            'prenom'       => $request->prenom,
            'name'         => $request->nom . ' ' . $request->prenom,
            'telephone'    => $request->telephone,
            'profession'   => $request->profession,
            'organisation' => $request->organisation,
            'adresse'      => $request->adresse,
            'quartier'     => $request->quartier,
            'updated_at'   => now(),
        ]);

        session(['user' => DB::table('users')->where('id', $user->id)->first()]);

        return back()->with('success_profil', 'Informations mises à jour avec succès.');
    }

    public function changePassword(Request $request)
    {
        $user = $this->getUser();
        if (!$user) return redirect('/login');

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:8',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error_pwd', 'Ancien mot de passe incorrect.');
        }

        DB::table('users')->where('id', $user->id)->update([
            'password'   => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        session(['user' => DB::table('users')->where('id', $user->id)->first()]);

        return back()->with('success_pwd', 'Mot de passe changé avec succès.');
    }

    public function updatePhoto(Request $request)
    {
        $user = $this->getUser();
        if (!$user) return redirect('/login');

        $request->validate([
            'photo_profil' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        $file     = $request->file('photo_profil');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        if (!file_exists(public_path('uploads/photos'))) {
            mkdir(public_path('uploads/photos'), 0775, true);
        }

        $file->move(public_path('uploads/photos'), $filename);
        $photoPath = 'uploads/photos/' . $filename;

        if ($user->photo_profil && file_exists(public_path($user->photo_profil))) {
            @unlink(public_path($user->photo_profil));
        }

        DB::table('users')->where('id', $user->id)->update([
            'photo_profil' => $photoPath,
            'updated_at'   => now(),
        ]);

        session(['user' => DB::table('users')->where('id', $user->id)->first()]);

        return back()->with('success_photo', 'Photo de profil mise à jour.');
    }
}
