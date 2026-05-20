@extends('layouts.app')

@section('content')

@php
$u = $user;
$nom_complet = trim(($u->prenom ?? '') . ' ' . ($u->nom ?? '')) ?: ($u->name ?? 'Utilisateur');
$role_label  = match($u->role ?? '') {
    'admin'       => 'Administrateur',
    'super_admin' => 'Super Admin',
    default       => ucfirst($u->role ?? 'Utilisateur'),
};
$statut_color = match($u->validation_status ?? '') {
    'valide'    => '#33ff88',
    'en_attente'=> '#ffd633',
    'bloque'    => '#ff5733',
    default     => '#8899cc',
};
$statut_label = match($u->validation_status ?? '') {
    'valide'    => 'ACTIF',
    'en_attente'=> 'EN ATTENTE',
    'bloque'    => 'BLOQUÉ',
    default     => strtoupper($u->validation_status ?? ''),
};
$photo_url = $u->photo_profil ? asset('storage/' . $u->photo_profil) : null;
$initiales = strtoupper(mb_substr($u->prenom ?? 'U', 0, 1) . mb_substr($u->nom ?? '', 0, 1));
$date_inscription = $u->created_at ? \Carbon\Carbon::parse($u->created_at)->format('d/m/Y') : '—';
@endphp

