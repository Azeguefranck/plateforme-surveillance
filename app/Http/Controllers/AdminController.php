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

    public static function mailToken(int $id, string $action): string
    {
        return substr(hash_hmac('sha256', $id . '|' . $action, config('app.key')), 0, 40);
    }

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
                        'Vous pouvez maintenant vous authentifier et accéder à votre espace de surveillance.',
                        'S\'AUTHENTIFIER',
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
        $svgCheck = '<svg style="display:inline-block;vertical-align:middle" width="18" height="18" viewBox="0 0 512 512"><path fill="' . $couleur . '" d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>';
        $svgBan   = '<svg style="display:inline-block;vertical-align:middle" width="18" height="18" viewBox="0 0 512 512"><path fill="' . $couleur . '" d="M367.2 412.5L99.5 144.8C77.1 176.1 64 214.5 64 256c0 106 86 192 192 192c41.5 0 79.9-13.1 111.2-35.5zm45.3-45.3C434.9 335.9 448 297.5 448 256c0-106-86-192-192-192c-41.5 0-79.9 13.1-111.2 35.5L412.5 367.2zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256z"/></svg>';
        $svgClock = '<svg style="display:inline-block;vertical-align:middle" width="18" height="18" viewBox="0 0 512 512"><path fill="' . $couleur . '" d="M256 0a256 256 0 1 1 0 512A256 256 0 1 1 256 0zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.5 33.3-6.5s4.5-25.9-6.5-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/></svg>';
        $svgUser  = '<svg style="display:inline-block;vertical-align:middle" width="16" height="16" viewBox="0 0 448 512"><path fill="' . $couleur . '" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>';

        $icoTitre = '';
        if (str_contains($titre, '✅') || str_contains($titre, 'créé') || str_contains($titre, 'validé')) {
            $icoTitre = $svgCheck;
        } elseif (str_contains($titre, '❌') || str_contains($titre, 'refus') || str_contains($titre, 'non accept')) {
            $icoTitre = $svgBan;
        } elseif (str_contains($titre, '⏳') || str_contains($titre, 'cours') || str_contains($titre, 'attente')) {
            $icoTitre = $svgClock;
        } else {
            $icoTitre = $svgUser;
        }

        $titrePropre = trim(preg_replace('/[\x{1F300}-\x{1FFFF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|✅|❌|⏳/u', '', $titre));

        $btn = '';
        if ($btnTxt && $btnUrl) {
            $svgLogin = '<svg style="display:inline-block;vertical-align:middle;margin-right:6px" width="14" height="14" viewBox="0 0 512 512"><path fill="' . $btnColor . '" d="M352 96l64 0c17.7 0 32 14.3 32 32l0 256c0 17.7-14.3 32-32 32l-64 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l64 0c53 0 96-43 96-96l0-256c0-53-43-96-96-96l-64 0c-17.7 0-32 14.3-32 32s14.3 32 32 32zm-9.4 182.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L242.7 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z"/></svg>';
            $btn = '<div style="text-align:center;margin:24px 0 0">'
                 . '<a href="' . $btnUrl . '" style="display:inline-block;padding:13px 30px;background:' . $btnBg . ';color:' . $btnColor . ';text-decoration:none;border-radius:8px;font-weight:bold;font-size:14px;letter-spacing:.5px">'
                 . $svgLogin . $btnTxt
                 . '</a></div>';
        }

        return '<!DOCTYPE html><html lang="fr"><head>'
            . '<meta charset="UTF-8">'
            . '<meta name="viewport" content="width=device-width,initial-scale=1.0">'
            . '<meta name="x-apple-disable-message-reformatting">'
            . '<style>'
            . 'body{margin:0;padding:0;background:#060c1a;font-family:Arial,sans-serif;-webkit-text-size-adjust:100%}'
            . '.ow{width:100%;background:#060c1a}'
            . '.iw{max-width:520px;margin:0 auto;background:#0d1a2e;border-radius:16px;overflow:hidden;border:1px solid #182640}'
            . '.hd{background:linear-gradient(135deg,#0d1a2e,#112240);padding:22px 24px;border-bottom:3px solid ' . $couleur . '}'
            . '.ht{margin:0;color:' . $couleur . ';font-size:16px;font-weight:800;word-break:break-word}'
            . '.hs{margin:5px 0 0;color:#6b7fa0;font-size:11px}'
            . '.bd{padding:22px 24px}'
            . '.ft{background:#070e1c;padding:12px 24px;border-top:1px solid #182640;text-align:center}'
            . '@media only screen and (max-width:600px){'
            . '.iw{border-radius:0!important}'
            . '.hd,.bd{padding:16px!important}'
            . '.ht{font-size:14px!important}'
            . '}'
            . '</style></head><body>'
            . '<table class="ow" cellpadding="0" cellspacing="0" role="presentation"><tr><td align="center" style="padding:20px 10px">'
            . '<table class="iw" cellpadding="0" cellspacing="0" role="presentation"><tr><td>'
            . '<div class="hd">'
            . '<h2 class="ht">' . $icoTitre . '&nbsp;' . htmlspecialchars($titrePropre) . '</h2>'
            . '<p class="hs">Plateforme Surveillance des Salles Serveurs</p>'
            . '</div>'
            . '<div class="bd">'
            . '<p style="color:#d4dced;margin:0 0 12px">Bonjour&nbsp;<strong style="color:#fff">' . htmlspecialchars(($user->prenom ?? '') . ' ' . ($user->nom ?? '')) . '</strong>,</p>'
            . '<p style="color:#a0aec0;line-height:1.7;margin:0 0 10px">' . $ligne1 . '</p>'
            . '<p style="color:#6b7fa0;line-height:1.7;font-size:13px;margin:0">' . $ligne2 . '</p>'
            . $btn
            . '</div>'
            . '<div class="ft"><p style="margin:0;color:#3a5070;font-size:11px">Plateforme Surveillance &mdash; Ne pas r&eacute;pondre &agrave; cet email</p></div>'
            . '</td></tr></table>'
            . '</td></tr></table>'
            . '</body></html>';
    }

}
