@extends('layouts.app')

@section('content')

@php
$u = is_array($user) ? (object)$user : $user;
$nom_complet = trim(($u->prenom ?? '') . ' ' . ($u->nom ?? '')) ?: ($u->name ?? 'Utilisateur');
$role_label  = match($u->role ?? '') {
    'administrateur' => 'Administrateur',
    'utilisateur'    => 'Utilisateur',
    default          => ucfirst($u->role ?? 'Utilisateur'),
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
*{box-sizing:border-box}
body{background:#060d1f;color:#e0e8ff;font-family:'Segoe UI',Arial,sans-serif}

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

.flash{
  padding:12px 18px;border-radius:10px;margin-bottom:20px;
  font-size:14px;font-weight:600;display:flex;align-items:center;gap:10px;
  animation:fadeUp .4s ease;
}
.flash-success{background:rgba(51,255,136,.1);border:1px solid rgba(51,255,136,.3);color:#33ff88}
.flash-error  {background:rgba(255,87,51,.1);border:1px solid rgba(255,87,51,.3);color:#ff5733}
@keyframes fadeUp{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}

.grid-top{display:grid;grid-template-columns:300px 1fr;gap:20px;margin-bottom:20px}
.grid-mid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px}
.grid-bot{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px}
@media(max-width:1024px){.grid-top{grid-template-columns:1fr}}
@media(max-width:768px){.grid-mid,.grid-bot{grid-template-columns:1fr}}

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

.btn-photo{
  display:inline-flex;align-items:center;gap:7px;
  padding:8px 18px;border-radius:8px;
  border:1px solid #1e2f5a;background:rgba(255,255,255,.04);
  color:#8899cc;font-size:12px;font-weight:600;
  cursor:pointer;transition:.25s;margin-top:12px;
}
.btn-photo:hover{border-color:#33ff88;color:#33ff88;background:rgba(51,255,136,.05)}
.btn-photo input{display:none}

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

.pw-wrap{position:relative}
.pw-wrap input{width:100%;padding-right:44px}
.pw-eye{
  position:absolute;right:12px;top:50%;transform:translateY(-50%);
  background:none;border:none;color:#5a6a99;cursor:pointer;
  font-size:16px;transition:.2s;padding:0;line-height:1;
}
.pw-eye:hover{color:#33ff88}

.pw-strength{margin-top:6px;display:flex;gap:4px}
.pw-bar{height:3px;border-radius:2px;flex:1;background:#1e2f5a;transition:.3s}

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

.section-sep{
  height:1px;margin:24px 0;
  background:linear-gradient(90deg,transparent,#1e2f5a,transparent);
}

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

.switch-list{display:flex;flex-direction:column;gap:10px}
.switch-row{
  display:flex;align-items:center;justify-content:space-between;
  padding:11px 14px;background:rgba(255,255,255,.03);
  border-radius:10px;border:1px solid transparent;transition:.25s;
}
.switch-row:hover{border-color:#1e2f5a;background:rgba(255,255,255,.05)}
.switch-label{font-size:13px;color:#c7d2ff;display:flex;align-items:center;gap:9px}
.switch-ico{font-size:15px;width:20px;text-align:center}
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

.stats-mini{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:16px}
.stat-mini{
  background:rgba(255,255,255,.03);border-radius:10px;
  padding:14px;text-align:center;border:1px solid transparent;transition:.25s;
}
.stat-mini:hover{border-color:#1e2f5a}
.stat-mini-num{font-size:24px;font-weight:700;color:#33ff88;line-height:1}
.stat-mini-label{font-size:11px;color:#5a6a99;margin-top:5px;letter-spacing:.5px;text-transform:uppercase}

.card-scroll{max-height:380px;overflow-y:auto;padding-right:4px}
.card-scroll::-webkit-scrollbar{width:3px}
.card-scroll::-webkit-scrollbar-track{background:#0a1225}
.card-scroll::-webkit-scrollbar-thumb{background:#33ff88;border-radius:2px}

.geo-sel{width:100%;background:#07102a;border:1px solid #1e2f5a;border-radius:8px;padding:10px 12px;color:#fff;font-size:13px;outline:none;transition:.2s;cursor:pointer;appearance:none;-webkit-appearance:none}
.geo-sel:focus{border-color:#33ff88;box-shadow:0 0 0 2px rgba(51,255,136,.1)}
.geo-sel:disabled{opacity:.45;cursor:not-allowed}
.geo-sel option{background:#0e1a38}
.geo-ld{font-size:11px;color:#33b5ff;margin-top:4px;display:none}
.geo-ld.show{display:block}
</style>


<div class="profil-header">
  <h1><i class="fa-solid fa-user"></i> Mon <span>Profil</span></h1>
  <div class="breadcrumb"><a href="/dashboard">Dashboard</a> / Mon Profil</div>
</div>

@if(session('success_profil'))
  <div class="flash flash-success"><i class="fa-solid fa-circle-check" style="color:#33ff88"></i> {{ session('success_profil') }}</div>
@endif
@if(session('success_password'))
  <div class="flash flash-success"><i class="fa-solid fa-lock"></i> {{ session('success_password') }}</div>
@endif
@if(session('success_photo'))
  <div class="flash flash-success"><i class="fa-solid fa-camera"></i> {{ session('success_photo') }}</div>
@endif
@if(session('error_password'))
  <div class="flash flash-error"><i class="fa-solid fa-circle-xmark" style="color:#ff5733"></i> {{ session('error_password') }}</div>
@endif
@if($errors->any())
  @foreach($errors->all() as $err)
    <div class="flash flash-error"><i class="fa-solid fa-triangle-exclamation" style="color:#ffd633"></i> {{ $err }}</div>
  @endforeach
@endif


<div class="grid-top">

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

    <form action="/profil/photo" method="POST" enctype="multipart/form-data" id="form-photo">
      @csrf
      <label class="btn-photo">
        <i class="fa-solid fa-camera"></i> Modifier la photo
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
          <label><i class="fa-solid fa-earth-africa"></i> Pays</label>
          <input type="hidden" name="pays"     id="p_pays_val"    value="{{ old('pays',    $u->pays    ?? '') }}">
          <input type="hidden" name="iso_pays" id="p_iso_val"     value="{{ old('iso_pays',$u->iso_pays?? '') }}">
          <select class="geo-sel" id="p_pays_sel">
            <option value="">— Sélectionner un pays —</option>
          </select>
          <div class="geo-ld" id="p_pays_ld">Chargement…</div>
        </div>
        <div class="field">
          <label><i class="fa-solid fa-globe"></i> Région / Province / État</label>
          <select class="geo-sel" name="region" id="p_region_sel" disabled>
            <option value="">— Choisir un pays d'abord —</option>
          </select>
          <div class="geo-ld" id="p_reg_ld">Chargement…</div>
        </div>
        <div class="field">
          <label><i class="fa-solid fa-building-columns"></i> Département / District</label>
          <select class="geo-sel" name="departement" id="p_dept_sel" disabled>
            <option value="">— Choisir une région d'abord —</option>
          </select>
          <div class="geo-ld" id="p_dept_ld">Chargement…</div>
        </div>
        <div class="field">
          <label><i class="fa-solid fa-house-chimney"></i> Arrondissement / Commune</label>
          <input type="hidden" name="arrondissement" id="p_h_arrond" value="{{ old('arrondissement', $u->arrondissement ?? '') }}">
          <select class="geo-sel" id="p_arrond_sel" style="display:none">
            <option value="">— Sélectionner —</option>
          </select>
          <input class="geo-sel" id="p_arrond_txt" type="text" name="_arrond_txt" placeholder="Ex: Yaoundé 1er, Lyon 3e…" value="{{ old('arrondissement', $u->arrondissement ?? '') }}" style="display:block">
          <div class="geo-ld" id="p_arrond_ld">Chargement…</div>
        </div>
        <div class="field">
          <label><i class="fa-solid fa-city"></i> Ville / Résidence</label>
          <select class="geo-sel" name="ville_residence" id="p_ville_sel" disabled>
            <option value="">— Choisir un département d'abord —</option>
          </select>
          <div class="geo-ld" id="p_ville_ld">Chargement…</div>
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
        <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications</button>
      </div>
    </form>
  </div>

</div>


<div class="grid-mid">

  <div class="card">
    <div class="card-title"><i class="fa-solid fa-lock"></i> Sécurité du compte</div>

    <form action="/profil/password" method="POST">
      @csrf

      <div class="field" style="margin-bottom:14px">
        <label>Mot de passe actuel</label>
        <div class="pw-wrap">
          <input type="password" name="ancien_mdp" id="pw-old" placeholder="••••••••" required>
          <button type="button" class="pw-eye" onclick="togglePw('pw-old',this)"><i class="fa-solid fa-eye"></i></button>
        </div>
      </div>

      <div class="field" style="margin-bottom:6px">
        <label>Nouveau mot de passe</label>
        <div class="pw-wrap">
          <input type="password" name="nouveau_mdp" id="pw-new" placeholder="Min. 8 caractères" required minlength="8" oninput="updateStrength(this.value)">
          <button type="button" class="pw-eye" onclick="togglePw('pw-new',this)"><i class="fa-solid fa-eye"></i></button>
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
          <button type="button" class="pw-eye" onclick="togglePw('pw-conf',this)"><i class="fa-solid fa-eye"></i></button>
        </div>
      </div>

      <div class="section-sep"></div>

      <div style="font-size:12px;color:#5a6a99;margin-bottom:16px;line-height:1.6">
        <i class="fa-solid fa-shield-halved"></i> Le mot de passe doit contenir au moins 8 caractères.<br>
        Recommandé : majuscules, chiffres, caractères spéciaux.
      </div>

      <button type="submit" class="btn-danger"><i class="fa-solid fa-key"></i> Changer le mot de passe</button>
    </form>
  </div>

  <div class="card">
    <div class="card-title"><i class="fa-solid fa-shield-halved"></i> Sécurité avancée</div>

    <div class="sec-list">
      <div class="sec-row">
        <div class="sec-ico"><i class="fa-solid fa-globe"></i></div>
        <div class="sec-content">
          <div class="sec-key">Adresse IP</div>
          <div class="sec-val" id="user-ip">Chargement…</div>
        </div>
      </div>
      <div class="sec-row">
        <div class="sec-ico"><i class="fa-solid fa-laptop"></i></div>
        <div class="sec-content">
          <div class="sec-key">Appareil</div>
          <div class="sec-val" id="user-device">—</div>
        </div>
      </div>
      <div class="sec-row">
        <div class="sec-ico"><i class="fa-solid fa-compass"></i></div>
        <div class="sec-content">
          <div class="sec-key">Navigateur</div>
          <div class="sec-val" id="user-browser">—</div>
        </div>
      </div>
      <div class="sec-row">
        <div class="sec-ico"><i class="fa-solid fa-clock"></i></div>
        <div class="sec-content">
          <div class="sec-key">Dernière activité</div>
          <div class="sec-val" id="user-activity">—</div>
        </div>
      </div>
      <div class="sec-row">
        <div class="sec-ico"><i class="fa-solid fa-link"></i></div>
        <div class="sec-content">
          <div class="sec-key">Statut authentification</div>
          <div class="sec-val online">● Session active</div>
        </div>
      </div>
      <div class="sec-row">
        <div class="sec-ico"><i class="fa-solid fa-calendar-days"></i></div>
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

</div>


<div class="grid-bot">

  <div class="card">
    <div class="card-title"><i class="fa-solid fa-clipboard-list"></i> Activités récentes</div>
    <div class="card-scroll">
      <div class="timeline" id="timeline">

        <div class="tl-item">
          <div class="tl-dot g"><i class="fa-solid fa-key"></i></div>
          <div class="tl-body">
            <div class="tl-msg">Authentification réussie à la plateforme</div>
            <div class="tl-time" id="tl-now">—</div>
          </div>
        </div>

        @forelse($alertes as $alerte)
          @php
            $ico   = $alerte->niveau === 'critique' ? '<i class="fa-solid fa-circle" style="color:#ff5733"></i>' : ($alerte->niveau === 'warning' ? '<i class="fa-solid fa-circle" style="color:#ffd633"></i>' : '<i class="fa-solid fa-circle" style="color:#33ff88"></i>');
            $cls   = $alerte->niveau === 'critique' ? 'r' : ($alerte->niveau === 'warning' ? 'y' : 'g');
            $date  = \Carbon\Carbon::parse($alerte->created_at)->format('d/m/Y H:i');
          @endphp
          <div class="tl-item">
            <div class="tl-dot {{ $cls }}">{!! $ico !!}</div>
            <div class="tl-body">
              <div class="tl-msg">{{ $alerte->message }}</div>
              <div class="tl-time">{{ $date }}</div>
            </div>
          </div>
        @empty
          <div class="tl-item">
            <div class="tl-dot b"><i class="fa-solid fa-chart-bar"></i></div>
            <div class="tl-body">
              <div class="tl-msg">Aucune alerte enregistrée pour le moment</div>
              <div class="tl-time">Système en attente de données capteurs</div>
            </div>
          </div>
        @endforelse

        <div class="tl-item">
          <div class="tl-dot b"><i class="fa-solid fa-circle-check"></i></div>
          <div class="tl-body">
            <div class="tl-msg">Compte créé et validé</div>
            <div class="tl-time">{{ $date_inscription }}</div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-title"><i class="fa-solid fa-bell"></i> Préférences notifications</div>

    <div class="switch-list">
      <div class="switch-row">
        <div class="switch-label"><span class="switch-ico"><i class="fa-solid fa-envelope"></i></span> Alertes par email</div>
        <label class="toggle"><input type="checkbox" id="notif-email" checked onchange="saveNotif()"><span class="toggle-slider"></span></label>
      </div>
      <div class="switch-row">
        <div class="switch-label"><span class="switch-ico"><i class="fa-solid fa-temperature-half"></i></span> Alertes température</div>
        <label class="toggle"><input type="checkbox" id="notif-temp" checked onchange="saveNotif()"><span class="toggle-slider"></span></label>
      </div>
      <div class="switch-row">
        <div class="switch-label"><span class="switch-ico"><i class="fa-solid fa-wind"></i></span> Alertes gaz / qualité air</div>
        <label class="toggle"><input type="checkbox" id="notif-gaz" checked onchange="saveNotif()"><span class="toggle-slider"></span></label>
      </div>
      <div class="switch-row">
        <div class="switch-label"><span class="switch-ico"><i class="fa-solid fa-person-walking"></i></span> Alertes mouvement PIR</div>
        <label class="toggle"><input type="checkbox" id="notif-pir" checked onchange="saveNotif()"><span class="toggle-slider"></span></label>
      </div>
      <div class="switch-row">
        <div class="switch-label"><span class="switch-ico"><i class="fa-solid fa-mobile-screen-button"></i></span> Alertes SMS</div>
        <label class="toggle"><input type="checkbox" id="notif-sms" onchange="saveNotif()"><span class="toggle-slider"></span></label>
      </div>
      <div class="switch-row">
        <div class="switch-label"><span class="switch-ico"><i class="fa-solid fa-circle" style="color:#ff5733"></i></span> Alertes anomalies critiques</div>
        <label class="toggle"><input type="checkbox" id="notif-anomalie" checked onchange="saveNotif()"><span class="toggle-slider"></span></label>
      </div>
    </div>

    <div style="margin-top:16px;font-size:11px;color:#3a4a6a;line-height:1.5">
      Les préférences sont sauvegardées localement sur cet appareil.
    </div>
  </div>

</div>


<script>
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

function togglePw(id, btn) {
  const el = document.getElementById(id);
  if (el.type === 'password') { el.type = 'text'; btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>'; }
  else                        { el.type = 'password'; btn.innerHTML = '<i class="fa-solid fa-eye"></i>'; }
}

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

const NOTIF_KEY = 'plateforme_notifs';
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

(function() {
  const ua = navigator.userAgent;
  let device = 'Ordinateur';
  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua)) device = 'Mobile';
  else if (/Tablet|iPad/i.test(ua)) device = 'Tablette';
  document.getElementById('user-device').textContent = device;

  let browser = 'Inconnu';
  if (ua.includes('Chrome') && !ua.includes('Edg'))  browser = 'Google Chrome';
  else if (ua.includes('Firefox'))                    browser = 'Mozilla Firefox';
  else if (ua.includes('Safari') && !ua.includes('Chrome')) browser = 'Apple Safari';
  else if (ua.includes('Edg'))                        browser = 'Microsoft Edge';
  else if (ua.includes('Opera') || ua.includes('OPR')) browser = 'Opera';
  document.getElementById('user-browser').textContent = browser;

  const now = new Date();
  document.getElementById('user-activity').textContent = now.toLocaleString('fr-FR');
  document.getElementById('tl-now').textContent = now.toLocaleString('fr-FR');

  fetch('https://api.ipify.org?format=json')
    .then(r => r.json())
    .then(d => { if(d.ip) document.getElementById('user-ip').textContent = d.ip; })
    .catch(() => { document.getElementById('user-ip').textContent = 'Non disponible'; });
})();

fetch('/api/stats')
  .then(r => r.json())
  .then(s => {
    document.getElementById('sm-alertes').textContent = (s.alertesCritiques || 0) + (s.alertesWarning || 0);
    document.getElementById('sm-mesures').textContent = s.totalMesures || 0;
  })
  .catch(() => {});

(function(){
const PRE_PAYS   = @json($u->pays    ?? '');
const PRE_ISO    = @json($u->iso_pays?? '');
const PRE_REGION = @json($u->region  ?? '');
const PRE_DEPT   = @json($u->departement ?? '');
const PRE_ARROND = @json($u->arrondissement ?? '');
const PRE_VILLE  = @json($u->ville_residence ?? '');

const P_CTRS = [
  {iso:'AF',fr:'Afghanistan',en:'Afghanistan'},{iso:'ZA',fr:'Afrique du Sud',en:'South Africa'},{iso:'AL',fr:'Albanie',en:'Albania'},
  {iso:'DZ',fr:'Algérie',en:'Algeria'},{iso:'DE',fr:'Allemagne',en:'Germany'},{iso:'AD',fr:'Andorre',en:'Andorra'},
  {iso:'AO',fr:'Angola',en:'Angola'},{iso:'AG',fr:'Antigua-et-Barbuda',en:'Antigua and Barbuda'},{iso:'SA',fr:'Arabie saoudite',en:'Saudi Arabia'},
  {iso:'AR',fr:'Argentine',en:'Argentina'},{iso:'AM',fr:'Arménie',en:'Armenia'},{iso:'AU',fr:'Australie',en:'Australia'},
  {iso:'AT',fr:'Autriche',en:'Austria'},{iso:'AZ',fr:'Azerbaïdjan',en:'Azerbaijan'},{iso:'BS',fr:'Bahamas',en:'Bahamas'},
  {iso:'BH',fr:'Bahreïn',en:'Bahrain'},{iso:'BD',fr:'Bangladesh',en:'Bangladesh'},{iso:'BB',fr:'Barbade',en:'Barbados'},
  {iso:'BE',fr:'Belgique',en:'Belgium'},{iso:'BZ',fr:'Belize',en:'Belize'},{iso:'BJ',fr:'Bénin',en:'Benin'},
  {iso:'BT',fr:'Bhoutan',en:'Bhutan'},{iso:'BY',fr:'Biélorussie',en:'Belarus'},{iso:'BO',fr:'Bolivie',en:'Bolivia'},
  {iso:'BA',fr:'Bosnie-Herzégovine',en:'Bosnia and Herzegovina'},{iso:'BW',fr:'Botswana',en:'Botswana'},{iso:'BR',fr:'Brésil',en:'Brazil'},
  {iso:'BN',fr:'Brunéi',en:'Brunei'},{iso:'BG',fr:'Bulgarie',en:'Bulgaria'},{iso:'BF',fr:'Burkina Faso',en:'Burkina Faso'},
  {iso:'BI',fr:'Burundi',en:'Burundi'},{iso:'CV',fr:'Cap-Vert',en:'Cape Verde'},{iso:'KH',fr:'Cambodge',en:'Cambodia'},
  {iso:'CM',fr:'Cameroun',en:'Cameroon'},{iso:'CA',fr:'Canada',en:'Canada'},{iso:'CF',fr:'Centrafrique',en:'Central African Republic'},
  {iso:'CL',fr:'Chili',en:'Chile'},{iso:'CN',fr:'Chine',en:'China'},{iso:'CY',fr:'Chypre',en:'Cyprus'},
  {iso:'CO',fr:'Colombie',en:'Colombia'},{iso:'KM',fr:'Comores',en:'Comoros'},{iso:'CG',fr:'Congo',en:'Congo'},
  {iso:'CD',fr:'Congo (RDC)',en:'DR Congo'},{iso:'KP',fr:'Corée du Nord',en:'North Korea'},{iso:'KR',fr:'Corée du Sud',en:'South Korea'},
  {iso:'CR',fr:'Costa Rica',en:'Costa Rica'},{iso:'CI',fr:"Côte d'Ivoire",en:"Ivory Coast"},{iso:'HR',fr:'Croatie',en:'Croatia'},
  {iso:'CU',fr:'Cuba',en:'Cuba'},{iso:'DK',fr:'Danemark',en:'Denmark'},{iso:'DJ',fr:'Djibouti',en:'Djibouti'},
  {iso:'DM',fr:'Dominique',en:'Dominica'},{iso:'EG',fr:'Égypte',en:'Egypt'},{iso:'AE',fr:'Émirats arabes unis',en:'United Arab Emirates'},
  {iso:'EC',fr:'Équateur',en:'Ecuador'},{iso:'ER',fr:'Érythrée',en:'Eritrea'},{iso:'ES',fr:'Espagne',en:'Spain'},
  {iso:'EE',fr:'Estonie',en:'Estonia'},{iso:'SZ',fr:'Eswatini',en:'Eswatini'},{iso:'ET',fr:'Éthiopie',en:'Ethiopia'},
  {iso:'FJ',fr:'Fidji',en:'Fiji'},{iso:'FI',fr:'Finlande',en:'Finland'},{iso:'FR',fr:'France',en:'France'},
  {iso:'GA',fr:'Gabon',en:'Gabon'},{iso:'GM',fr:'Gambie',en:'Gambia'},{iso:'GE',fr:'Géorgie',en:'Georgia'},
  {iso:'GH',fr:'Ghana',en:'Ghana'},{iso:'GR',fr:'Grèce',en:'Greece'},{iso:'GD',fr:'Grenade',en:'Grenada'},
  {iso:'GT',fr:'Guatemala',en:'Guatemala'},{iso:'GN',fr:'Guinée',en:'Guinea'},{iso:'GW',fr:'Guinée-Bissau',en:'Guinea-Bissau'},
  {iso:'GQ',fr:'Guinée équatoriale',en:'Equatorial Guinea'},{iso:'GY',fr:'Guyana',en:'Guyana'},{iso:'HT',fr:'Haïti',en:'Haiti'},
  {iso:'HN',fr:'Honduras',en:'Honduras'},{iso:'HU',fr:'Hongrie',en:'Hungary'},{iso:'IN',fr:'Inde',en:'India'},
  {iso:'ID',fr:'Indonésie',en:'Indonesia'},{iso:'IQ',fr:'Irak',en:'Iraq'},{iso:'IR',fr:'Iran',en:'Iran'},
  {iso:'IE',fr:'Irlande',en:'Ireland'},{iso:'IS',fr:'Islande',en:'Iceland'},{iso:'IL',fr:'Israël',en:'Israel'},
  {iso:'IT',fr:'Italie',en:'Italy'},{iso:'JM',fr:'Jamaïque',en:'Jamaica'},{iso:'JP',fr:'Japon',en:'Japan'},
  {iso:'JO',fr:'Jordanie',en:'Jordan'},{iso:'KZ',fr:'Kazakhstan',en:'Kazakhstan'},{iso:'KE',fr:'Kenya',en:'Kenya'},
  {iso:'KG',fr:'Kirghizistan',en:'Kyrgyzstan'},{iso:'KI',fr:'Kiribati',en:'Kiribati'},{iso:'KW',fr:'Koweït',en:'Kuwait'},
  {iso:'LA',fr:'Laos',en:'Laos'},{iso:'LS',fr:'Lesotho',en:'Lesotho'},{iso:'LV',fr:'Lettonie',en:'Latvia'},
  {iso:'LB',fr:'Liban',en:'Lebanon'},{iso:'LR',fr:'Libéria',en:'Liberia'},{iso:'LY',fr:'Libye',en:'Libya'},
  {iso:'LI',fr:'Liechtenstein',en:'Liechtenstein'},{iso:'LT',fr:'Lituanie',en:'Lithuania'},{iso:'LU',fr:'Luxembourg',en:'Luxembourg'},
  {iso:'MG',fr:'Madagascar',en:'Madagascar'},{iso:'MY',fr:'Malaisie',en:'Malaysia'},{iso:'MW',fr:'Malawi',en:'Malawi'},
  {iso:'MV',fr:'Maldives',en:'Maldives'},{iso:'ML',fr:'Mali',en:'Mali'},{iso:'MT',fr:'Malte',en:'Malta'},
  {iso:'MA',fr:'Maroc',en:'Morocco'},{iso:'MH',fr:'Marshall',en:'Marshall Islands'},{iso:'MU',fr:'Maurice',en:'Mauritius'},
  {iso:'MR',fr:'Mauritanie',en:'Mauritania'},{iso:'MX',fr:'Mexique',en:'Mexico'},{iso:'FM',fr:'Micronésie',en:'Micronesia'},
  {iso:'MD',fr:'Moldavie',en:'Moldova'},{iso:'MC',fr:'Monaco',en:'Monaco'},{iso:'MN',fr:'Mongolie',en:'Mongolia'},
  {iso:'ME',fr:'Monténégro',en:'Montenegro'},{iso:'MZ',fr:'Mozambique',en:'Mozambique'},{iso:'MM',fr:'Myanmar',en:'Myanmar'},
  {iso:'NA',fr:'Namibie',en:'Namibia'},{iso:'NR',fr:'Nauru',en:'Nauru'},{iso:'NP',fr:'Népal',en:'Nepal'},
  {iso:'NI',fr:'Nicaragua',en:'Nicaragua'},{iso:'NE',fr:'Niger',en:'Niger'},{iso:'NG',fr:'Nigeria',en:'Nigeria'},
  {iso:'NO',fr:'Norvège',en:'Norway'},{iso:'NZ',fr:'Nouvelle-Zélande',en:'New Zealand'},{iso:'OM',fr:'Oman',en:'Oman'},
  {iso:'UG',fr:'Ouganda',en:'Uganda'},{iso:'UZ',fr:'Ouzbékistan',en:'Uzbekistan'},{iso:'PK',fr:'Pakistan',en:'Pakistan'},
  {iso:'PW',fr:'Palaos',en:'Palau'},{iso:'PA',fr:'Panama',en:'Panama'},{iso:'PG',fr:'Papouasie',en:'Papua New Guinea'},
  {iso:'PY',fr:'Paraguay',en:'Paraguay'},{iso:'NL',fr:'Pays-Bas',en:'Netherlands'},{iso:'PE',fr:'Pérou',en:'Peru'},
  {iso:'PH',fr:'Philippines',en:'Philippines'},{iso:'PL',fr:'Pologne',en:'Poland'},{iso:'PT',fr:'Portugal',en:'Portugal'},
  {iso:'QA',fr:'Qatar',en:'Qatar'},{iso:'RO',fr:'Roumanie',en:'Romania'},{iso:'GB',fr:'Royaume-Uni',en:'United Kingdom'},
  {iso:'RU',fr:'Russie',en:'Russia'},{iso:'RW',fr:'Rwanda',en:'Rwanda'},{iso:'KN',fr:'Saint-Kitts',en:'Saint Kitts and Nevis'},
  {iso:'LC',fr:'Saint-Lucie',en:'Saint Lucia'},{iso:'VC',fr:'Saint-Vincent',en:'Saint Vincent and the Grenadines'},
  {iso:'SB',fr:'Salomon',en:'Solomon Islands'},{iso:'SV',fr:'Salvador',en:'El Salvador'},{iso:'WS',fr:'Samoa',en:'Samoa'},
  {iso:'ST',fr:'Sao Tomé-et-Principe',en:'Sao Tome and Principe'},{iso:'SN',fr:'Sénégal',en:'Senegal'},{iso:'RS',fr:'Serbie',en:'Serbia'},
  {iso:'SC',fr:'Seychelles',en:'Seychelles'},{iso:'SL',fr:'Sierra Leone',en:'Sierra Leone'},{iso:'SG',fr:'Singapour',en:'Singapore'},
  {iso:'SK',fr:'Slovaquie',en:'Slovakia'},{iso:'SI',fr:'Slovénie',en:'Slovenia'},{iso:'SO',fr:'Somalie',en:'Somalia'},
  {iso:'SD',fr:'Soudan',en:'Sudan'},{iso:'SS',fr:'Soudan du Sud',en:'South Sudan'},{iso:'LK',fr:'Sri Lanka',en:'Sri Lanka'},
  {iso:'SE',fr:'Suède',en:'Sweden'},{iso:'CH',fr:'Suisse',en:'Switzerland'},{iso:'SR',fr:'Suriname',en:'Suriname'},
  {iso:'SY',fr:'Syrie',en:'Syria'},{iso:'TJ',fr:'Tadjikistan',en:'Tajikistan'},{iso:'TZ',fr:'Tanzanie',en:'Tanzania'},
  {iso:'TD',fr:'Tchad',en:'Chad'},{iso:'CZ',fr:'Tchéquie',en:'Czech Republic'},{iso:'TH',fr:'Thaïlande',en:'Thailand'},
  {iso:'TL',fr:'Timor oriental',en:'Timor-Leste'},{iso:'TG',fr:'Togo',en:'Togo'},{iso:'TO',fr:'Tonga',en:'Tonga'},
  {iso:'TT',fr:'Trinité-et-Tobago',en:'Trinidad and Tobago'},{iso:'TN',fr:'Tunisie',en:'Tunisia'},{iso:'TM',fr:'Turkménistan',en:'Turkmenistan'},
  {iso:'TR',fr:'Turquie',en:'Turkey'},{iso:'TV',fr:'Tuvalu',en:'Tuvalu'},{iso:'UA',fr:'Ukraine',en:'Ukraine'},
  {iso:'UY',fr:'Uruguay',en:'Uruguay'},{iso:'VU',fr:'Vanuatu',en:'Vanuatu'},{iso:'VE',fr:'Venezuela',en:'Venezuela'},
  {iso:'VN',fr:'Vietnam',en:'Vietnam'},{iso:'YE',fr:'Yémen',en:'Yemen'},{iso:'ZM',fr:'Zambie',en:'Zambia'},
  {iso:'ZW',fr:'Zimbabwe',en:'Zimbabwe'},{iso:'US',fr:'États-Unis',en:'United States'}
];

function pFlag(iso){ try{return String.fromCodePoint(...[...iso.toUpperCase()].map(c=>0x1F1E6-65+c.charCodeAt(0)))}catch(e){return''} }
function pXss(s){const d=document.createElement('div');d.textContent=s;return d.innerHTML}
function pLd(id,show){const el=document.getElementById(id);if(el){el.className='geo-ld'+(show?' show':'')}}
function pReset(sel,disabled,placeholder){
  sel.innerHTML='<option value="">'+placeholder+'</option>';
  sel.disabled=disabled;
}

function pLoadRegions(countryEn, preSelect){
  const sel=document.getElementById('p_region_sel');
  pLd('p_reg_ld',true);
  pReset(sel,true,'Chargement…');
  fetch('/api/geo/states/'+encodeURIComponent(countryEn))
    .then(r=>r.json())
    .then(list=>{
      pLd('p_reg_ld',false);
      pReset(sel,false,'— Région / Province —');
      list.forEach(name=>{
        const o=document.createElement('option');
        o.value=name; o.textContent=name;
        if(name===preSelect) o.selected=true;
        sel.appendChild(o);
      });
      sel.disabled=list.length===0;
      if(preSelect && list.includes(preSelect)){
        pLoadDepts(countryEn, preSelect, PRE_DEPT);
      }
    })
    .catch(()=>{pLd('p_reg_ld',false);pReset(sel,false,'— Région non disponible —')});
}

function pLoadDepts(countryEn, region, preSelect){
  const sel=document.getElementById('p_dept_sel');
  pLd('p_dept_ld',true);
  pReset(sel,true,'Chargement…');
  fetch('/api/geo/state-cities/'+encodeURIComponent(countryEn)+'/'+encodeURIComponent(region))
    .then(r=>r.json())
    .then(list=>{
      pLd('p_dept_ld',false);
      pReset(sel,false,'— Département / District —');
      list.forEach(name=>{
        const o=document.createElement('option');
        o.value=name; o.textContent=name;
        if(name===preSelect) o.selected=true;
        sel.appendChild(o);
      });
      sel.disabled=list.length===0;
      if(preSelect && list.includes(preSelect)){
        pLoadArronds(countryEn, preSelect, PRE_ARROND);
        pLoadVilles(countryEn, preSelect, PRE_VILLE);
      }
    })
    .catch(()=>{pLd('p_dept_ld',false);pReset(sel,false,'— Dept non disponible —')});
}

function pLoadArronds(countryEn, dept, preSelect){
  const hid=document.getElementById('p_h_arrond');
  const sel=document.getElementById('p_arrond_sel');
  const txt=document.getElementById('p_arrond_txt');
  pLd('p_arrond_ld',true);
  fetch('/api/geo/subcities/'+encodeURIComponent(countryEn)+'/'+encodeURIComponent(dept))
    .then(r=>r.json())
    .then(list=>{
      pLd('p_arrond_ld',false);
      if(list.length>0){
        sel.innerHTML='<option value="">— Arrondissement —</option>';
        list.forEach(name=>{
          const o=document.createElement('option');
          o.value=name; o.textContent=name;
          if(name===preSelect) o.selected=true;
          sel.appendChild(o);
        });
        sel.style.display='block'; txt.style.display='none';
        hid.value=preSelect||'';
        sel.onchange=()=>{ hid.value=sel.value; };
      } else {
        sel.style.display='none'; txt.style.display='block';
        txt.value=preSelect||'';
        hid.value=preSelect||'';
      }
    })
    .catch(()=>{ pLd('p_arrond_ld',false); sel.style.display='none'; txt.style.display='block'; txt.value=preSelect||''; hid.value=preSelect||''; });
}

function pLoadVilles(countryEn, dept, preSelect){
  const sel=document.getElementById('p_ville_sel');
  pLd('p_ville_ld',true);
  pReset(sel,true,'Chargement…');
  fetch('/api/geo/state-cities/'+encodeURIComponent(countryEn)+'/'+encodeURIComponent(dept))
    .then(r=>r.json())
    .then(list=>{
      pLd('p_ville_ld',false);
      pReset(sel,false,'— Ville / Résidence —');
      list.forEach(name=>{
        const o=document.createElement('option');
        o.value=name; o.textContent=name;
        if(name===preSelect) o.selected=true;
        sel.appendChild(o);
      });
      sel.disabled=list.length===0;
    })
    .catch(()=>{pLd('p_ville_ld',false);pReset(sel,false,'— Villes non disponibles —')});
}

document.addEventListener('DOMContentLoaded',function(){
  const pSel=document.getElementById('p_pays_sel');
  const pVal=document.getElementById('p_pays_val');
  const pIso=document.getElementById('p_iso_val');

  P_CTRS.sort((a,b)=>a.fr.localeCompare(b.fr,'fr')).forEach(c=>{
    const o=document.createElement('option');
    o.value=c.iso; o.dataset.en=c.en; o.dataset.fr=c.fr;
    o.textContent=pFlag(c.iso)+' '+c.fr;
    if(c.iso===PRE_ISO || c.fr===PRE_PAYS || c.en===PRE_PAYS) o.selected=true;
    pSel.appendChild(o);
  });

  pSel.addEventListener('change',function(){
    const opt=pSel.options[pSel.selectedIndex];
    pVal.value=opt.dataset.fr||'';
    pIso.value=opt.value||'';
    pReset(document.getElementById('p_region_sel'),true,'— Choisir un pays d\'abord —');
    pReset(document.getElementById('p_dept_sel'),true,'— Choisir une région d\'abord —');
    pReset(document.getElementById('p_ville_sel'),true,'— Choisir un département d\'abord —');
    document.getElementById('p_arrond_sel').style.display='none';
    document.getElementById('p_arrond_txt').style.display='block';
    document.getElementById('p_arrond_txt').value='';
    document.getElementById('p_h_arrond').value='';
    if(opt.dataset.en) pLoadRegions(opt.dataset.en,'');
  });

  document.getElementById('p_region_sel').addEventListener('change',function(){
    const opt=pSel.options[pSel.selectedIndex];
    const region=this.value;
    pReset(document.getElementById('p_dept_sel'),true,'Chargement…');
    pReset(document.getElementById('p_ville_sel'),true,'— Choisir un département d\'abord —');
    document.getElementById('p_arrond_sel').style.display='none';
    document.getElementById('p_arrond_txt').style.display='block';
    document.getElementById('p_arrond_txt').value='';
    document.getElementById('p_h_arrond').value='';
    if(opt && opt.dataset.en && region) pLoadDepts(opt.dataset.en, region,'');
  });

  document.getElementById('p_dept_sel').addEventListener('change',function(){
    const opt=pSel.options[pSel.selectedIndex];
    const dept=this.value;
    pReset(document.getElementById('p_ville_sel'),true,'Chargement…');
    document.getElementById('p_arrond_sel').style.display='none';
    document.getElementById('p_arrond_txt').style.display='block';
    document.getElementById('p_arrond_txt').value='';
    document.getElementById('p_h_arrond').value='';
    if(opt && opt.dataset.en && dept){
      pLoadArronds(opt.dataset.en, dept,'');
      pLoadVilles(opt.dataset.en, dept,'');
    }
  });

  document.getElementById('p_arrond_txt').addEventListener('input',function(){
    document.getElementById('p_h_arrond').value=this.value;
  });

  if(PRE_ISO || PRE_PAYS){
    const opt=Array.from(pSel.options).find(o=>o.value===PRE_ISO || o.dataset.fr===PRE_PAYS || o.dataset.en===PRE_PAYS);
    if(opt){
      pSel.value=opt.value;
      pVal.value=opt.dataset.fr||'';
      pIso.value=opt.value||'';
      if(opt.dataset.en) pLoadRegions(opt.dataset.en, PRE_REGION);
    }
  }
});
})();
</script>

@endsection
