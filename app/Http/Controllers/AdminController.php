<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{

    public function utilisateurs()
    {
        $users = DB::table('users')->orderBy('created_at', 'desc')->get();
        return view('utilisateurs', compact('users'));
    }

    // ══════════════════════════════════════════════════════
    //  ACTIONS DEPUIS LE PANNEAU ADMIN (POST)
    // ══════════════════════════════════════════════════════

    public function valider($id)
    {
        DB::table('users')->where('id', $id)->update([
            'statut'            => 'valide',
            'validation_status' => 'valide',
            'updated_at'        => now(),
        ]);

        $user = DB::table('users')->where('id', $id)->first();
        if ($user) {
            $this->envoyerEmailValidation($user);
        }

        return back()->with('success', 'Compte de ' . ($user->nom ?? '') . ' validé avec succès.');
    }

    public function refuser($id)
    {
        DB::table('users')->where('id', $id)->update([
            'statut'            => 'refuse',
            'validation_status' => 'refuse',
            'updated_at'        => now(),
        ]);

        $user = DB::table('users')->where('id', $id)->first();
        if ($user) {
            $this->envoyerEmailRefus($user);
        }

        return back()->with('success', 'Compte de ' . ($user->nom ?? '') . ' refusé.');
    }

    public function attente($id)
    {
        DB::table('users')->where('id', $id)->update([
            'statut'            => 'en_attente',
            'validation_status' => 'en_attente',
            'updated_at'        => now(),
        ]);

        $user = DB::table('users')->where('id', $id)->first();
        if ($user) {
            $this->envoyerEmailAttente($user);
        }

        return back()->with('success', 'Compte de ' . ($user->nom ?? '') . ' mis en attente.');
    }

    // ══════════════════════════════════════════════════════
    //  TOKEN SÉCURISÉ POUR LIENS EMAIL
    // ══════════════════════════════════════════════════════

    public static function mailToken(int $id, string $action): string
    {
        return substr(hash_hmac('sha256', $id . '|' . $action, config('app.key')), 0, 40);
    }

    // ══════════════════════════════════════════════════════
    //  ACTIONS DEPUIS LIEN EMAIL (GET — sans connexion requise)
    // ══════════════════════════════════════════════════════

    public function validerMail($id, $token)
    {
        if (!hash_equals(self::mailToken((int)$id, 'valider'), $token)) {
            return view('admin-mail-action', ['ok' => false, 'action' => '', 'user' => null,
                'msg' => 'Lien invalide ou expiré. Utilisez le lien reçu dans l\'email d\'inscription.']);
        }
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return view('admin-mail-action', ['ok' => false, 'action' => '', 'user' => null,
                'msg' => 'Utilisateur introuvable (id=' . $id . ').']);
        }
        $already = ($user->statut === 'valide');
        if (!$already) {
            DB::table('users')->where('id', $id)->update([
                'statut' => 'valide', 'validation_status' => 'valide', 'updated_at' => now(),
            ]);
            $user = DB::table('users')->where('id', $id)->first();
            $this->envoyerEmailValidation($user);
        }
        return view('admin-mail-action', ['ok' => true, 'action' => 'valide', 'user' => $user, 'already' => $already,
            'msg' => $already ? 'Ce compte était déjà validé.' : 'Compte validé. Un email de confirmation a été envoyé à ' . $user->email . '.']);
    }

    public function refuserMail($id, $token)
    {
        if (!hash_equals(self::mailToken((int)$id, 'refuser'), $token)) {
            return view('admin-mail-action', ['ok' => false, 'action' => '', 'user' => null,
                'msg' => 'Lien invalide ou expiré. Utilisez le lien reçu dans l\'email d\'inscription.']);
        }
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return view('admin-mail-action', ['ok' => false, 'action' => '', 'user' => null,
                'msg' => 'Utilisateur introuvable (id=' . $id . ').']);
        }
        $already = ($user->statut === 'refuse');
        if (!$already) {
            DB::table('users')->where('id', $id)->update([
                'statut' => 'refuse', 'validation_status' => 'refuse', 'updated_at' => now(),
            ]);
            $user = DB::table('users')->where('id', $id)->first();
            $this->envoyerEmailRefus($user);
        }
        return view('admin-mail-action', ['ok' => true, 'action' => 'refuse', 'user' => $user, 'already' => $already,
            'msg' => $already ? 'Ce compte était déjà refusé.' : 'Compte refusé. Un email de notification a été envoyé à ' . $user->email . '.']);
    }

    public function attenterMail($id, $token)
    {
        if (!hash_equals(self::mailToken((int)$id, 'attente'), $token)) {
            return view('admin-mail-action', ['ok' => false, 'action' => '', 'user' => null,
                'msg' => 'Lien invalide ou expiré. Utilisez le lien reçu dans l\'email d\'inscription.']);
        }
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return view('admin-mail-action', ['ok' => false, 'action' => '', 'user' => null,
                'msg' => 'Utilisateur introuvable (id=' . $id . ').']);
        }
        $already = ($user->statut === 'en_attente');
        if (!$already) {
            DB::table('users')->where('id', $id)->update([
                'statut' => 'en_attente', 'validation_status' => 'en_attente', 'updated_at' => now(),
            ]);
            $user = DB::table('users')->where('id', $id)->first();
            $this->envoyerEmailAttente($user);
        }
        return view('admin-mail-action', ['ok' => true, 'action' => 'attente', 'user' => $user, 'already' => $already,
            'msg' => $already ? 'Ce compte était déjà en attente.' : 'Compte mis en attente. Un email de notification a été envoyé à ' . $user->email . '.']);
    }

    // ══════════════════════════════════════════════════════
    //  MÉTHODES EMAIL PRIVÉES
    // ══════════════════════════════════════════════════════

    private function envoyerEmailValidation($user)
    {
        $loginUrl = config('app.url') . '/login';
        try {
            Mail::send([], [], function ($msg) use ($user, $loginUrl) {
                $msg->to($user->email)
                    ->subject('✅ Compte validé — Accès autorisé')
                    ->html($this->templateEmail(
                        '✅ Compte créé avec succès',
                        '#2fa84f',
                        $user,
                        'Votre accès à la plateforme a été validé par l\'administrateur.',
                        'Vous pouvez maintenant vous connecter et accéder à votre espace de surveillance.',
                        'SE CONNECTER',
                        $loginUrl,
                        '#2fa84f',
                        '#060c1a'
                    ));
            });
        } catch (\Exception $e) {
            Log::error('Email validation non envoyé à ' . $user->email . ': ' . $e->getMessage());
        }
    }

    private function envoyerEmailRefus($user)
    {
        try {
            Mail::send([], [], function ($msg) use ($user) {
                $msg->to($user->email)
                    ->subject('Demande d\'inscription — Décision')
                    ->html($this->templateEmail(
                        '❌ Demande non acceptée',
                        '#dc2626',
                        $user,
                        'Votre demande d\'inscription a été refusée.',
                        'Pour plus d\'informations, veuillez contacter l\'administrateur de la plateforme.',
                        null, null, null, null
                    ));
            });
        } catch (\Exception $e) {
            Log::error('Email refus non envoyé à ' . $user->email . ': ' . $e->getMessage());
        }
    }

    private function envoyerEmailAttente($user)
    {
        try {
            Mail::send([], [], function ($msg) use ($user) {
                $msg->to($user->email)
                    ->subject('⏳ Demande en cours de vérification')
                    ->html($this->templateEmail(
                        '⏳ Vérification en cours',
                        '#d97706',
                        $user,
                        'Votre demande est toujours en cours de vérification.',
                        'L\'administrateur examine votre dossier. Vous serez notifié par email dès qu\'une décision sera prise.',
                        null, null, null, null
                    ));
            });
        } catch (\Exception $e) {
            Log::error('Email attente non envoyé à ' . $user->email . ': ' . $e->getMessage());
        }
    }

    private function templateEmail($titre, $couleur, $user, $ligne1, $ligne2, $btnTxt, $btnUrl, $btnBg, $btnColor)
    {
        $btn = '';
        if ($btnTxt && $btnUrl) {
            $btn = '
<div style="text-align:center;margin:28px 0 0;">
  <a href="' . $btnUrl . '" style="display:inline-block;padding:14px 34px;background:' . $btnBg . ';color:' . $btnColor . ';text-decoration:none;border-radius:50px;font-weight:bold;font-size:15px;letter-spacing:.5px;">
    ' . $btnTxt . '
  </a>
</div>';
        }

        return '
<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#060c1a;font-family:Arial,sans-serif;">
<div style="max-width:540px;margin:30px auto;background:#0d1a2e;border-radius:16px;overflow:hidden;border:1px solid #182640;">
  <div style="background:linear-gradient(135deg,#0d1a2e,#112240);padding:26px 32px;border-bottom:3px solid ' . $couleur . ';">
    <h2 style="margin:0;color:' . $couleur . ';font-size:18px;">' . $titre . '</h2>
    <p style="margin:5px 0 0;color:#6b7fa0;font-size:12px;">Plateforme Surveillance des Salles Serveurs</p>
  </div>
  <div style="padding:28px 32px;">
    <p style="color:#d4dced;margin-bottom:12px;">Bonjour <strong style="color:white;">' . ($user->nom ?? '') . ' ' . ($user->prenom ?? '') . '</strong>,</p>
    <p style="color:#a0aec0;line-height:1.7;margin-bottom:10px;">' . $ligne1 . '</p>
    <p style="color:#6b7fa0;line-height:1.7;font-size:13px;">' . $ligne2 . '</p>
    ' . $btn . '
  </div>
  <div style="background:#070e1c;padding:14px 32px;border-top:1px solid #182640;text-align:center;">
    <p style="margin:0;color:#3a5070;font-size:12px;">Plateforme de Surveillance — Ne pas répondre à cet email</p>
  </div>
</div>
</body>
</html>';
    }

}
