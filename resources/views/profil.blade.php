@extends('layouts.app')

@section('content')

@php
// $user est passé depuis la route (objet DB frais).
// Le cast (object) protège contre toute désérialisation en array.
$u = (object)(isset($user) ? (array)$user : (array)(session('user') ?? []));

$initiales = strtoupper(substr($u->nom ?? 'U', 0, 1) . substr($u->prenom ?? '', 0, 1));

$roleLabel = match($u->role ?? 'utilisateur') {
    'admin'        => ['ADMIN',        '#f59e0b'],
    'superadmin'   => ['SUPER ADMIN',  '#ef4444'],
    'technicien'   => ['TECHNICIEN',   '#3b82f6'],
    default        => ['UTILISATEUR',  '#2fa84f'],
};

$statutLabel = match($u->validation_status ?? 'en_attente') {
    'valide'    => ['Validé',     '#2fa84f', '#052010'],
    'refuse'    => ['Refusé',     '#ef4444', '#300808'],
    'bloque'    => ['Bloqué',     '#f59e0b', '#2d1800'],
    default     => ['En attente', '#6b7fa0', '#0d1a2e'],
};

$memberSince = ($u->created_at ?? null)
    ? \Carbon\Carbon::parse($u->created_at)->locale('fr')->isoFormat('D MMMM YYYY')
    : 'N/A';
@endphp

<style>

/* ─── BASE ─── */
.profil-wrap{
    display:flex;
    flex-direction:column;
    gap:18px;
    max-width:1300px;
    animation:pfadeIn .5s ease;
}

@keyframes pfadeIn{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);}}

/* ─── CARDS ─── */
.pcard{
    background:#0d1a2e;
    border:1px solid #182640;
    border-radius:16px;
    padding:22px 26px;
    position:relative;
    overflow:hidden;
    transition:border-color .25s;
}
.pcard::before{
    content:'';position:absolute;top:0;left:0;right:0;
    height:2px;
    background:linear-gradient(90deg,transparent,rgba(47,168,79,0.5),transparent);
}
.pcard:hover{border-color:rgba(47,168,79,0.25);}

