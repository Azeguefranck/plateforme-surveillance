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
        // Check DB before validation (unique:users hits the DB)
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            return back()->withInput()->with('error', '⚠️ Base de données inaccessible. Démarrez MySQL : sudo /opt/lampp/lampp startmysql');
        }

        $request->validate([
            'nom'      => 'required|string|max:100',
            'prenom'   => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'telephone'=> 'required|string|max:30',
            'password' => 'required|confirmed|min:8',
            'pays'     => 'required|string',
        ]);

        // Génération du token sécurisé pour les boutons d'action dans l'email admin
        $adminToken    = bin2hex(random_bytes(32));
        $tokenExpires  = now()->addHours(48);

        // Photo de profil
        $photoPath = null;
        if ($request->hasFile('photo_profil')) {
            $photoPath = $request->file('photo_profil')->store('photos', 'public');
        }

        $userId = DB::table('users')->insertGetId([
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
            'pays_code'         => $request->iso_pays,
            'pays_nom'          => $request->pays_nom ?: $request->pays,
            'pays_geoname_id'   => $request->pays_geoname_id ?: null,
            'region_geoname_id' => $request->region_geoname_id ?: null,
            'dept_geoname_id'   => $request->dept_geoname_id ?: null,
            'arr_geoname_id'    => $request->arr_geoname_id ?: null,
            'ville'             => $request->ville ?: $request->ville_residence,
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
            'lieu_naissance'   => $request->lieu_naissance,
            'admin_token'      => $adminToken,
            'token_expires_at' => $tokenExpires,
            'password'         => Hash::make($request->password),
            'validation_status'=> 'en_attente',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Email de confirmation à l'utilisateur
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
                "Plateforme de Surveillance",
                function ($message) use ($request) {
                    $message->to($request->email)->subject('Inscription reçue — En attente de validation');
                }
            );
        } catch (\Exception $e) {}

        // Email HTML professionnel à l'administrateur avec 3 boutons d'action
        // On utilise l'hôte réel de la requête, pas APP_URL (évite 127.0.0.1 dans les emails)
        $baseUrl     = $request->getSchemeAndHttpHost();
        $validerUrl  = $baseUrl . '/admin/validate-user/' . $adminToken;
        $refuserUrl  = $baseUrl . '/admin/refuse-user/'   . $adminToken;
        $attenteUrl  = $baseUrl . '/admin/pending-user/'  . $adminToken;
        $adminEmail  = 'franckazegue0007@gmail.com';

        $esc = fn($v) => htmlspecialchars($v ?? '—', ENT_QUOTES, 'UTF-8');
        $p   = $esc($request->prenom);
        $n   = $esc($request->nom);
        $em  = $esc($request->email);
        $tel = $esc(($request->indicatif_tel ?? '') . ' ' . ($request->telephone ?? ''));
        $adminHtml =
            '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>'
            . '*{margin:0;padding:0;box-sizing:border-box}'
            . 'body{background:#060d1f;font-family:Arial,Helvetica,sans-serif}'
            . '.w{max-width:600px;margin:0 auto;background:#060d1f}'
            . '.h{background:linear-gradient(135deg,#0e1a38,#060d1f);padding:28px;text-align:center;border-bottom:3px solid #33ff88}'
            . '.hl{color:#33ff88;font-size:24px;font-weight:900;letter-spacing:2px;margin:0}'
            . '.hs{color:#5a6a99;font-size:11px;margin-top:5px;letter-spacing:1.5px}'
            . '.b{background:#0a1428;padding:26px}'
            . '.badge{display:inline-block;background:rgba(255,214,51,.12);color:#ffd633;border:1px solid rgba(255,214,51,.3);padding:5px 14px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:1px;margin-bottom:16px}'
            . '.st{color:#8899cc;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;font-weight:700;margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid #1e2f5a}'
            . 'table{width:100%;border-collapse:collapse;margin-bottom:16px}'
            . 'tr:nth-child(even){background:rgba(255,255,255,.025)}'
            . 'td{padding:9px 10px;font-size:13px;border-bottom:1px solid #0e1c35}'
            . '.k{color:#8899cc;width:42%;font-weight:600}'
            . '.v{color:#c7d2ff}'
            . '.acts{text-align:center;padding:18px 0 12px}'
            . 'a.btn{display:inline-block;padding:13px 24px;border-radius:8px;font-size:13px;font-weight:800;text-decoration:none;letter-spacing:1px;margin:4px}'
            . '.b1{background:#0f2e1a;color:#33ff88;border:2px solid #33ff88}'
            . '.b2{background:#2e0f0f;color:#ff5733;border:2px solid #ff5733}'
            . '.b3{background:#2e250a;color:#ffd633;border:2px solid #ffd633}'
            . '.note{font-size:11px;color:#3a4a6a;text-align:center;margin-top:10px;line-height:1.6}'
            . '.f{background:#060d1f;padding:14px;text-align:center;color:#3a4a6a;font-size:11px;border-top:1px solid #0e1c35}'
            . '</style></head><body>'
            . '<div class="w">'
            . '<div class="h"><h1 class="hl">&#128274; NOUVELLE INSCRIPTION</h1><div class="hs">SURVEILLANCE DES SALLES SERVEURS</div></div>'
            . '<div class="b">'
            . '<div><span class="badge">&#128290; NOUVELLE INSCRIPTION</span></div>'
            . '<div class="st">Informations utilisateur</div>'
            . '<table>'
            . '<tr><td class="k">Nom complet</td><td class="v">' . $p . ' ' . $n . '</td></tr>'
            . '<tr><td class="k">Email</td><td class="v">' . $em . '</td></tr>'
            . '<tr><td class="k">T&eacute;l&eacute;phone</td><td class="v">' . $tel . '</td></tr>'
            . '<tr><td class="k">Sexe</td><td class="v">' . $esc($request->sexe) . '</td></tr>'
            . '<tr><td class="k">Date de naissance</td><td class="v">' . $esc($request->date_naissance) . '</td></tr>'
            . '<tr><td class="k">Lieu de naissance</td><td class="v">' . $esc($request->lieu_naissance) . '</td></tr>'
            . '<tr><td class="k">Nationalit&eacute;</td><td class="v">' . $esc($request->nationalite) . '</td></tr>'
            . '<tr><td class="k">Statut matrimonial</td><td class="v">' . $esc($request->statut_matrimonial) . '</td></tr>'
            . '<tr><td class="k">Pays</td><td class="v">' . $esc($request->pays) . '</td></tr>'
            . '<tr><td class="k">R&eacute;gion</td><td class="v">' . $esc($request->region) . '</td></tr>'
            . '<tr><td class="k">D&eacute;partement</td><td class="v">' . $esc($request->departement) . '</td></tr>'
            . '<tr><td class="k">Arrondissement</td><td class="v">' . $esc($request->arrondissement) . '</td></tr>'
            . '<tr><td class="k">Ville</td><td class="v">' . $esc($request->ville_residence) . '</td></tr>'
            . '<tr><td class="k">Profession</td><td class="v">' . $esc($request->profession) . '</td></tr>'
            . '<tr><td class="k">Organisation</td><td class="v">' . $esc($request->organisation) . '</td></tr>'
            . '<tr><td class="k">R&ocirc;le demand&eacute;</td><td class="v">' . $esc($request->role ?? 'utilisateur') . '</td></tr>'
            . '<tr><td class="k">Photo de profil</td><td class="v">' . ($photoPath ? '&#10003; Oui ('.$photoPath.')' : 'Non') . '</td></tr>'
            . '<tr><td class="k">Date inscription</td><td class="v">' . now()->format('d/m/Y H:i:s') . '</td></tr>'
            . '</table>'
            . '<div class="acts">'
            . '<a href="' . $validerUrl . '" class="btn b1">&#9989; VALIDER</a>'
            . '&nbsp;<a href="' . $refuserUrl . '" class="btn b2">&#10060; REFUSER</a>'
            . '&nbsp;<a href="' . $attenteUrl . '" class="btn b3">&#9203; EN ATTENTE</a>'
            . '</div>'
            . '<div class="note">Cliquez sur un bouton pour agir sur cette demande.<br>Une notification email sera automatiquement envoy&eacute;e &agrave; l\'utilisateur.</div>'
            . '</div>'
            . '<div class="f">Plateforme de Surveillance &mdash; Message automatique &mdash; Ne pas r&eacute;pondre</div>'
            . '</div></body></html>';

        try {
            Mail::html($adminHtml, function ($message) use ($adminEmail, $request) {
                $message->to($adminEmail)
                        ->subject('Nouvelle inscription — ' . $request->prenom . ' ' . $request->nom);
            });
        } catch (\Exception $e) {}

        return redirect('/login')->with('success', 'Inscription envoyée. En attente de validation administrateur.');
    }



    public function login(Request $request)
    {
        try {
            $user = DB::table('users')
                ->where('email', $request->email)
                ->first();
        } catch (\Exception $e) {
            return back()->with('error', '⚠️ Base de données inaccessible. Démarrez MySQL avec : sudo /opt/lampp/lampp startmysql');
        }

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


        if($user->validation_status == 'refuse'){

            return back()->with(
                'error',
                'Compte refusé. Veuillez contacter l\'administrateur.'
            );

        }


        if($user->validation_status == 'en_attente'){

            return back()->with(
                'error',
                'Compte en attente de validation administrateur.'
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