<style>
/* ── Base ─────────────────────────────────────────────── */
*{box-sizing:border-box}
body{background:#060d1f;color:#e0e8ff;font-family:'Segoe UI',Arial,sans-serif}

/* ── Page header ──────────────────────────────────────── */
.profil-header{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:28px;flex-wrap:wrap;gap:12px;
}
.profil-header h1{
  font-size:24px;font-weight:700;color:#fff;
  letter-spacing:1px;display:flex;align-items:center;gap:10px;
}
.profil-header h1 span{color:#33ff88}
.breadcrumb{font-size:12px;color:#5a6a99}
.breadcrumb a{color:#33ff88;text-decoration:none}

/* ── Flash messages ───────────────────────────────────── */
.flash{
  padding:12px 18px;border-radius:10px;margin-bottom:20px;
  font-size:14px;font-weight:600;display:flex;align-items:center;gap:10px;
  animation:fadeUp .4s ease;
}
.flash-success{background:rgba(51,255,136,.1);border:1px solid rgba(51,255,136,.3);color:#33ff88}
.flash-error  {background:rgba(255,87,51,.1);border:1px solid rgba(255,87,51,.3);color:#ff5733}
@keyframes fadeUp{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}

/* ── Grid layouts ─────────────────────────────────────── */
.grid-top{display:grid;grid-template-columns:300px 1fr;gap:20px;margin-bottom:20px}
.grid-mid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px}
.grid-bot{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px}
@media(max-width:1024px){.grid-top{grid-template-columns:1fr}}
@media(max-width:768px){.grid-mid,.grid-bot{grid-template-columns:1fr}}

/* ── Card base ────────────────────────────────────────── */
.card{
  background:linear-gradient(135deg,#0e1a38,#0c1530);
  border:1px solid #1e2f5a;border-radius:18px;
  padding:26px;position:relative;overflow:hidden;
  transition:border-color .3s;
}
.card:hover{border-color:rgba(51,255,136,.2)}
.card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:2px;
  background:linear-gradient(90deg,transparent,rgba(51,255,136,.4),transparent);
  opacity:0;transition:.3s;
}
.card:hover::before{opacity:1}
.card-title{
  font-size:13px;font-weight:700;letter-spacing:1.5px;color:#8899cc;
  text-transform:uppercase;margin-bottom:20px;
  display:flex;align-items:center;gap:8px;
}
.card-title::before{
  content:'';width:3px;height:14px;border-radius:2px;
  background:linear-gradient(180deg,#33ff88,#33b5ff);flex-shrink:0;
}

/* ── Avatar card ──────────────────────────────────────── */
.avatar-card{text-align:center}
.avatar-wrap{
  position:relative;display:inline-block;margin:0 auto 16px;
}
.avatar-ring{
  width:110px;height:110px;border-radius:50%;
  border:3px solid #1e2f5a;padding:4px;
  background:linear-gradient(135deg,#0e1a38,#060d1f);
  position:relative;
  box-shadow:0 0 0 1px rgba(51,255,136,.15),0 0 30px rgba(51,255,136,.1);
  animation:ringPulse 3s ease-in-out infinite;
}
@keyframes ringPulse{
  0%,100%{box-shadow:0 0 0 1px rgba(51,255,136,.15),0 0 30px rgba(51,255,136,.1)}
  50%{box-shadow:0 0 0 3px rgba(51,255,136,.25),0 0 45px rgba(51,255,136,.18)}
}
.avatar-img{
  width:100%;height:100%;border-radius:50%;object-fit:cover;
  display:block;
}
.avatar-initials{
  width:100%;height:100%;border-radius:50%;
  background:linear-gradient(135deg,#1e3a6e,#0d2550);
  display:flex;align-items:center;justify-content:center;
  font-size:32px;font-weight:700;color:#33ff88;
  letter-spacing:2px;
}
.avatar-online{
  position:absolute;bottom:6px;right:6px;
  width:16px;height:16px;border-radius:50%;
  background:#33ff88;border:2px solid #060d1f;
  animation:dotPulse 2s infinite;
}
@keyframes dotPulse{0%,100%{transform:scale(1)}50%{transform:scale(1.3)}}

.avatar-name{font-size:18px;font-weight:700;color:#fff;margin-bottom:6px}
.avatar-email{font-size:13px;color:#8899cc;margin-bottom:12px}

.badge{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;letter-spacing:.5px;margin:3px}
.badge-role {background:rgba(51,181,255,.1);color:#33b5ff;border:1px solid rgba(51,181,255,.25)}
.badge-statut{border:1px solid;border-radius:20px;padding:4px 12px;font-size:11px;font-weight:700;letter-spacing:.5px}

.avatar-meta{margin-top:14px;display:flex;flex-direction:column;gap:8px}
.meta-row{display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:rgba(255,255,255,.03);border-radius:8px;font-size:13px}
.meta-row .ml{color:#5a6a99}
.meta-row .mr{color:#c7d2ff;font-weight:600}

/* Photo upload button */
.btn-photo{
  display:inline-flex;align-items:center;gap:7px;
  padding:8px 18px;border-radius:8px;
  border:1px solid #1e2f5a;background:rgba(255,255,255,.04);
  color:#8899cc;font-size:12px;font-weight:600;
  cursor:pointer;transition:.25s;margin-top:12px;
}
.btn-photo:hover{border-color:#33ff88;color:#33ff88;background:rgba(51,255,136,.05)}
.btn-photo input{display:none}

/* ── Form fields ──────────────────────────────────────── */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:600px){.form-grid{grid-template-columns:1fr}}
.form-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px}
@media(max-width:700px){.form-grid-3{grid-template-columns:1fr 1fr}}
@media(max-width:480px){.form-grid-3{grid-template-columns:1fr}}

.field{display:flex;flex-direction:column;gap:5px}
.field label{font-size:11px;font-weight:700;color:#5a6a99;letter-spacing:.5px;text-transform:uppercase}
.field input,.field select,.field textarea{
  background:rgba(255,255,255,.04);border:1px solid #1e2f5a;
  border-radius:8px;padding:10px 14px;color:#e0e8ff;
  font-size:14px;outline:none;transition:.25s;
  font-family:inherit;
}
.field input:focus,.field select:focus,.field textarea:focus{
  border-color:#33ff88;
  box-shadow:0 0 0 3px rgba(51,255,136,.08);
}
.field input[readonly]{color:#5a6a99;cursor:default}
.field input[readonly]:focus{border-color:#1e2f5a;box-shadow:none}
.field select option{background:#0e1a38;color:#e0e8ff}

/* Password field with toggle */
.pw-wrap{position:relative}
.pw-wrap input{width:100%;padding-right:44px}
.pw-eye{
  position:absolute;right:12px;top:50%;transform:translateY(-50%);
  background:none;border:none;color:#5a6a99;cursor:pointer;
  font-size:16px;transition:.2s;padding:0;line-height:1;
}
.pw-eye:hover{color:#33ff88}

/* Password strength */
.pw-strength{margin-top:6px;display:flex;gap:4px}
.pw-bar{height:3px;border-radius:2px;flex:1;background:#1e2f5a;transition:.3s}

/* ── Buttons ──────────────────────────────────────────── */
.btn-primary{
  display:inline-flex;align-items:center;gap:8px;
  padding:11px 24px;border-radius:9px;border:none;
  background:linear-gradient(135deg,rgba(51,255,136,.15),rgba(51,255,136,.08));
  border:1px solid rgba(51,255,136,.3);
  color:#33ff88;font-size:14px;font-weight:700;
  cursor:pointer;transition:.25s;letter-spacing:.5px;
}
.btn-primary:hover{
  background:linear-gradient(135deg,rgba(51,255,136,.22),rgba(51,255,136,.12));
  box-shadow:0 0 20px rgba(51,255,136,.25);
  transform:translateY(-1px);
}
.btn-danger{
  display:inline-flex;align-items:center;gap:8px;
  padding:11px 24px;border-radius:9px;border:none;
  background:linear-gradient(135deg,rgba(255,87,51,.12),rgba(255,87,51,.06));
  border:1px solid rgba(255,87,51,.3);
  color:#ff7755;font-size:14px;font-weight:700;
  cursor:pointer;transition:.25s;
}
.btn-danger:hover{
  background:linear-gradient(135deg,rgba(255,87,51,.2),rgba(255,87,51,.1));
  box-shadow:0 0 20px rgba(255,87,51,.2);transform:translateY(-1px);
}
.btn-actions{display:flex;gap:12px;margin-top:20px;flex-wrap:wrap}

/* ── Section separator ────────────────────────────────── */
.section-sep{
  height:1px;margin:24px 0;
  background:linear-gradient(90deg,transparent,#1e2f5a,transparent);
}

/* ── Timeline ─────────────────────────────────────────── */
.timeline{display:flex;flex-direction:column;gap:0}
.tl-item{
  display:flex;gap:14px;padding:12px 0;
  position:relative;
}
.tl-item:not(:last-child)::after{
  content:'';position:absolute;left:15px;top:40px;bottom:0;
  width:1px;background:linear-gradient(180deg,#1e2f5a,transparent);
}
.tl-dot{
  width:30px;height:30px;border-radius:50%;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-size:12px;border:1px solid;margin-top:2px;
}
.tl-dot.g{background:rgba(51,255,136,.1);border-color:rgba(51,255,136,.3);color:#33ff88}
.tl-dot.r{background:rgba(255,87,51,.1);border-color:rgba(255,87,51,.3);color:#ff5733}
.tl-dot.y{background:rgba(255,214,51,.1);border-color:rgba(255,214,51,.3);color:#ffd633}
.tl-dot.b{background:rgba(51,181,255,.1);border-color:rgba(51,181,255,.3);color:#33b5ff}
.tl-body{flex:1;min-width:0}
.tl-msg{font-size:13px;color:#c7d2ff;line-height:1.4}
.tl-time{font-size:11px;color:#3a4a6a;margin-top:3px}

/* ── Notification switches ────────────────────────────── */
.switch-list{display:flex;flex-direction:column;gap:10px}
.switch-row{
  display:flex;align-items:center;justify-content:space-between;
  padding:11px 14px;background:rgba(255,255,255,.03);
  border-radius:10px;border:1px solid transparent;transition:.25s;
}
.switch-row:hover{border-color:#1e2f5a;background:rgba(255,255,255,.05)}
.switch-label{font-size:13px;color:#c7d2ff;display:flex;align-items:center;gap:9px}
.switch-ico{font-size:15px;width:20px;text-align:center}
/* Toggle switch */
.toggle{position:relative;display:inline-block;width:44px;height:24px;flex-shrink:0}
.toggle input{opacity:0;width:0;height:0}
.toggle-slider{
  position:absolute;inset:0;background:#1e2f5a;
  border-radius:24px;cursor:pointer;transition:.3s;
}
.toggle-slider::before{
  content:'';position:absolute;height:18px;width:18px;
  left:3px;top:3px;background:#5a6a99;
  border-radius:50%;transition:.3s;
}
.toggle input:checked + .toggle-slider{background:rgba(51,255,136,.25);border:1px solid rgba(51,255,136,.4)}
.toggle input:checked + .toggle-slider::before{transform:translateX(20px);background:#33ff88;box-shadow:0 0 8px rgba(51,255,136,.5)}

/* ── Security info ────────────────────────────────────── */
.sec-list{display:flex;flex-direction:column;gap:10px}
.sec-row{
  display:flex;align-items:flex-start;gap:12px;
  padding:12px;background:rgba(255,255,255,.03);
  border-radius:10px;
}
.sec-ico{
  width:34px;height:34px;border-radius:8px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-size:14px;background:rgba(51,181,255,.08);border:1px solid rgba(51,181,255,.15);
}
.sec-content{flex:1;min-width:0}
.sec-key{font-size:11px;color:#5a6a99;letter-spacing:.5px;text-transform:uppercase;margin-bottom:3px}
.sec-val{font-size:13px;color:#c7d2ff;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.sec-val.online{color:#33ff88}

/* ── Stats mini ───────────────────────────────────────── */
.stats-mini{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:16px}
.stat-mini{
  background:rgba(255,255,255,.03);border-radius:10px;
  padding:14px;text-align:center;border:1px solid transparent;transition:.25s;
}
.stat-mini:hover{border-color:#1e2f5a}
.stat-mini-num{font-size:24px;font-weight:700;color:#33ff88;line-height:1}
.stat-mini-label{font-size:11px;color:#5a6a99;margin-top:5px;letter-spacing:.5px;text-transform:uppercase}

/* ── Scroll ───────────────────────────────────────────── */
.card-scroll{max-height:380px;overflow-y:auto;padding-right:4px}
.card-scroll::-webkit-scrollbar{width:3px}
.card-scroll::-webkit-scrollbar-track{background:#0a1225}
.card-scroll::-webkit-scrollbar-thumb{background:#33ff88;border-radius:2px}
</style>


{{-- ── Header ── --}}
<div class="profil-header">
  <h1>👤 Mon <span>Profil</span></h1>
  <div class="breadcrumb"><a href="/dashboard">Dashboard</a> / Mon Profil</div>
</div>

{{-- ── Flash messages ── --}}
@if(session('success_profil'))
  <div class="flash flash-success">✅ {{ session('success_profil') }}</div>
@endif
@if(session('success_password'))
  <div class="flash flash-success">🔐 {{ session('success_password') }}</div>
@endif
@if(session('success_photo'))
  <div class="flash flash-success">📷 {{ session('success_photo') }}</div>
@endif
@if(session('error_password'))
  <div class="flash flash-error">❌ {{ session('error_password') }}</div>
@endif
@if($errors->any())
  @foreach($errors->all() as $err)
    <div class="flash flash-error">⚠️ {{ $err }}</div>
  @endforeach
@endif


{{-- ═══════════════════════════════════════════════════════
     ROW 1 : Avatar  |  Informations personnelles
════════════════════════════════════════════════════════ --}}
<div class="grid-top">

  {{-- ── Carte Avatar ── --}}
  <div class="card avatar-card">
    <div class="card-title">Identité</div>

    <div class="avatar-wrap">
      <div class="avatar-ring">
        @if($photo_url)
          <img class="avatar-img" src="{{ $photo_url }}" alt="Photo profil" id="avatar-preview">
        @else
          <div class="avatar-initials" id="avatar-preview-wrap">{{ $initiales }}</div>
        @endif
      </div>
      <div class="avatar-online"></div>
    </div>

    <div class="avatar-name">{{ $nom_complet }}</div>
    <div class="avatar-email">{{ $u->email ?? '' }}</div>

    <div>
      <span class="badge badge-role">{{ $role_label }}</span>
      <span class="badge badge-statut" style="color:{{ $statut_color }};border-color:{{ $statut_color }}20;background:{{ $statut_color }}10">
        {{ $statut_label }}
      </span>
    </div>

    {{-- Photo upload form --}}
    <form action="/profil/photo" method="POST" enctype="multipart/form-data" id="form-photo">
      @csrf
      <label class="btn-photo">
        📷 Modifier la photo
        <input type="file" name="photo_profil" accept="image/*" id="photo-input" onchange="previewPhoto(this);document.getElementById('form-photo').submit()">
      </label>
    </form>

    <div class="avatar-meta">
      <div class="meta-row">
        <span class="ml">Inscription</span>
        <span class="mr">{{ $date_inscription }}</span>
      </div>
      <div class="meta-row">
        <span class="ml">Rôle</span>
        <span class="mr">{{ $role_label }}</span>
      </div>
      <div class="meta-row">
        <span class="ml">Statut</span>
        <span class="mr" style="color:{{ $statut_color }}">{{ $statut_label }}</span>
      </div>
      @if($u->pays ?? false)
      <div class="meta-row">
        <span class="ml">Pays</span>
        <span class="mr">{{ $u->pays }}</span>
      </div>
      @endif
    </div>
  </div>

  {{-- ── Informations personnelles ── --}}
  <div class="card">
    <div class="card-title">Informations personnelles</div>

    <form action="/profil/update" method="POST">
      @csrf

      <div class="form-grid">
        <div class="field">
          <label>Prénom</label>
          <input type="text" name="prenom" value="{{ old('prenom', $u->prenom ?? '') }}" required>
        </div>
        <div class="field">
          <label>Nom</label>
          <input type="text" name="nom" value="{{ old('nom', $u->nom ?? '') }}" required>
        </div>
        <div class="field">
          <label>Email</label>
          <input type="email" value="{{ $u->email ?? '' }}" readonly>
        </div>
        <div class="field">
          <label>Téléphone</label>
          <input type="text" name="telephone" value="{{ old('telephone', $u->telephone ?? '') }}" required>
        </div>
      </div>

      <div class="section-sep"></div>

      <div class="form-grid-3">
        <div class="field">
          <label>Pays</label>
          <input type="text" name="pays" value="{{ old('pays', $u->pays ?? '') }}">
        </div>
        <div class="field">
          <label>Région / Province</label>
          <input type="text" name="region" value="{{ old('region', $u->region ?? '') }}">
        </div>
        <div class="field">
          <label>Département</label>
          <input type="text" name="departement" value="{{ old('departement', $u->departement ?? '') }}">
        </div>
        <div class="field">
          <label>Arrondissement</label>
          <input type="text" name="arrondissement" value="{{ old('arrondissement', $u->arrondissement ?? '') }}">
        </div>
        <div class="field">
          <label>Ville / Résidence</label>
          <input type="text" name="ville_residence" value="{{ old('ville_residence', $u->ville_residence ?? '') }}">
        </div>
        <div class="field">
          <label>Quartier</label>
          <input type="text" name="quartier" value="{{ old('quartier', $u->quartier ?? '') }}">
        </div>
      </div>

      <div class="section-sep"></div>

      <div class="form-grid">
        <div class="field">
          <label>Profession</label>
          <input type="text" name="profession" value="{{ old('profession', $u->profession ?? '') }}">
        </div>
        <div class="field">
          <label>Organisation</label>
          <input type="text" name="organisation" value="{{ old('organisation', $u->organisation ?? '') }}">
        </div>
      </div>
      <div class="field" style="margin-top:14px">
        <label>Adresse complète</label>
        <input type="text" name="adresse" value="{{ old('adresse', $u->adresse ?? '') }}">
      </div>

      <div class="btn-actions">
        <button type="submit" class="btn-primary">💾 Enregistrer les modifications</button>
      </div>
    </form>
  </div>

</div>{{-- /grid-top --}}


{{-- ═══════════════════════════════════════════════════════
     ROW 2 : Sécurité  |  Sécurité avancée
════════════════════════════════════════════════════════ --}}
<div class="grid-mid">

  {{-- ── Changer le mot de passe ── --}}
  <div class="card">
    <div class="card-title">🔐 Sécurité du compte</div>

    <form action="/profil/password" method="POST">
      @csrf

      <div class="field" style="margin-bottom:14px">
        <label>Mot de passe actuel</label>
        <div class="pw-wrap">
          <input type="password" name="ancien_mdp" id="pw-old" placeholder="••••••••" required>
          <button type="button" class="pw-eye" onclick="togglePw('pw-old',this)">👁</button>
        </div>
      </div>

      <div class="field" style="margin-bottom:6px">
        <label>Nouveau mot de passe</label>
        <div class="pw-wrap">
          <input type="password" name="nouveau_mdp" id="pw-new" placeholder="Min. 8 caractères" required minlength="8" oninput="updateStrength(this.value)">
          <button type="button" class="pw-eye" onclick="togglePw('pw-new',this)">👁</button>
        </div>
        <div class="pw-strength">
          <div class="pw-bar" id="b1"></div>
          <div class="pw-bar" id="b2"></div>
          <div class="pw-bar" id="b3"></div>
          <div class="pw-bar" id="b4"></div>
          <div class="pw-bar" id="b5"></div>
        </div>
        <div style="font-size:11px;color:#5a6a99;margin-top:4px" id="pw-label">Entrez un mot de passe</div>
      </div>

      <div class="field" style="margin-bottom:20px">
        <label>Confirmer le nouveau mot de passe</label>
        <div class="pw-wrap">
          <input type="password" name="nouveau_mdp_confirmation" id="pw-conf" placeholder="Répéter le mot de passe" required minlength="8">
          <button type="button" class="pw-eye" onclick="togglePw('pw-conf',this)">👁</button>
        </div>
      </div>

      <div class="section-sep"></div>

      <div style="font-size:12px;color:#5a6a99;margin-bottom:16px;line-height:1.6">
        🛡 Le mot de passe doit contenir au moins 8 caractères.<br>
        Recommandé : majuscules, chiffres, caractères spéciaux.
      </div>

      <button type="submit" class="btn-danger">🔑 Changer le mot de passe</button>
    </form>
  </div>

  {{-- ── Sécurité avancée ── --}}
  <div class="card">
    <div class="card-title">🛡 Sécurité avancée</div>

    <div class="sec-list">
      <div class="sec-row">
        <div class="sec-ico">🌐</div>
        <div class="sec-content">
          <div class="sec-key">Adresse IP</div>
          <div class="sec-val" id="user-ip">Chargement…</div>
        </div>
      </div>
      <div class="sec-row">
        <div class="sec-ico">💻</div>
        <div class="sec-content">
          <div class="sec-key">Appareil</div>
          <div class="sec-val" id="user-device">—</div>
        </div>
      </div>
      <div class="sec-row">
        <div class="sec-ico">🧭</div>
        <div class="sec-content">
          <div class="sec-key">Navigateur</div>
          <div class="sec-val" id="user-browser">—</div>
        </div>
      </div>
      <div class="sec-row">
        <div class="sec-ico">🕐</div>
        <div class="sec-content">
          <div class="sec-key">Dernière activité</div>
          <div class="sec-val" id="user-activity">—</div>
        </div>
      </div>
      <div class="sec-row">
        <div class="sec-ico">🔗</div>
        <div class="sec-content">
          <div class="sec-key">Statut connexion</div>
          <div class="sec-val online">● Session active</div>
        </div>
      </div>
      <div class="sec-row">
        <div class="sec-ico">📅</div>
        <div class="sec-content">
          <div class="sec-key">Compte créé le</div>
          <div class="sec-val">{{ $date_inscription }}</div>
        </div>
      </div>
    </div>

    <div class="stats-mini">
      <div class="stat-mini">
        <div class="stat-mini-num" id="sm-alertes">—</div>
        <div class="stat-mini-label">Alertes reçues</div>
      </div>
      <div class="stat-mini">
        <div class="stat-mini-num" id="sm-mesures">—</div>
        <div class="stat-mini-label">Mesures enreg.</div>
      </div>
    </div>
  </div>

</div>{{-- /grid-mid --}}


{{-- ═══════════════════════════════════════════════════════
     ROW 3 : Activités  |  Notifications
════════════════════════════════════════════════════════ --}}
<div class="grid-bot">

  {{-- ── Activités récentes ── --}}
  <div class="card">
    <div class="card-title">📋 Activités récentes</div>
    <div class="card-scroll">
      <div class="timeline" id="timeline">

        {{-- Connexion actuelle --}}
        <div class="tl-item">
          <div class="tl-dot g">🔑</div>
          <div class="tl-body">
            <div class="tl-msg">Connexion réussie à la plateforme</div>
            <div class="tl-time" id="tl-now">—</div>
          </div>
        </div>

        {{-- Alertes depuis la DB --}}
        @forelse($alertes as $alerte)
          @php
            $ico   = $alerte->niveau === 'critique' ? '🔴' : ($alerte->niveau === 'warning' ? '🟡' : '🟢');
            $cls   = $alerte->niveau === 'critique' ? 'r' : ($alerte->niveau === 'warning' ? 'y' : 'g');
            $date  = \Carbon\Carbon::parse($alerte->created_at)->format('d/m/Y H:i');
          @endphp
          <div class="tl-item">
            <div class="tl-dot {{ $cls }}">{{ $ico }}</div>
            <div class="tl-body">
              <div class="tl-msg">{{ $alerte->message }}</div>
              <div class="tl-time">{{ $date }}</div>
            </div>
          </div>
        @empty
          <div class="tl-item">
            <div class="tl-dot b">📊</div>
            <div class="tl-body">
              <div class="tl-msg">Aucune alerte enregistrée pour le moment</div>
              <div class="tl-time">Système en attente de données capteurs</div>
            </div>
          </div>
        @endforelse

        {{-- Inscription --}}
        <div class="tl-item">
          <div class="tl-dot b">✅</div>
          <div class="tl-body">
            <div class="tl-msg">Compte créé et validé</div>
            <div class="tl-time">{{ $date_inscription }}</div>
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- ── Préférences notifications ── --}}
  <div class="card">
    <div class="card-title">🔔 Préférences notifications</div>

    <div class="switch-list">
      <div class="switch-row">
        <div class="switch-label"><span class="switch-ico">📧</span> Alertes par email</div>
        <label class="toggle"><input type="checkbox" id="notif-email" checked onchange="saveNotif()"><span class="toggle-slider"></span></label>
      </div>
      <div class="switch-row">
        <div class="switch-label"><span class="switch-ico">🌡</span> Alertes température</div>
        <label class="toggle"><input type="checkbox" id="notif-temp" checked onchange="saveNotif()"><span class="toggle-slider"></span></label>
      </div>
      <div class="switch-row">
        <div class="switch-label"><span class="switch-ico">💨</span> Alertes gaz / qualité air</div>
        <label class="toggle"><input type="checkbox" id="notif-gaz" checked onchange="saveNotif()"><span class="toggle-slider"></span></label>
      </div>
      <div class="switch-row">
        <div class="switch-label"><span class="switch-ico">⚡</span> Alertes puissance / courant</div>
        <label class="toggle"><input type="checkbox" id="notif-power" checked onchange="saveNotif()"><span class="toggle-slider"></span></label>
      </div>
      <div class="switch-row">
        <div class="switch-label"><span class="switch-ico">🚶</span> Alertes mouvement PIR</div>
        <label class="toggle"><input type="checkbox" id="notif-pir" checked onchange="saveNotif()"><span class="toggle-slider"></span></label>
      </div>
      <div class="switch-row">
        <div class="switch-label"><span class="switch-ico">📱</span> Alertes SMS</div>
        <label class="toggle"><input type="checkbox" id="notif-sms" onchange="saveNotif()"><span class="toggle-slider"></span></label>
      </div>
      <div class="switch-row">
        <div class="switch-label"><span class="switch-ico">🔴</span> Alertes anomalies critiques</div>
        <label class="toggle"><input type="checkbox" id="notif-anomalie" checked onchange="saveNotif()"><span class="toggle-slider"></span></label>
      </div>
    </div>

    <div style="margin-top:16px;font-size:11px;color:#3a4a6a;line-height:1.5">
      Les préférences sont sauvegardées localement sur cet appareil.
    </div>
  </div>

</div>{{-- /grid-bot --}}


<script>
// ── Preview photo avant upload ──────────────────────────
function previewPhoto(input) {
  if (!input.files || !input.files[0]) return;
  const r = new FileReader();
  r.onload = e => {
    const prev = document.getElementById('avatar-preview');
    const wrap = document.getElementById('avatar-preview-wrap');
    if (prev && prev.tagName === 'IMG') {
      prev.src = e.target.result;
    } else {
      const ring = document.querySelector('.avatar-ring');
      ring.innerHTML = `<img class="avatar-img" src="${e.target.result}" id="avatar-preview">`;
    }
    if (wrap) wrap.style.display = 'none';
  };
  r.readAsDataURL(input.files[0]);
}

// ── Toggle visibility mot de passe ─────────────────────
function togglePw(id, btn) {
  const el = document.getElementById(id);
  if (el.type === 'password') { el.type = 'text'; btn.textContent = '🙈'; }
  else                        { el.type = 'password'; btn.textContent = '👁'; }
}

// ── Indicateur force mot de passe ──────────────────────
function updateStrength(v) {
  let s = 0;
  if (v.length >= 8)  s++;
  if (v.length >= 12) s++;
  if (/[A-Z]/.test(v))  s++;
  if (/[0-9]/.test(v))  s++;
  if (/[^A-Za-z0-9]/.test(v)) s++;

  const colors  = ['#ff4444','#ff7733','#ffd633','#33b5ff','#33ff88'];
  const labels  = ['Très faible','Faible','Moyen','Fort','Très fort'];
  for (let i = 1; i <= 5; i++) {
    document.getElementById('b' + i).style.background = i <= s ? colors[s - 1] : '#1e2f5a';
  }
  document.getElementById('pw-label').textContent = v.length ? labels[s - 1] : 'Entrez un mot de passe';
  document.getElementById('pw-label').style.color  = v.length ? colors[s - 1] : '#5a6a99';
}

// ── Notifications localStorage ──────────────────────────
const NOTIF_KEY = 'supserver_notifs';
function saveNotif() {
  const ids = ['notif-email','notif-temp','notif-gaz','notif-power','notif-pir','notif-sms','notif-anomalie'];
  const prefs = {};
  ids.forEach(id => prefs[id] = document.getElementById(id).checked);
  localStorage.setItem(NOTIF_KEY, JSON.stringify(prefs));
}
function loadNotif() {
  const raw = localStorage.getItem(NOTIF_KEY);
  if (!raw) return;
  try {
    const prefs = JSON.parse(raw);
    Object.entries(prefs).forEach(([id, val]) => {
      const el = document.getElementById(id);
      if (el) el.checked = val;
    });
  } catch(e) {}
}
loadNotif();

// ── Infos sécurité avancée ─────────────────────────────
(function() {
  // Device
  const ua = navigator.userAgent;
  let device = 'Ordinateur';
  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua)) device = 'Mobile';
  else if (/Tablet|iPad/i.test(ua)) device = 'Tablette';
  document.getElementById('user-device').textContent = device;

  // Browser
  let browser = 'Inconnu';
  if (ua.includes('Chrome') && !ua.includes('Edg'))  browser = 'Google Chrome';
  else if (ua.includes('Firefox'))                    browser = 'Mozilla Firefox';
  else if (ua.includes('Safari') && !ua.includes('Chrome')) browser = 'Apple Safari';
  else if (ua.includes('Edg'))                        browser = 'Microsoft Edge';
  else if (ua.includes('Opera') || ua.includes('OPR')) browser = 'Opera';
  document.getElementById('user-browser').textContent = browser;

  // Last activity
  const now = new Date();
  document.getElementById('user-activity').textContent = now.toLocaleString('fr-FR');
  document.getElementById('tl-now').textContent = now.toLocaleString('fr-FR');

  // IP via API publique
  fetch('https://api.ipify.org?format=json')
    .then(r => r.json())
    .then(d => { if(d.ip) document.getElementById('user-ip').textContent = d.ip; })
    .catch(() => { document.getElementById('user-ip').textContent = 'Non disponible'; });
})();

// ── Mini stats depuis /api/stats ────────────────────────
fetch('/api/stats')
  .then(r => r.json())
  .then(s => {
    document.getElementById('sm-alertes').textContent = (s.alertesCritiques || 0) + (s.alertesWarning || 0);
    document.getElementById('sm-mesures').textContent = s.totalMesures || 0;
  })
  .catch(() => {});
</script>

@endsection