.pcard-title{
    display:flex;align-items:center;gap:10px;
    font-size:14px;font-weight:bold;
    color:#d4dced;letter-spacing:.5px;
    margin-bottom:20px;
    padding-bottom:14px;
    border-bottom:1px solid #182640;
}
.pcard-title i{color:#2fa84f;font-size:15px;}

/* ─── ALERT MESSAGES ─── */
.p-alert{
    padding:11px 16px;border-radius:9px;
    font-size:13px;font-weight:bold;
    margin-bottom:16px;
    display:flex;align-items:center;gap:9px;
}
.p-alert-ok {background:#052010;border:1px solid #2fa84f;color:#6ee7a0;}
.p-alert-err{background:#2d0808;border:1px solid #ef4444;color:#fca5a5;}

/* ─── HEADER CARD ─── */
.profil-header{
    display:flex;
    align-items:center;
    gap:28px;
    flex-wrap:wrap;
}

/* Avatar */
.avatar-zone{position:relative;flex-shrink:0;}

.avatar-ring{
    width:96px;height:96px;
    border-radius:50%;
    border:3px solid #2fa84f;
    box-shadow:0 0 20px rgba(47,168,79,0.3),0 0 40px rgba(47,168,79,0.1);
    overflow:hidden;
    position:relative;
    animation:avatarGlow 3s ease-in-out infinite;
}

@keyframes avatarGlow{
    0%,100%{box-shadow:0 0 18px rgba(47,168,79,0.3),0 0 36px rgba(47,168,79,0.1);}
    50%{box-shadow:0 0 28px rgba(47,168,79,0.5),0 0 50px rgba(47,168,79,0.2);}
}

.avatar-img{width:100%;height:100%;object-fit:cover;}

.avatar-initials{
    width:100%;height:100%;
    display:flex;align-items:center;justify-content:center;
    font-size:32px;font-weight:bold;
    color:#2fa84f;background:#0a1525;
    letter-spacing:1px;
}

.avatar-edit-btn{
    position:absolute;bottom:-2px;right:-2px;
    width:28px;height:28px;
    background:#2fa84f;border:2px solid #0d1a2e;
    border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;transition:.2s;font-size:11px;color:#060c1a;
}
.avatar-edit-btn:hover{background:#249040;transform:scale(1.1);}

/* Header info */
.header-info{flex:1;min-width:200px;}

.header-name{
    font-size:22px;font-weight:bold;color:#e8edf8;
    margin-bottom:6px;
}

.header-badges{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px;}

.badge{
    display:inline-flex;align-items:center;gap:5px;
    padding:4px 12px;border-radius:20px;
    font-size:11px;font-weight:bold;letter-spacing:.8px;
}

.header-meta{
    display:flex;flex-wrap:wrap;gap:16px;
    font-size:12px;color:#6b7fa0;
}

.header-meta span{display:flex;align-items:center;gap:5px;}
.header-meta i{color:#2fa84f;font-size:11px;}

/* ─── GRID ─── */
.profil-grid{
    display:grid;
    grid-template-columns:1fr 380px;
    gap:18px;
    align-items:start;
}

/* ─── FORM FIELDS ─── */
.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
}

.form-grid .full{grid-column:1/-1;}

.fld{display:flex;flex-direction:column;gap:5px;}

.fld label{
    font-size:11px;font-weight:bold;
    color:#6b7fa0;letter-spacing:.5px;
    text-transform:uppercase;
}

.fld label i{color:#2fa84f;margin-right:4px;}

.fld input, .fld select, .fld textarea{
    background:#0a1525;
    border:1.5px solid #1e3050;
    border-radius:9px;
    padding:10px 13px;
    font-size:13px;color:#d4dced;
    outline:none;
    transition:border-color .2s,box-shadow .2s;
    width:100%;
}

.fld input:focus, .fld select:focus, .fld textarea:focus{
    border-color:#2fa84f;
    box-shadow:0 0 0 3px rgba(47,168,79,0.1);
}

.fld input::placeholder, .fld textarea::placeholder{color:#2d4060;}

.fld .readonly-val{
    background:#070e1c;
    border:1.5px solid #111d35;
    border-radius:9px;
    padding:10px 13px;
    font-size:13px;color:#6b7fa0;
    cursor:not-allowed;
}

/* Input avec œil */
.input-eye{position:relative;}
.input-eye input{padding-right:40px;}
.eye-toggle{
    position:absolute;right:12px;top:50%;transform:translateY(-50%);
    background:none;border:none;color:#6b7fa0;
    cursor:pointer;font-size:14px;transition:color .2s;
}
.eye-toggle:hover{color:#2fa84f;}

/* Boutons */
.btn-primary{
    display:inline-flex;align-items:center;gap:8px;
    padding:10px 22px;
    background:#2fa84f;color:#060c1a;
    border:none;border-radius:9px;
    font-size:13px;font-weight:bold;
    cursor:pointer;transition:.2s;
    text-decoration:none;
}
.btn-primary:hover{background:#249040;transform:translateY(-1px);}

.btn-secondary{
    display:inline-flex;align-items:center;gap:8px;
    padding:10px 22px;
    background:rgba(30,50,100,0.4);color:#c0d4ff;
    border:1.5px solid rgba(60,100,200,0.4);
    border-radius:9px;font-size:13px;font-weight:bold;
    cursor:pointer;transition:.2s;
    text-decoration:none;
}
.btn-secondary:hover{background:rgba(30,80,200,0.4);border-color:rgba(100,150,255,0.7);}

.btn-danger{
    display:inline-flex;align-items:center;gap:8px;
    padding:10px 22px;
    background:rgba(100,10,10,0.4);color:#fca5a5;
    border:1.5px solid rgba(200,40,40,0.4);
    border-radius:9px;font-size:13px;font-weight:bold;
    cursor:pointer;transition:.2s;
}
.btn-danger:hover{background:rgba(150,20,20,0.5);border-color:#ef4444;}

/* Barre force mot de passe */
.strength-bar{height:3px;background:#182640;border-radius:3px;margin-top:5px;overflow:hidden;}
.strength-fill{height:100%;width:0;border-radius:3px;transition:width .3s,background .3s;}
.strength-txt{font-size:11px;color:#6b7fa0;margin-top:3px;}

/* ─── ACTIVITÉS TIMELINE ─── */
.timeline{display:flex;flex-direction:column;gap:0;}

.tl-item{
    display:flex;gap:14px;
    padding-bottom:16px;
    position:relative;
}

.tl-item::before{
    content:'';
    position:absolute;
    left:17px;top:34px;
    width:2px;height:calc(100% - 10px);
    background:linear-gradient(to bottom,#182640,transparent);
}

.tl-item:last-child::before{display:none;}

.tl-dot{
    width:34px;height:34px;
    border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    font-size:13px;
    flex-shrink:0;
    border:2px solid;
}

.tl-body{flex:1;min-width:0;padding-top:4px;}
.tl-label{font-size:13px;font-weight:bold;color:#d4dced;margin-bottom:2px;}
.tl-time{font-size:11px;color:#6b7fa0;}
.tl-desc{font-size:12px;color:#8090b0;margin-top:3px;}

/* ─── TOGGLE SWITCHES ─── */
.notif-list{display:flex;flex-direction:column;gap:12px;}

.notif-item{
    display:flex;justify-content:space-between;align-items:center;
    padding:10px 14px;
    background:#0a1525;border:1px solid #182640;
    border-radius:9px;transition:.2s;
}
.notif-item:hover{border-color:rgba(47,168,79,0.2);}

.notif-left{display:flex;align-items:center;gap:10px;}
.notif-icon{
    width:32px;height:32px;border-radius:8px;
    display:flex;align-items:center;justify-content:center;
    font-size:13px;
}
.notif-name{font-size:13px;font-weight:bold;color:#d4dced;}
.notif-desc{font-size:11px;color:#6b7fa0;margin-top:1px;}

/* Toggle switch */
.toggle{
    position:relative;
    width:42px;height:22px;
    flex-shrink:0;
}
.toggle input{opacity:0;width:0;height:0;}
.toggle-slider{
    position:absolute;cursor:pointer;
    inset:0;background:#1e3050;
    border-radius:22px;transition:.3s;
}
.toggle-slider::before{
    content:'';position:absolute;
    width:16px;height:16px;
    left:3px;bottom:3px;
    background:#6b7fa0;border-radius:50%;
    transition:.3s;
}
.toggle input:checked + .toggle-slider{background:#2fa84f;box-shadow:0 0 8px rgba(47,168,79,0.4);}
.toggle input:checked + .toggle-slider::before{transform:translateX(20px);background:white;}

/* ─── SÉCURITÉ INFO ─── */
.sec-grid{display:flex;flex-direction:column;gap:10px;}

.sec-row{
    display:flex;align-items:center;gap:12px;
    padding:10px 14px;
    background:#0a1525;border:1px solid #182640;
    border-radius:9px;
}

.sec-icon{
    width:34px;height:34px;border-radius:8px;
    display:flex;align-items:center;justify-content:center;
    font-size:13px;flex-shrink:0;
    background:rgba(47,168,79,0.1);color:#2fa84f;
}

.sec-label{font-size:11px;color:#6b7fa0;text-transform:uppercase;letter-spacing:.5px;}
.sec-val{font-size:12px;color:#d4dced;font-weight:bold;margin-top:1px;word-break:break-all;}

/* ─── RESPONSIVE ─── */
@media(max-width:1100px){
    .profil-grid{grid-template-columns:1fr;}
}

@media(max-width:700px){
    .form-grid{grid-template-columns:1fr;}
    .form-grid .full{grid-column:1;}
    .pcard{padding:16px 16px;}
    .profil-header{gap:18px;}
    .avatar-ring{width:80px;height:80px;}
    .avatar-initials{font-size:26px;}
    .header-name{font-size:18px;}
}

@media(max-width:480px){
    .header-badges{gap:6px;}
    .badge{font-size:10px;padding:3px 9px;}
    .header-meta{gap:10px;font-size:11px;}
}

</style>

<div class="profil-wrap">

    {{-- ══════════════════ HEADER CARD ══════════════════ --}}
    <div class="pcard profil-header">

        {{-- Avatar --}}
        <form method="POST" action="/profil/photo" enctype="multipart/form-data" id="photoForm">
            @csrf
            <div class="avatar-zone">
                <div class="avatar-ring">
                    @if($u->photo_profil && file_exists(public_path($u->photo_profil)))
                        <img src="{{ asset($u->photo_profil) }}" class="avatar-img" alt="Photo profil" id="avatarPreview">
                    @else
                        <div class="avatar-initials" id="avatarInitiales">{{ $initiales }}</div>
                    @endif
                </div>
                <label class="avatar-edit-btn" title="Modifier la photo" for="photoInput">
                    <i class="fa-solid fa-camera"></i>
                </label>
                <input type="file" id="photoInput" name="photo_profil" accept="image/*"
                       style="display:none;" onchange="previewPhoto(this)">
            </div>
        </form>

        {{-- Infos header --}}
        <div class="header-info">

            @if(session('success_photo'))
                <div class="p-alert p-alert-ok"><i class="fa-solid fa-circle-check"></i> {{ session('success_photo') }}</div>
            @endif

            <div class="header-name">{{ $u->prenom ?? '' }} {{ $u->nom ?? '' }}</div>

            <div class="header-badges">
                <span class="badge" style="background:rgba(0,0,0,0.3);border:1px solid {{ $roleLabel[1] }};color:{{ $roleLabel[1] }};">
                    <i class="fa-solid fa-shield-halved" style="font-size:10px;"></i>
                    {{ $roleLabel[0] }}
                </span>
                <span class="badge" style="background:{{ $statutLabel[2] }};border:1px solid {{ $statutLabel[1] }};color:{{ $statutLabel[1] }};">
                    <i class="fa-solid fa-circle" style="font-size:7px;"></i>
                    {{ $statutLabel[0] }}
                </span>
            </div>

            <div class="header-meta">
                <span><i class="fa-solid fa-envelope"></i> {{ $u->email ?? 'N/A' }}</span>
                <span><i class="fa-solid fa-calendar"></i> Membre depuis {{ $memberSince }}</span>
                @if($u->pays)
                    <span><i class="fa-solid fa-globe"></i> {{ $u->pays }}</span>
                @endif
            </div>
        </div>

    </div>

    {{-- ══════════════════ GRILLE PRINCIPALE ══════════════════ --}}
    <div class="profil-grid">

        {{-- ─── COLONNE GAUCHE ─── --}}
        <div style="display:flex;flex-direction:column;gap:18px;">

            {{-- INFORMATIONS PERSONNELLES --}}
            <div class="pcard">
                <div class="pcard-title">
                    <i class="fa-solid fa-user-pen"></i>
                    Informations personnelles
                </div>

                @if(session('success_profil'))
                    <div class="p-alert p-alert-ok"><i class="fa-solid fa-circle-check"></i> {{ session('success_profil') }}</div>
                @endif

                <form method="POST" action="/profil/update">
                    @csrf
                    <div class="form-grid">

                        <div class="fld">
                            <label><i class="fa-solid fa-user"></i> Nom</label>
                            <input type="text" name="nom" value="{{ $u->nom ?? '' }}" placeholder="Nom" required>
                        </div>

                        <div class="fld">
                            <label><i class="fa-solid fa-user"></i> Prénom</label>
                            <input type="text" name="prenom" value="{{ $u->prenom ?? '' }}" placeholder="Prénom" required>
                        </div>

                        <div class="fld">
                            <label><i class="fa-solid fa-envelope"></i> Email</label>
                            <div class="readonly-val">{{ $u->email ?? 'N/A' }}</div>
                        </div>

                        <div class="fld">
                            <label><i class="fa-solid fa-phone"></i> Téléphone</label>
                            <input type="text" name="telephone" value="{{ $u->telephone ?? '' }}" placeholder="Téléphone">
                        </div>

                        <div class="fld">
                            <label><i class="fa-solid fa-briefcase"></i> Profession</label>
                            <input type="text" name="profession" value="{{ $u->profession ?? '' }}" placeholder="Profession">
                        </div>

                        <div class="fld">
                            <label><i class="fa-solid fa-building"></i> Organisation</label>
                            <input type="text" name="organisation" value="{{ $u->organisation ?? '' }}" placeholder="Organisation">
                        </div>

                        <div class="fld">
                            <label><i class="fa-solid fa-globe"></i> Pays</label>
                            <div class="readonly-val">{{ $u->pays ?? 'Non renseigné' }}</div>
                        </div>

                        <div class="fld">
                            <label><i class="fa-solid fa-map"></i> Région / État</label>
                            <div class="readonly-val">{{ $u->etat ?? $u->region ?? 'Non renseigné' }}</div>
                        </div>

                        <div class="fld">
                            <label><i class="fa-solid fa-map-pin"></i> Département</label>
                            <div class="readonly-val">{{ $u->departement ?? 'Non renseigné' }}</div>
                        </div>

                        <div class="fld">
                            <label><i class="fa-solid fa-location-dot"></i> Arrondissement</label>
                            <div class="readonly-val">{{ $u->arrondissement ?? 'Non renseigné' }}</div>
                        </div>

                        <div class="fld">
                            <label><i class="fa-solid fa-city"></i> Ville</label>
                            <div class="readonly-val">{{ $u->ville ?? 'Non renseigné' }}</div>
                        </div>

                        <div class="fld">
                            <label><i class="fa-solid fa-house"></i> Quartier</label>
                            <input type="text" name="quartier" value="{{ $u->quartier ?? '' }}" placeholder="Quartier">
                        </div>

                        <div class="fld full">
                            <label><i class="fa-solid fa-map-location-dot"></i> Adresse complète</label>
                            <input type="text" name="adresse" value="{{ $u->adresse ?? '' }}" placeholder="Adresse complète">
                        </div>

                    </div>

                    <div style="margin-top:18px;display:flex;gap:10px;">
                        <button type="submit" class="btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>

            {{-- SÉCURITÉ — CHANGEMENT MOT DE PASSE --}}
            <div class="pcard">
                <div class="pcard-title">
                    <i class="fa-solid fa-lock"></i>
                    Sécurité du compte
                </div>

                @if(session('success_pwd'))
                    <div class="p-alert p-alert-ok"><i class="fa-solid fa-circle-check"></i> {{ session('success_pwd') }}</div>
                @endif
                @if(session('error_pwd'))
                    <div class="p-alert p-alert-err"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error_pwd') }}</div>
                @endif

                <form method="POST" action="/profil/password">
                    @csrf
                    <div class="form-grid">

                        <div class="fld full">
                            <label><i class="fa-solid fa-key"></i> Mot de passe actuel</label>
                            <div class="input-eye">
                                <input type="password" name="current_password" id="pwd0" placeholder="Saisissez votre mot de passe actuel" required>
                                <button type="button" class="eye-toggle" onclick="toggleEye('pwd0','eye0')">
                                    <i class="fa-solid fa-eye" id="eye0"></i>
                                </button>
                            </div>
                        </div>

                        <div class="fld">
                            <label><i class="fa-solid fa-lock"></i> Nouveau mot de passe</label>
                            <div class="input-eye">
                                <input type="password" name="password" id="pwd1" placeholder="Min. 8 caractères"
                                       required minlength="8" oninput="checkStrength(this.value)">
                                <button type="button" class="eye-toggle" onclick="toggleEye('pwd1','eye1')">
                                    <i class="fa-solid fa-eye" id="eye1"></i>
                                </button>
                            </div>
                            <div class="strength-bar"><div class="strength-fill" id="sfill"></div></div>
                            <div class="strength-txt" id="stxt"></div>
                        </div>

                        <div class="fld">
                            <label><i class="fa-solid fa-lock"></i> Confirmer le mot de passe</label>
                            <div class="input-eye">
                                <input type="password" name="password_confirmation" id="pwd2"
                                       placeholder="Répétez le mot de passe"
                                       required minlength="8" oninput="checkMatch()">
                                <button type="button" class="eye-toggle" onclick="toggleEye('pwd2','eye2')">
                                    <i class="fa-solid fa-eye" id="eye2"></i>
                                </button>
                            </div>
                            <div class="strength-txt" id="mtxt"></div>
                        </div>

                    </div>

                    <div style="margin-top:18px;">
                        <button type="submit" class="btn-danger" id="pwdBtn">
                            <i class="fa-solid fa-shield-halved"></i> Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>

        </div>

        {{-- ─── COLONNE DROITE ─── --}}
        <div style="display:flex;flex-direction:column;gap:18px;">

            {{-- ACTIVITÉS RÉCENTES --}}
            <div class="pcard">
                <div class="pcard-title">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Activités récentes
                </div>

                <div class="timeline">

                    {{-- Connexion actuelle --}}
                    <div class="tl-item">
                        <div class="tl-dot" style="background:rgba(47,168,79,0.12);border-color:#2fa84f;color:#2fa84f;">
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </div>
                        <div class="tl-body">
                            <div class="tl-label">Connexion</div>
                            <div class="tl-time">{{ now()->locale('fr')->isoFormat('D MMM YYYY, HH:mm') }}</div>
                            <div class="tl-desc">Session active en cours</div>
                        </div>
                    </div>

                    {{-- Alertes récentes --}}
                    @if(isset($alertes) && $alertes->count())
                        @foreach($alertes->take(4) as $alerte)
                        <div class="tl-item">
                            <div class="tl-dot" style="background:rgba(239,68,68,0.1);border-color:#ef4444;color:#ef4444;">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                            <div class="tl-body">
                                <div class="tl-label">Alerte système</div>
                                <div class="tl-time">
                                    {{ $alerte->created_at ? \Carbon\Carbon::parse($alerte->created_at)->locale('fr')->isoFormat('D MMM, HH:mm') : 'N/A' }}
                                </div>
                                <div class="tl-desc">
                                    {{ isset($alerte->message) ? Str::limit($alerte->message, 55) : ($alerte->type ?? 'Alerte capteur') }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="tl-item">
                            <div class="tl-dot" style="background:rgba(107,127,160,0.1);border-color:#6b7fa0;color:#6b7fa0;">
                                <i class="fa-solid fa-inbox"></i>
                            </div>
                            <div class="tl-body">
                                <div class="tl-label" style="color:#6b7fa0;">Aucune alerte récente</div>
                                <div class="tl-time">Système nominal</div>
                            </div>
                        </div>
                    @endif

                    {{-- Création compte --}}
                    <div class="tl-item">
                        <div class="tl-dot" style="background:rgba(59,130,246,0.1);border-color:#3b82f6;color:#3b82f6;">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div class="tl-body">
                            <div class="tl-label">Compte créé</div>
                            <div class="tl-time">{{ $memberSince }}</div>
                            <div class="tl-desc">Inscription sur la plateforme</div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- PRÉFÉRENCES NOTIFICATIONS --}}
            <div class="pcard">
                <div class="pcard-title">
                    <i class="fa-solid fa-bell"></i>
                    Préférences notifications
                </div>

                <div class="notif-list">

                    @php
                    $notifs = [
                        ['id'=>'n_sms',   'icon'=>'fa-mobile-screen-button', 'color'=>'#2fa84f',   'bg'=>'rgba(47,168,79,0.1)',   'name'=>'Alertes SMS',        'desc'=>'Via module GSM SIM900'],
                        ['id'=>'n_email', 'icon'=>'fa-envelope',             'color'=>'#3b82f6',   'bg'=>'rgba(59,130,246,0.1)', 'name'=>'Alertes Email',      'desc'=>'Notification par email'],
                        ['id'=>'n_temp',  'icon'=>'fa-temperature-high',     'color'=>'#ef4444',   'bg'=>'rgba(239,68,68,0.1)',  'name'=>'Température',        'desc'=>'Seuil critique capteur'],
                        ['id'=>'n_gaz',   'icon'=>'fa-smog',                 'color'=>'#f59e0b',   'bg'=>'rgba(245,158,11,0.1)', 'name'=>'Gaz / Fumée',        'desc'=>'Détection gaz MQ135'],
                        ['id'=>'n_pir',   'icon'=>'fa-person-walking',       'color'=>'#a855f7',   'bg'=>'rgba(168,85,247,0.1)', 'name'=>'Mouvement PIR',      'desc'=>'Détection intrusion'],
                        ['id'=>'n_pow',   'icon'=>'fa-bolt',                 'color'=>'#06b6d4',   'bg'=>'rgba(6,182,212,0.1)',  'name'=>'Puissance électrique','desc'=>'Anomalie courant'],
                    ];
                    @endphp

                    @foreach($notifs as $n)
                    <div class="notif-item">
                        <div class="notif-left">
                            <div class="notif-icon" style="background:{{ $n['bg'] }};color:{{ $n['color'] }};">
                                <i class="fa-solid {{ $n['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="notif-name">{{ $n['name'] }}</div>
                                <div class="notif-desc">{{ $n['desc'] }}</div>
                            </div>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" class="notif-chk" data-id="{{ $n['id'] }}"
                                   {{ in_array($n['id'], ['n_sms','n_email','n_temp','n_gaz']) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    @endforeach

                </div>
            </div>

            {{-- SÉCURITÉ AVANCÉE --}}
            <div class="pcard">
                <div class="pcard-title">
                    <i class="fa-solid fa-shield-halved"></i>
                    Informations de connexion
                </div>

                <div class="sec-grid">

                    <div class="sec-row">
                        <div class="sec-icon"><i class="fa-solid fa-network-wired"></i></div>
                        <div>
                            <div class="sec-label">Adresse IP</div>
                            <div class="sec-val">{{ request()->ip() }}</div>
                        </div>
                    </div>

                    <div class="sec-row">
                        <div class="sec-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6;">
                            <i class="fa-solid fa-display"></i>
                        </div>
                        <div>
                            <div class="sec-label">Appareil</div>
                            <div class="sec-val" id="device-info">Détection...</div>
                        </div>
                    </div>

                    <div class="sec-row">
                        <div class="sec-icon" style="background:rgba(168,85,247,0.1);color:#a855f7;">
                            <i class="fa-brands fa-chrome"></i>
                        </div>
                        <div>
                            <div class="sec-label">Navigateur</div>
                            <div class="sec-val" id="browser-info">Détection...</div>
                        </div>
                    </div>

                    <div class="sec-row">
                        <div class="sec-icon" style="background:rgba(245,158,11,0.1);color:#f59e0b;">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <div class="sec-label">Dernière activité</div>
                            <div class="sec-val" id="last-act">—</div>
                        </div>
                    </div>

                    <div class="sec-row">
                        <div class="sec-icon" style="background:rgba(47,168,79,0.1);color:#2fa84f;">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <div class="sec-label">Statut session</div>
                            <div class="sec-val" style="color:#2fa84f;">Active &nbsp;●</div>
                        </div>
                    </div>

                </div>
            </div>

        </div>{{-- fin col droite --}}

    </div>{{-- fin grid --}}

</div>{{-- fin wrap --}}

<script>
/* ─── Aperçu photo ─── */
function previewPhoto(input){
    if(!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const ring = input.closest('.avatar-zone').querySelector('.avatar-ring');
        ring.innerHTML = `<img src="${e.target.result}" class="avatar-img" style="width:100%;height:100%;object-fit:cover;">`;
    };
    reader.readAsDataURL(input.files[0]);
    document.getElementById('photoForm').submit();
}

/* ─── Afficher/masquer mot de passe ─── */
function toggleEye(id, iconId){
    const inp = document.getElementById(id);
    const ico = document.getElementById(iconId);
    if(inp.type === 'password'){
        inp.type = 'text';
        ico.classList.replace('fa-eye','fa-eye-slash');
    } else {
        inp.type = 'password';
        ico.classList.replace('fa-eye-slash','fa-eye');
    }
}

/* ─── Force mot de passe ─── */
function checkStrength(v){
    const fill = document.getElementById('sfill');
    const txt  = document.getElementById('stxt');
    let s = 0;
    if(v.length >= 8)         s++;
    if(/[A-Z]/.test(v))       s++;
    if(/[0-9]/.test(v))       s++;
    if(/[^A-Za-z0-9]/.test(v))s++;
    const levels = [
        {w:'0%',  c:'#c0392b', l:''},
        {w:'25%', c:'#c0392b', l:'Faible'},
        {w:'50%', c:'#d97706', l:'Moyen'},
        {w:'75%', c:'#2e86c1', l:'Bon'},
        {w:'100%',c:'#2fa84f', l:'Excellent'},
    ];
    fill.style.width      = levels[s].w;
    fill.style.background = levels[s].c;
    txt.textContent       = levels[s].l;
    txt.style.color       = levels[s].c;
    checkMatch();
}

function checkMatch(){
    const p1  = document.getElementById('pwd1').value;
    const p2  = document.getElementById('pwd2').value;
    const mt  = document.getElementById('mtxt');
    const btn = document.getElementById('pwdBtn');
    if(!p2){ mt.textContent=''; btn.disabled=false; return; }
    if(p1 === p2 && p1.length >= 8){
        mt.textContent='Les mots de passe correspondent'; mt.style.color='#2fa84f'; btn.disabled=false;
    } else {
        mt.textContent='Les mots de passe ne correspondent pas'; mt.style.color='#c0392b'; btn.disabled=true;
    }
}

/* ─── Notifications (localStorage) ─── */
document.querySelectorAll('.notif-chk').forEach(chk => {
    const key = 'notif_' + chk.dataset.id;
    if(localStorage.getItem(key) !== null){
        chk.checked = localStorage.getItem(key) === '1';
    }
    chk.addEventListener('change', () => {
        localStorage.setItem(key, chk.checked ? '1' : '0');
    });
});

/* ─── Infos navigateur / appareil ─── */
(function(){
    const ua  = navigator.userAgent;
    let br    = 'Navigateur';
    let dev   = 'Ordinateur';

    if(/Edg/.test(ua))       br = 'Microsoft Edge';
    else if(/Chrome/.test(ua)) br = 'Google Chrome';
    else if(/Firefox/.test(ua))br = 'Mozilla Firefox';
    else if(/Safari/.test(ua)) br = 'Safari';
    else if(/Opera/.test(ua))  br = 'Opera';

    if(/Android/.test(ua))     dev = 'Android';
    else if(/iPhone/.test(ua)) dev = 'iPhone';
    else if(/iPad/.test(ua))   dev = 'iPad';
    else if(/Macintosh/.test(ua)) dev = 'Mac';
    else if(/Windows/.test(ua))   dev = 'Windows';
    else if(/Linux/.test(ua))     dev = 'Linux';

    document.getElementById('browser-info').textContent = br;
    document.getElementById('device-info').textContent  = dev;

    const now = new Date();
    document.getElementById('last-act').textContent =
        now.toLocaleDateString('fr-FR') + ' à ' + now.toLocaleTimeString('fr-FR');
})();
</script>

@endsection
