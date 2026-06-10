<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

        $nouveau = $request->nouveau_mdp;

        DB::table('users')->where('id', $user->id)->update([
            'password'   => Hash::make($nouveau),
            'updated_at' => now(),
        ]);

        session(['user' => DB::table('users')->where('id', $user->id)->first()]);

        try {
            $prenom = $user->prenom ?? $user->name ?? 'Utilisateur';
            $nom    = $user->nom    ?? '';
            Mail::html(
                '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>'
                . 'body{background:#060d1f;font-family:Arial,sans-serif;margin:0}'
                . '.w{max-width:520px;margin:0 auto;background:#060d1f}'
                . '.h{background:linear-gradient(135deg,#0e1a38,#060d1f);padding:24px;text-align:center;border-bottom:3px solid #33b5ff}'
                . '.hl{color:#33b5ff;font-size:20px;font-weight:900;letter-spacing:2px;margin:0}'
                . '.b{background:#0a1428;padding:24px}'
                . '.pw-box{background:#07102a;border:1px solid rgba(51,181,255,.3);border-radius:10px;padding:16px 20px;margin:16px 0;text-align:center}'
                . '.pw{color:#33ff88;font-size:22px;font-weight:900;letter-spacing:3px;font-family:monospace}'
                . '.info{color:#8899cc;font-size:13px;line-height:1.7}'
                . '.warn{color:#ffd633;font-size:12px;margin-top:14px;padding:10px 14px;background:rgba(255,214,51,.07);border-radius:8px;border-left:3px solid #ffd633}'
                . '.f{background:#060d1f;padding:14px;text-align:center;color:#3a4a6a;font-size:11px;border-top:1px solid #0e1c35}'
                . '</style></head><body>'
                . '<div class="w">'
                . '<div class="h"><h1 class="hl">&#128274; MOT DE PASSE MODIFI&Eacute;</h1></div>'
                . '<div class="b">'
                . '<p class="info">Bonjour <strong style="color:#fff">' . htmlspecialchars($prenom . ' ' . $nom) . '</strong>,</p>'
                . '<p class="info" style="margin-top:10px">Votre mot de passe a &eacute;t&eacute; modifi&eacute; avec succ&egrave;s. Voici votre nouveau mot de passe :</p>'
                . '<div class="pw-box"><div class="pw">' . htmlspecialchars($nouveau) . '</div></div>'
                . '<p class="info">Vous pouvez maintenant vous connecter avec ce mot de passe.</p>'
                . '<div class="warn">&#9888; Si vous n\'&ecirc;tes pas &agrave; l\'origine de cette modification, contactez imm&eacute;diatement l\'administrateur.</div>'
                . '</div>'
                . '<div class="f">Plateforme de Surveillance &mdash; Message automatique</div>'
                . '</div></body></html>',
                function ($mail) use ($user, $prenom, $nom) {
                    $mail->to($user->email)
                         ->subject('🔐 Nouveau mot de passe — Plateforme de Surveillance');
                }
            );
        } catch (\Exception $e) {}

        return back()->with('success_password', 'Mot de passe changé avec succès. Un email de confirmation a été envoyé.');
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
