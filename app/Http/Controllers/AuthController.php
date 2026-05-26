<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    // Email de l'administrateur principal
    const ADMIN_EMAIL = 'franckazegue0007@gmail.com';

    public function register(Request $request)
    {

        $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'telephone'      => 'required',
            'profession'     => 'required',
            'sexe'           => 'required',
            'date_naissance' => 'required|date|before:-16 years',
            'password'       => 'required|confirmed|min:8',
            'photo_profil'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo_profil') && $request->file('photo_profil')->isValid()) {
            $file = $request->file('photo_profil');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/photos'), $filename);
            $photoPath = 'uploads/photos/' . $filename;
        }

        // Résolution des champs géographiques (select dynamique ou saisie libre)
        $etat          = $request->etat          ?: $request->etat_libre;
        $departement   = $request->departement   ?: $request->departement_libre;
        $arrondissement= $request->arrondissement?: $request->arrondissement_libre;
        $ville         = $request->ville         ?: $request->ville_libre;

        // Téléphone : format international en priorité
        $telephone = $request->telephone_international ?: $request->telephone;

        $userId = DB::table('users')->insertGetId([
            'name'             => $request->nom . ' ' . $request->prenom,
            'nom'              => $request->nom,
            'prenom'           => $request->prenom,
            'email'            => $request->email,
            'telephone'        => $telephone,
            'profession'       => $request->profession,
            'pays'             => $request->pays,
            'code_pays'        => $request->code_pays,
            'nationalite'      => $request->nationalite,
            'region'           => $etat,
            'etat'             => $etat,
            'departement'      => $departement,
            'arrondissement'   => $arrondissement,
            'ville'            => $ville,
            'quartier'         => $request->quartier,
            'adresse'          => $request->adresse,
            'organisation'     => $request->organisation,
            'sexe'             => $request->sexe,
            'date_naissance'   => $request->date_naissance,
            'statut_matrimonial' => $request->statut_matrimonial,
            'role'             => $request->role ?? 'utilisateur',
            'validation_status'=> 'en_attente',
            'statut'           => 'en_attente',
            'password'         => Hash::make($request->password),
            'photo_profil'     => $photoPath,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $appUrl = config('app.url');

        // ── Email de confirmation à l'utilisateur ──────────────────────────
        try {
            Mail::send([], [], function($message) use ($request) {
                $message->to($request->email)
                        ->subject('Demande d\'inscription reçue')
                        ->html('
<!DOCTYPE html>
<html>
<body style="background:#050816;font-family:Arial;padding:30px;">
<div style="max-width:550px;margin:auto;background:#101935;border-radius:16px;padding:35px;">
<h2 style="color:#39ff14;text-align:center;">Demande reçue</h2>
<p style="color:#ccc;margin-top:15px;">Bonjour <strong style="color:white;">'.$request->nom.' '.$request->prenom.'</strong>,</p>
<p style="color:#ccc;margin-top:12px;">Votre demande d\'inscription a bien été reçue et est en cours de traitement.</p>
<p style="color:#ccc;margin-top:12px;">L\'administrateur examinera votre dossier et vous recevrez un email de confirmation dès que votre compte sera validé.</p>
<p style="color:#9ca3af;margin-top:25px;font-size:13px;">Ne répondez pas à cet email.</p>
</div>
</body>
</html>
                ');
            });
        } catch (\Exception $e) {
            Log::error('Email inscription utilisateur non envoyé à ' . $request->email . ': ' . $e->getMessage());
        }

        // ── Email de notification à l'administrateur ───────────────────────
        try {
            $tkV = substr(hash_hmac('sha256', $userId . '|valider', config('app.key')), 0, 40);
            $tkR = substr(hash_hmac('sha256', $userId . '|refuser', config('app.key')), 0, 40);
            $tkA = substr(hash_hmac('sha256', $userId . '|attente', config('app.key')), 0, 40);
            $validerUrl  = $appUrl . '/admin/valider-mail/' . $userId . '/' . $tkV;
            $refuserUrl  = $appUrl . '/admin/refuser-mail/' . $userId . '/' . $tkR;
            $attenteUrl  = $appUrl . '/admin/attente-mail/' . $userId . '/' . $tkA;
            $panelUrl    = $appUrl . '/admin/utilisateurs';
            $userIp      = request()->ip();
            $userAgent   = request()->userAgent();
            $dateInsc    = now()->format('d/m/Y H:i');
            $aPhoto      = $photoPath ? 'Oui' : 'Non';

            $row = function($label, $value, $color='#e8edf8') {
                $v = htmlspecialchars($value ?? '-', ENT_QUOTES, 'UTF-8');
                return '<tr style="border-bottom:1px solid #182640;">
                  <td style="padding:9px 0;color:#6b7fa0;font-size:13px;width:42%;">'.$label.'</td>
                  <td style="padding:9px 0;color:'.$color.';font-size:13px;">'.$v.'</td>
                </tr>';
            };

            Mail::send([], [], function($message) use (
                $request, $validerUrl, $refuserUrl, $attenteUrl, $panelUrl,
                $userIp, $userAgent, $dateInsc, $etat, $departement,
                $arrondissement, $ville, $telephone, $aPhoto, $row
            ) {
                $message->to(self::ADMIN_EMAIL)
                        ->subject('🔔 Nouvelle demande — ' . $request->nom . ' ' . $request->prenom)
                        ->html('
<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#060c1a;font-family:Arial,sans-serif;">
<div style="max-width:660px;margin:30px auto;background:#0d1a2e;border-radius:16px;overflow:hidden;border:1px solid #182640;">

  <div style="background:linear-gradient(135deg,#0d1a2e,#112240);padding:28px 32px;border-bottom:2px solid #2fa84f;">
    <h2 style="margin:0;color:#2fa84f;font-size:20px;letter-spacing:1px;">🔔 NOUVELLE DEMANDE D\'INSCRIPTION</h2>
    <p style="margin:6px 0 0;color:#6b7fa0;font-size:13px;">Plateforme Surveillance des Salles Serveurs</p>
  </div>

  <div style="padding:28px 32px;">

    <p style="color:#2fa84f;font-size:12px;letter-spacing:2px;font-weight:bold;margin-bottom:12px;text-transform:uppercase;">▸ Identité</p>
    <table style="width:100%;border-collapse:collapse;margin-bottom:22px;">
      '.$row('Nom complet', $request->nom.' '.$request->prenom, '#e8edf8').'
      '.$row('Email', $request->email, '#4a9fc4').'
      '.$row('Téléphone', $telephone).'
      '.$row('Sexe', $request->sexe).'
      '.$row('Date de naissance', $request->date_naissance).'
      '.$row('Nationalité', $request->nationalite).'
      '.$row('Statut matrimonial', $request->statut_matrimonial).'
      '.$row('Profession', $request->profession).'
      '.$row('Organisation', $request->organisation).'
      <tr style="border-bottom:1px solid #182640;">
        <td style="padding:9px 0;color:#6b7fa0;font-size:13px;width:42%;">Rôle demandé</td>
        <td style="padding:9px 0;font-size:13px;">
          <span style="background:rgba(47,168,79,0.15);color:#2fa84f;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:bold;">'.strtoupper($request->role ?? 'utilisateur').'</span>
        </td>
      </tr>
      '.$row('Photo de profil', $aPhoto).'
    </table>

    <p style="color:#2fa84f;font-size:12px;letter-spacing:2px;font-weight:bold;margin-bottom:12px;text-transform:uppercase;">▸ Localisation</p>
    <table style="width:100%;border-collapse:collapse;margin-bottom:22px;">
      '.$row('Pays', $request->pays).'
      '.$row('Nationalité', $request->nationalite).'
      '.$row('Région / État', $etat).'
      '.$row('Département', $departement).'
      '.$row('Arrondissement', $arrondissement).'
      '.$row('Ville', $ville).'
      '.$row('Quartier', $request->quartier).'
      '.$row('Adresse', $request->adresse).'
    </table>

    <p style="color:#2fa84f;font-size:12px;letter-spacing:2px;font-weight:bold;margin-bottom:12px;text-transform:uppercase;">▸ Informations techniques</p>
    <table style="width:100%;border-collapse:collapse;margin-bottom:26px;">
      '.$row('Date d\'inscription', $dateInsc).'
      '.$row('Adresse IP', $userIp, '#4a9fc4').'
      <tr>
        <td style="padding:9px 0;color:#6b7fa0;font-size:13px;vertical-align:top;">Navigateur</td>
        <td style="padding:9px 0;color:#e8edf8;font-size:12px;word-break:break-all;">'.htmlspecialchars(substr($userAgent, 0, 150), ENT_QUOTES, 'UTF-8').'</td>
      </tr>
    </table>

    <div style="text-align:center;padding:16px 0 8px;">
      <a href="'.$validerUrl.'" style="display:inline-block;margin:6px;padding:13px 26px;background:#2fa84f;color:#060c1a;text-decoration:none;border-radius:50px;font-weight:bold;font-size:14px;">✅ VALIDER</a>
      <a href="'.$attenteUrl.'" style="display:inline-block;margin:6px;padding:13px 26px;background:#d97706;color:white;text-decoration:none;border-radius:50px;font-weight:bold;font-size:14px;">⏳ EN ATTENTE</a>
      <a href="'.$refuserUrl.'" style="display:inline-block;margin:6px;padding:13px 26px;background:#dc2626;color:white;text-decoration:none;border-radius:50px;font-weight:bold;font-size:14px;">❌ REFUSER</a>
    </div>
    <div style="text-align:center;margin-top:14px;">
      <a href="'.$panelUrl.'" style="color:#2fa84f;font-size:13px;text-decoration:none;">→ Gérer depuis le panneau administrateur</a>
    </div>

  </div>

  <div style="background:#070e1c;padding:14px 32px;border-top:1px solid #182640;text-align:center;">
    <p style="margin:0;color:#3a5070;font-size:12px;">Plateforme de Surveillance — Action requise</p>
  </div>

</div>
</body>
</html>
                ');
            });
        } catch (\Exception $e) {
            Log::error('Email notification admin non envoyé à ' . self::ADMIN_EMAIL . ': ' . $e->getMessage());
        }

        return redirect('/login')
            ->with('success', 'Votre demande d\'inscription a été envoyée à l\'administrateur principal.');

    }



    public function login(Request $request)
    {
        $hash = env('ACCESS_PASSWORD', '');

        if (!$hash || !Hash::check($request->password, $hash)) {
            return back()->with('error', 'Mot de passe incorrect.');
        }

        session(['user' => (object)['id' => 1, 'nom' => 'Admin', 'role' => 'admin']]);

        return redirect('/dashboard');
    }



    public function logout()
    {
        session()->forget('user');
        session()->flush();

        return redirect('/login')->with('success', 'Vous avez été déconnecté.');
    }



    // ══════════════════════════════════════════════════════
    //  RÉINITIALISATION MOT DE PASSE
    // ══════════════════════════════════════════════════════

    public function forgotPasswordForm()
    {
        return view('forgot-password');
    }

    public function forgotPasswordPost(\Illuminate\Http\Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Aucun compte trouvé avec cette adresse email.');
        }

        // Crée la table si elle n'existe pas encore
        DB::statement("CREATE TABLE IF NOT EXISTS `password_resets` (
            `email` varchar(255) NOT NULL,
            `token` varchar(255) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $token = bin2hex(random_bytes(32));

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $resetUrl = config('app.url') . '/reset-password/' . $token . '?email=' . urlencode($request->email);

        $htmlEmail = '
<!DOCTYPE html>
<html>
<body style="background:#060c1a;font-family:Arial;padding:30px;">
<div style="max-width:520px;margin:auto;background:#0d1a2e;border-radius:16px;padding:35px;border:1px solid #182640;">
<h2 style="color:#2fa84f;text-align:center;margin-bottom:5px;">Réinitialisation du mot de passe</h2>
<p style="color:#6b7fa0;text-align:center;margin-bottom:25px;font-size:14px;">Plateforme de Surveillance</p>
<p style="color:#d4dced;margin-bottom:14px;">Bonjour <strong>' . e($user->nom) . ' ' . e($user->prenom) . '</strong>,</p>
<p style="color:#a0aec0;margin-bottom:20px;line-height:1.6;">
  Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous pour en choisir un nouveau.
  Ce lien est valide pendant <strong style="color:#d4dced;">1 heure</strong>.
</p>
<div style="text-align:center;margin:28px 0;">
<a href="' . $resetUrl . '" style="display:inline-block;padding:14px 32px;background:#2fa84f;color:#060c1a;text-decoration:none;border-radius:10px;font-weight:bold;font-size:16px;">
  RÉINITIALISER MON MOT DE PASSE
</a>
</div>
<p style="color:#4a5568;font-size:13px;margin-top:20px;word-break:break-all;">
  Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
  <span style="color:#2fa84f;">' . $resetUrl . '</span>
</p>
<p style="color:#4a5568;font-size:12px;margin-top:16px;">
  Si vous n\'avez pas demandé cette réinitialisation, ignorez cet email. Votre mot de passe ne sera pas modifié.
</p>
</div>
</body>
</html>';

        try {
            Mail::send([], [], function ($msg) use ($request, $htmlEmail) {
                $msg->to($request->email)
                    ->subject('Réinitialisation de votre mot de passe — Surveillance')
                    ->html($htmlEmail);
            });
        } catch (\Exception $e) {
            Log::error('Reset email non envoyé à ' . $request->email . ': ' . $e->getMessage());
            return back()->with('error', 'Impossible d\'envoyer l\'email : problème de configuration SMTP. Contactez l\'administrateur.');
        }

        return back()->with('success', 'Un lien de réinitialisation a été envoyé à ' . $request->email . '. Vérifiez aussi vos spams.');
    }

    public function resetPasswordForm(\Illuminate\Http\Request $request, $token)
    {
        $email = $request->query('email', '');
        return view('reset-password', compact('token', 'email'));
    }

    public function resetPasswordPost(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'token'                 => 'required',
            'password'              => 'required|confirmed|min:8',
        ]);

        $reset = DB::table('password_resets')->where('email', $request->email)->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return back()->with('error', 'Lien invalide ou déjà utilisé. Veuillez refaire une demande.');
        }

        // Vérifie l'expiration (1 heure)
        if (\Carbon\Carbon::parse($reset->created_at)->addHour()->lt(now())) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return back()->with('error', 'Ce lien a expiré. Veuillez faire une nouvelle demande.');
        }

        DB::table('users')->where('email', $request->email)->update([
            'password'   => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect('/login')->with('success', 'Mot de passe réinitialisé avec succès. Vous pouvez vous connecter.');
    }

}
