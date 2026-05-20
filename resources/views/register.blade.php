<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription — SupServer</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
:root{
  --bg:#040d1a;--bg2:#071426;--card:#0c1c34;--card2:#0f2040;
  --green:#39ff14;--cyan:#00d4ff;
  --bd:rgba(57,255,20,.18);--bd2:rgba(0,212,255,.2);
  --text:#cde0f5;--muted:rgba(205,224,245,.4);
}
html,body{width:100%;min-height:100vh;background:var(--bg);font-family:Arial,sans-serif;color:var(--text);}

.bg-grid{position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:linear-gradient(rgba(57,255,20,.02) 1px,transparent 1px),linear-gradient(90deg,rgba(57,255,20,.02) 1px,transparent 1px);
  background-size:48px 48px;}
.bg-glow{position:fixed;top:-200px;left:50%;transform:translateX(-50%);width:600px;height:400px;border-radius:50%;
  background:radial-gradient(ellipse,rgba(57,255,20,.06) 0%,transparent 70%);pointer-events:none;z-index:0;}

.reg-page{position:relative;z-index:1;min-height:100vh;display:flex;flex-direction:column;align-items:center;padding:30px 16px 50px;}

/* Header */
.reg-header{text-align:center;margin-bottom:28px;animation:fadeDown .6s ease;}
.reg-logo{display:inline-flex;align-items:center;gap:10px;margin-bottom:8px;}
.reg-logo-icon{width:38px;height:38px;border-radius:8px;background:rgba(57,255,20,.08);border:1.5px solid var(--green);
  box-shadow:0 0 14px rgba(57,255,20,.25);display:flex;align-items:center;justify-content:center;font-size:18px;}
.reg-logo-text{font-size:14px;font-weight:700;letter-spacing:4px;color:var(--green);}
.reg-title{font-size:26px;font-weight:900;letter-spacing:.1em;color:#fff;text-transform:uppercase;}
.reg-sub{font-size:12px;color:var(--muted);letter-spacing:.04em;margin-top:4px;}

/* Card */
.reg-card{width:100%;max-width:860px;background:var(--card);border:1px solid var(--bd);border-radius:20px;
  overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,.5);animation:fadeUp .7s ease .1s both;}

/* Steps header */
.steps-header{background:var(--card2);padding:20px 28px 0;border-bottom:1px solid var(--bd);}
.steps-row{display:flex;align-items:center;justify-content:center;overflow-x:auto;}
.step-item{display:flex;flex-direction:column;align-items:center;gap:4px;min-width:100px;position:relative;flex:1;cursor:pointer;}
.step-item::before{content:'';position:absolute;top:16px;left:calc(50% + 16px);right:calc(-50% + 16px);height:2px;
  background:rgba(255,255,255,.08);transition:background .4s;z-index:0;}
.step-item:last-child::before{display:none;}
.step-item.done::before,.step-item.active::before{background:var(--green);}
.step-num{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;
  font-size:13px;font-weight:700;background:rgba(255,255,255,.05);border:2px solid rgba(255,255,255,.12);
  color:var(--muted);transition:all .35s;position:relative;z-index:1;}
.step-item.done  .step-num{background:var(--green);border-color:var(--green);color:#000;}
.step-item.active .step-num{background:transparent;border-color:var(--green);color:var(--green);box-shadow:0 0 12px rgba(57,255,20,.4);}
.step-label{font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);
  transition:color .3s;white-space:nowrap;padding-bottom:12px;}
.step-item.done  .step-label{color:var(--green);}
.step-item.active .step-label{color:#fff;}
.prog-bar{height:3px;background:rgba(255,255,255,.05);}
.prog-fill{height:100%;background:linear-gradient(90deg,var(--green),var(--cyan));transition:width .5s ease;box-shadow:0 0 8px rgba(57,255,20,.4);}

/* Body */
.reg-body{padding:30px 32px;}
@media(max-width:600px){.reg-body{padding:20px 16px;}}
.step-pane{display:none;animation:fadeIn .35s ease;}
.step-pane.active{display:block;}
.pane-title{font-size:18px;font-weight:700;color:#fff;margin-bottom:4px;letter-spacing:.04em;}
.pane-sub{font-size:12px;color:var(--muted);margin-bottom:22px;}

/* Grid */
.fg{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.fg.col1{grid-template-columns:1fr;}
@media(max-width:640px){.fg{grid-template-columns:1fr;}}
.f-full{grid-column:1/-1;}

/* Field */
.fld{display:flex;flex-direction:column;gap:5px;}
.flbl{font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:rgba(57,255,20,.7);font-weight:700;}
.flbl .req{color:#ff5a5a;margin-left:2px;}
.finp{background:rgba(0,0,0,.35);border:1px solid rgba(57,255,20,.15);border-radius:8px;padding:11px 13px;
  color:#fff;font-size:13px;transition:border-color .25s,box-shadow .25s;outline:none;width:100%;}
.finp:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(57,255,20,.08);}
.finp::placeholder{color:rgba(255,255,255,.2);}
.finp[readonly]{color:rgba(255,255,255,.4);cursor:default;}
select.finp option{background:#0c1c34;color:#fff;}

/* Tom Select dark */
.ts-wrapper .ts-control{background:rgba(0,0,0,.35)!important;border:1px solid rgba(57,255,20,.15)!important;
  border-radius:8px!important;color:#fff!important;padding:10px 12px!important;min-height:44px!important;font-size:13px!important;}
.ts-wrapper.focus .ts-control{border-color:var(--green)!important;box-shadow:0 0 0 3px rgba(57,255,20,.08)!important;}
.ts-wrapper .ts-dropdown{background:#0c1c34!important;border:1px solid var(--bd)!important;border-radius:10px!important;
  margin-top:4px!important;box-shadow:0 8px 30px rgba(0,0,0,.6)!important;overflow:hidden;}
.ts-wrapper .ts-dropdown .option{padding:10px 14px!important;color:var(--text)!important;font-size:13px!important;display:flex;align-items:center;gap:8px;}
.ts-wrapper .ts-dropdown .option:hover,.ts-wrapper .ts-dropdown .option.active{background:rgba(57,255,20,.1)!important;color:#fff!important;}
.ts-wrapper .ts-control input{color:#fff!important;background:transparent!important;}

/* Photo */
.photo-zone{display:flex;align-items:center;gap:16px;padding:16px;background:rgba(0,0,0,.25);
  border:2px dashed rgba(57,255,20,.2);border-radius:12px;cursor:pointer;transition:border-color .25s;grid-column:1/-1;}
.photo-zone:hover{border-color:var(--green);}
.photo-preview{width:72px;height:72px;border-radius:50%;background:rgba(57,255,20,.07);
  border:2px solid var(--bd);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;font-size:28px;}
.photo-preview img{width:100%;height:100%;object-fit:cover;border-radius:50%;}
.photo-info .photo-title{font-size:13px;color:#fff;margin-bottom:4px;}
.photo-info .photo-sub{font-size:11px;color:var(--muted);}

/* Dial */
.dial-row{display:flex;gap:8px;align-items:flex-end;}
.dial-badge{flex-shrink:0;height:44px;min-width:80px;background:rgba(57,255,20,.07);border:1px solid var(--bd);
  border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;
  color:var(--green);letter-spacing:.04em;transition:all .25s;}
.dial-badge.loaded{box-shadow:0 0 10px rgba(57,255,20,.2);}

/* Loader */
.loader{display:none;align-items:center;gap:6px;font-size:11px;color:var(--muted);margin-top:4px;}
.loader.show{display:flex;}
.spin{width:12px;height:12px;border:2px solid rgba(57,255,20,.2);border-top-color:var(--green);
  border-radius:50%;animation:spin .8s linear infinite;}
@keyframes spin{to{transform:rotate(360deg)}}

/* Section divider */
.sec-div{grid-column:1/-1;display:flex;align-items:center;gap:10px;margin:8px 0 2px;}
.sec-div-line{flex:1;height:1px;background:var(--bd);}
.sec-div-text{font-size:9px;letter-spacing:.14em;text-transform:uppercase;color:rgba(57,255,20,.5);}

/* Password */
.pwd-bars{display:flex;gap:3px;margin-top:5px;}
.pwd-bar{flex:1;height:3px;border-radius:2px;background:rgba(255,255,255,.07);transition:background .3s;}
.s1,.s2{background:#ff4444;}.s3{background:#ffaa00;}.s4,.s5{background:#39ff14;}
.pwd-hint{font-size:10px;margin-top:3px;color:var(--muted);}
.eye-wrap{position:relative;}
.eye-wrap .finp{padding-right:40px;}
.eye-btn{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;
  color:rgba(57,255,20,.45);font-size:15px;line-height:1;transition:color .2s;padding:0;}
.eye-btn:hover{color:var(--green);}

/* Recap */
.recap-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;background:rgba(0,0,0,.25);
  border-radius:12px;padding:16px;border:1px solid var(--bd);}
@media(max-width:600px){.recap-grid{grid-template-columns:1fr;}}
.recap-item{display:flex;flex-direction:column;gap:2px;padding:8px 10px;border-radius:8px;background:rgba(57,255,20,.03);}
.recap-label{font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:rgba(57,255,20,.5);}
.recap-val{font-size:13px;color:#fff;word-break:break-word;}
.recap-avatar{grid-column:1/-1;display:flex;align-items:center;gap:14px;padding:14px;
  background:rgba(0,212,255,.04);border:1px solid var(--bd2);border-radius:12px;}
.recap-avatar-img{width:56px;height:56px;border-radius:50%;border:2px solid var(--cyan);
  background:rgba(0,212,255,.1);display:flex;align-items:center;justify-content:center;font-size:22px;overflow:hidden;flex-shrink:0;}
.recap-avatar-img img{width:100%;height:100%;object-fit:cover;}
.recap-name{font-size:18px;font-weight:700;color:#fff;}
.recap-role{font-size:11px;color:var(--cyan);letter-spacing:.06em;margin-top:3px;}
.conf-badge{background:rgba(57,255,20,.08);border:1px solid rgba(57,255,20,.3);border-radius:12px;
  padding:16px;display:flex;align-items:center;gap:12px;margin-bottom:18px;}
.conf-badge-icon{font-size:28px;}
.conf-badge-text{font-size:13px;color:rgba(255,255,255,.8);line-height:1.6;}
.cgu-row{display:flex;align-items:flex-start;gap:10px;padding:12px;background:rgba(0,0,0,.2);
  border-radius:10px;border:1px solid var(--bd);margin-bottom:8px;}
.cgu-check{width:18px;height:18px;border-radius:4px;border:1.5px solid var(--green);cursor:pointer;
  background:transparent;flex-shrink:0;margin-top:1px;appearance:none;-webkit-appearance:none;
  display:flex;align-items:center;justify-content:center;}
.cgu-check:checked{background:var(--green);}
.cgu-check:checked::after{content:'✓';font-size:11px;color:#000;font-weight:900;}
.cgu-label{font-size:12px;color:var(--muted);}

/* Flash */
.flash{padding:12px 16px;border-radius:10px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:10px;}
.flash-err{background:rgba(255,60,60,.1);border:1px solid rgba(255,60,60,.3);color:#ff6060;}

/* Buttons */
.nav-row{display:flex;align-items:center;justify-content:space-between;margin-top:28px;padding-top:20px;
  border-top:1px solid var(--bd);flex-wrap:wrap;gap:12px;}
.btn{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:8px;font-size:13px;
  font-weight:700;letter-spacing:.08em;text-transform:uppercase;cursor:pointer;border:none;transition:all .25s ease;outline:none;}
.btn-prev{background:transparent;color:var(--muted);border:1.5px solid rgba(255,255,255,.1);}
.btn-prev:hover{border-color:rgba(255,255,255,.3);color:#fff;}
.btn-next{background:transparent;color:var(--green);border:1.5px solid var(--green);box-shadow:0 0 14px rgba(57,255,20,.15);}
.btn-next:hover{background:rgba(57,255,20,.1);box-shadow:0 0 26px rgba(57,255,20,.4);transform:translateY(-2px);color:#fff;}
.btn-submit{background:var(--green);color:#000;font-weight:900;border:none;box-shadow:0 0 20px rgba(57,255,20,.4);}
.btn-submit:hover{background:#5fff3a;transform:translateY(-2px);box-shadow:0 0 36px rgba(57,255,20,.6);}
.btn-skip{background:none;border:none;font-size:11px;color:var(--muted);letter-spacing:.06em;cursor:pointer;text-transform:uppercase;padding:4px 8px;}
.btn-skip:hover{color:#fff;}

/* Footer */
.reg-footer{text-align:center;margin-top:22px;font-size:13px;color:var(--muted);animation:fadeUp .6s ease .3s both;}
.reg-footer a{color:var(--cyan);text-decoration:none;}
.reg-footer a:hover{color:#fff;}

/* Err */
.finp.err{border-color:#ff5a5a!important;}
.err-msg{font-size:11px;color:#ff6060;margin-top:3px;}

@keyframes fadeDown{from{opacity:0;transform:translateY(-16px)}to{opacity:1;transform:none}}
@keyframes fadeUp  {from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:none}}
@keyframes fadeIn  {from{opacity:0;transform:scale(.98)}       to{opacity:1;transform:none}}
</style>
</head>
<body>

<div class="bg-grid"></div>
<div class="bg-glow"></div>

<div class="reg-page">

  <div class="reg-header">
    <div class="reg-logo">
      <div class="reg-logo-icon">🖥️</div>
      <span class="reg-logo-text">SUPSERVER</span>
    </div>
    <h1 class="reg-title">Créer votre compte</h1>
    <p class="reg-sub">Plateforme mondiale de surveillance des salles serveurs</p>
  </div>

  <div class="reg-card">

    <div class="steps-header">
      <div class="steps-row">
        <div class="step-item active" data-s="1"><div class="step-num">1</div><div class="step-label">Identité</div></div>
        <div class="step-item" data-s="2"><div class="step-num">2</div><div class="step-label">Localisation</div></div>
        <div class="step-item" data-s="3"><div class="step-num">3</div><div class="step-label">Professionnel</div></div>
        <div class="step-item" data-s="4"><div class="step-num">4</div><div class="step-label">Compte</div></div>
        <div class="step-item" data-s="5"><div class="step-num">5</div><div class="step-label">Confirmation</div></div>
      </div>
    </div>
    <div class="prog-bar"><div class="prog-fill" id="progFill" style="width:20%"></div></div>

    <div class="reg-body">

      @if($errors->any())
        <div class="flash flash-err">❌ {{ $errors->first() }}</div>
      @endif

      <form action="/register-user" method="POST" enctype="multipart/form-data" id="regForm" novalidate>
        @csrf
        <input type="hidden" name="iso_pays"      id="h_iso">
        <input type="hidden" name="indicatif_tel" id="h_dial">
        <input type="hidden" name="nationalite"   id="h_nat">

        {{-- ══ STEP 1 : IDENTITÉ ══ --}}
        <div class="step-pane active" id="pane1">
          <div class="pane-title">👤 Identité personnelle</div>
          <div class="pane-sub">Vos informations de base</div>
          <div class="fg">
            <div class="photo-zone" onclick="document.getElementById('photoInput').click()">
              <div class="photo-preview" id="photoPrev">📷</div>
              <div class="photo-info">
                <div class="photo-title">Photo de profil (optionnel)</div>
                <div class="photo-sub">Cliquez pour choisir · JPG, PNG · Max 2 Mo</div>
              </div>
              <input type="file" name="photo_profil" id="photoInput" accept="image/*" style="display:none" onchange="previewPhoto(this)">
            </div>
            <div class="fld">
              <label class="flbl">Prénom <span class="req">*</span></label>
              <input class="finp" type="text" name="prenom" id="prenom" placeholder="Votre prénom" value="{{ old('prenom') }}">
            </div>
            <div class="fld">
              <label class="flbl">Nom <span class="req">*</span></label>
              <input class="finp" type="text" name="nom" id="nom" placeholder="Votre nom de famille" value="{{ old('nom') }}">
            </div>
            <div class="fld">
              <label class="flbl">Sexe</label>
              <select class="finp" name="sexe">
                <option value="">— Sélectionner —</option>
                <option value="M" {{ old('sexe')=='M'?'selected':'' }}>Homme</option>
                <option value="F" {{ old('sexe')=='F'?'selected':'' }}>Femme</option>
                <option value="A" {{ old('sexe')=='A'?'selected':'' }}>Autre</option>
              </select>
            </div>
            <div class="fld">
              <label class="flbl">Date de naissance</label>
              <input class="finp" type="date" name="date_naissance" value="{{ old('date_naissance') }}" max="{{ date('Y-m-d') }}">
            </div>
            <div class="fld f-full">
              <label class="flbl">Statut matrimonial</label>
              <select class="finp" name="statut_matrimonial">
                <option value="">— Sélectionner —</option>
                <option value="celibataire">Célibataire</option>
                <option value="marie">Marié(e)</option>
                <option value="divorce">Divorcé(e)</option>
                <option value="veuf">Veuf / Veuve</option>
                <option value="en_couple">En couple</option>
              </select>
            </div>
          </div>
          <div class="nav-row">
            <span></span>
            <div style="display:flex;align-items:center;gap:12px">
              <button type="button" class="btn-skip" onclick="goStep(2)">Passer →</button>
              <button type="button" class="btn btn-next" onclick="nextStep()">Suivant →</button>
            </div>
          </div>
        </div>

        {{-- ══ STEP 2 : LOCALISATION ══ --}}
        <div class="step-pane" id="pane2">
          <div class="pane-title">🌍 Localisation géographique</div>
          <div class="pane-sub">Votre pays, région et adresse</div>
          <div class="fg">
            <div class="fld f-full">
              <label class="flbl">Pays <span class="req">*</span></label>
              <select id="pays_select" name="pays" placeholder="Rechercher un pays..."></select>
            </div>
            <div class="fld f-full">
              <label class="flbl">Téléphone <span class="req">*</span></label>
              <div class="dial-row">
                <div class="dial-badge" id="dialBadge">+—</div>
                <input class="finp" type="tel" name="telephone" id="telephone" placeholder="Numéro de téléphone" style="flex:1" value="{{ old('telephone') }}">
              </div>
            </div>
            {{-- Niveau 2 : Région --}}
            <div class="fld">
              <label class="flbl">🌐 Région / Province / État</label>
              <select class="finp" name="region" id="region_sel" disabled>
                <option value="">— Choisir un pays d'abord —</option>
              </select>
              <div class="loader" id="regLoader"><div class="spin"></div> Chargement des régions...</div>
            </div>
            {{-- Niveau 3 : Département --}}
            <div class="fld">
              <label class="flbl">🏛️ Département / District / Comté</label>
              <select class="finp" name="departement" id="dept_sel" disabled>
                <option value="">— Choisir une région d'abord —</option>
              </select>
              <div class="loader" id="deptLoader"><div class="spin"></div> Chargement des départements...</div>
            </div>
            {{-- Niveau 4 : Arrondissement --}}
            <div class="fld">
              <label class="flbl">🏘️ Arrondissement / Commune</label>
              <input type="hidden" name="arrondissement" id="h_arrond" value="{{ old('arrondissement') }}">
              <select class="finp" id="arrond_sel" style="display:none">
                <option value="">— Sélectionner un arrondissement —</option>
              </select>
              <input class="finp" id="arrond_txt" type="text" placeholder="Ex: Yaoundé 1er, Lyon 3e, District 1..." value="{{ old('arrondissement') }}">
              <div class="loader" id="arrondLoader" style="display:none"><div class="spin"></div> Chargement...</div>
            </div>
            {{-- Niveau 5 : Ville --}}
            <div class="fld">
              <label class="flbl">🏙️ Ville / Résidence</label>
              <select class="finp" name="ville_residence" id="ville_sel" disabled>
                <option value="">— Choisir un département d'abord —</option>
              </select>
              <div class="loader" id="villeLoader"><div class="spin"></div> Chargement des villes...</div>
            </div>
            <div class="fld">
              <label class="flbl">Quartier</label>
              <input class="finp" type="text" name="quartier" placeholder="Ex: Bastos, Montmartre, Downtown..." value="{{ old('quartier') }}">
            </div>
            <div class="fld">
              <label class="flbl">Adresse complète</label>
              <input class="finp" type="text" name="adresse" placeholder="Rue, numéro, bâtiment..." value="{{ old('adresse') }}">
            </div>
          </div>
          <div class="nav-row">
            <button type="button" class="btn btn-prev" onclick="goStep(1)">← Retour</button>
            <button type="button" class="btn btn-next" onclick="nextStep()">Suivant →</button>
          </div>
        </div>

        {{-- ══ STEP 3 : PROFESSIONNEL ══ --}}
        <div class="step-pane" id="pane3">
          <div class="pane-title">💼 Informations professionnelles</div>
          <div class="pane-sub">Votre activité et votre rôle</div>
          <div class="fg">
            <div class="fld">
              <label class="flbl">Profession</label>
              <input class="finp" type="text" name="profession" placeholder="Ex: Ingénieur réseau, Admin système..." value="{{ old('profession') }}">
            </div>
            <div class="fld">
              <label class="flbl">Organisation / Entreprise</label>
              <input class="finp" type="text" name="organisation" placeholder="Nom de l'entreprise..." value="{{ old('organisation') }}">
            </div>
            <div class="fld f-full">
              <label class="flbl">Rôle sur la plateforme</label>
              <select class="finp" name="role" id="role_sel">
                <option value="utilisateur"    {{ old('role','utilisateur')=='utilisateur'   ?'selected':'' }}>👤 Utilisateur standard</option>
                <option value="technicien"     {{ old('role')=='technicien'   ?'selected':'' }}>🔧 Technicien</option>
                <option value="superviseur"    {{ old('role')=='superviseur'  ?'selected':'' }}>📊 Superviseur</option>
                <option value="administrateur" {{ old('role')=='administrateur'?'selected':'' }}>🛡️ Administrateur</option>
                <option value="prestataire"    {{ old('role')=='prestataire'  ?'selected':'' }}>🏢 Prestataire</option>
                <option value="invite"         {{ old('role')=='invite'       ?'selected':'' }}>👁️ Invité (lecture seule)</option>
              </select>
            </div>
          </div>
          <div class="nav-row">
            <button type="button" class="btn btn-prev" onclick="goStep(2)">← Retour</button>
            <button type="button" class="btn btn-next" onclick="nextStep()">Suivant →</button>
          </div>
        </div>

        {{-- ══ STEP 4 : COMPTE ══ --}}
        <div class="step-pane" id="pane4">
          <div class="pane-title">🔐 Informations de connexion</div>
          <div class="pane-sub">Votre email et mot de passe sécurisé</div>
          <div class="fg col1">
            <div class="fld">
              <label class="flbl">Adresse email <span class="req">*</span></label>
              <input class="finp" type="email" name="email" id="email" placeholder="vous@exemple.com"
                     value="{{ old('email') }}" oninput="checkEmail(this.value)" autocomplete="email">
              <div class="err-msg" id="emailErr"></div>
            </div>
            <div class="fld">
              <label class="flbl">Mot de passe <span class="req">*</span></label>
              <div class="eye-wrap">
                <input class="finp" type="password" name="password" id="password"
                       placeholder="Minimum 8 caractères" oninput="checkPwd(this.value)" autocomplete="new-password">
                <button type="button" class="eye-btn" onclick="toggleEye('password',this)">👁</button>
              </div>
              <div class="pwd-bars">
                <div class="pwd-bar" id="pb1"></div><div class="pwd-bar" id="pb2"></div>
                <div class="pwd-bar" id="pb3"></div><div class="pwd-bar" id="pb4"></div>
                <div class="pwd-bar" id="pb5"></div>
              </div>
              <div class="pwd-hint" id="pwdHint">Minimum 8 caractères</div>
            </div>
            <div class="fld">
              <label class="flbl">Confirmer le mot de passe <span class="req">*</span></label>
              <div class="eye-wrap">
                <input class="finp" type="password" name="password_confirmation" id="pwdConf"
                       placeholder="Répéter le mot de passe" oninput="checkMatch()" autocomplete="new-password">
                <button type="button" class="eye-btn" onclick="toggleEye('pwdConf',this)">👁</button>
              </div>
              <div class="err-msg" id="matchErr"></div>
            </div>
          </div>
          <div class="nav-row">
            <button type="button" class="btn btn-prev" onclick="goStep(3)">← Retour</button>
            <button type="button" class="btn btn-next" onclick="nextStep()">Vérifier →</button>
          </div>
        </div>

        {{-- ══ STEP 5 : CONFIRMATION ══ --}}
        <div class="step-pane" id="pane5">
          <div class="pane-title">✅ Confirmation</div>
          <div class="pane-sub">Vérifiez vos informations avant de soumettre</div>
          <div class="conf-badge">
            <div class="conf-badge-icon">🔔</div>
            <div class="conf-badge-text">
              Votre compte sera soumis à <strong>validation par l'administrateur</strong>.<br>
              Un email de confirmation sera envoyé à l'adresse indiquée.
            </div>
          </div>
          <div class="recap-grid" id="recapGrid">
            <div class="recap-avatar">
              <div class="recap-avatar-img" id="recapAvatarImg">👤</div>
              <div><div class="recap-name" id="recapName">—</div><div class="recap-role" id="recapRole">—</div></div>
            </div>
            <div class="recap-item"><div class="recap-label">Email</div><div class="recap-val" id="r_email">—</div></div>
            <div class="recap-item"><div class="recap-label">Téléphone</div><div class="recap-val" id="r_tel">—</div></div>
            <div class="recap-item"><div class="recap-label">Pays</div><div class="recap-val" id="r_pays">—</div></div>
            <div class="recap-item"><div class="recap-label">Région</div><div class="recap-val" id="r_region">—</div></div>
            <div class="recap-item"><div class="recap-label">Département</div><div class="recap-val" id="r_dept">—</div></div>
            <div class="recap-item"><div class="recap-label">Arrondissement</div><div class="recap-val" id="r_arrond">—</div></div>
            <div class="recap-item"><div class="recap-label">Profession</div><div class="recap-val" id="r_prof">—</div></div>
            <div class="recap-item"><div class="recap-label">Organisation</div><div class="recap-val" id="r_org">—</div></div>
          </div>
          <div style="margin-top:18px">
            <div class="cgu-row">
              <input type="checkbox" class="cgu-check" id="cgu" required>
              <label class="cgu-label" for="cgu">
                J'accepte les conditions d'utilisation et confirme que mes informations sont exactes.
              </label>
            </div>
          </div>
          <div class="nav-row">
            <button type="button" class="btn btn-prev" onclick="goStep(4)">← Modifier</button>
            <button type="submit" class="btn btn-submit">🚀 Créer mon compte</button>
          </div>
        </div>

      </form>
    </div>
  </div>

  <div class="reg-footer">
    Déjà un compte ? <a href="/login">Se connecter →</a>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
/* ── 195 Pays ────────────────────────────────────── */
const COUNTRIES=[
  {iso:'AF',fr:'Afghanistan',en:'Afghanistan',dial:'+93'},
  {iso:'ZA',fr:'Afrique du Sud',en:'South Africa',dial:'+27'},
  {iso:'AL',fr:'Albanie',en:'Albania',dial:'+355'},
  {iso:'DZ',fr:'Algérie',en:'Algeria',dial:'+213'},
  {iso:'DE',fr:'Allemagne',en:'Germany',dial:'+49'},
  {iso:'AD',fr:'Andorre',en:'Andorra',dial:'+376'},
  {iso:'AO',fr:'Angola',en:'Angola',dial:'+244'},
  {iso:'AG',fr:'Antigua-et-Barbuda',en:'Antigua and Barbuda',dial:'+1268'},
  {iso:'SA',fr:'Arabie saoudite',en:'Saudi Arabia',dial:'+966'},
  {iso:'AR',fr:'Argentine',en:'Argentina',dial:'+54'},
  {iso:'AM',fr:'Arménie',en:'Armenia',dial:'+374'},
  {iso:'AU',fr:'Australie',en:'Australia',dial:'+61'},
  {iso:'AT',fr:'Autriche',en:'Austria',dial:'+43'},
  {iso:'AZ',fr:'Azerbaïdjan',en:'Azerbaijan',dial:'+994'},
  {iso:'BS',fr:'Bahamas',en:'Bahamas',dial:'+1242'},
  {iso:'BH',fr:'Bahreïn',en:'Bahrain',dial:'+973'},
  {iso:'BD',fr:'Bangladesh',en:'Bangladesh',dial:'+880'},
  {iso:'BB',fr:'Barbade',en:'Barbados',dial:'+1246'},
  {iso:'BE',fr:'Belgique',en:'Belgium',dial:'+32'},
  {iso:'BZ',fr:'Belize',en:'Belize',dial:'+501'},
  {iso:'BJ',fr:'Bénin',en:'Benin',dial:'+229'},
  {iso:'BT',fr:'Bhoutan',en:'Bhutan',dial:'+975'},
  {iso:'BY',fr:'Biélorussie',en:'Belarus',dial:'+375'},
  {iso:'BO',fr:'Bolivie',en:'Bolivia',dial:'+591'},
  {iso:'BA',fr:'Bosnie-Herzégovine',en:'Bosnia and Herzegovina',dial:'+387'},
  {iso:'BW',fr:'Botswana',en:'Botswana',dial:'+267'},
  {iso:'BR',fr:'Brésil',en:'Brazil',dial:'+55'},
  {iso:'BN',fr:'Brunei',en:'Brunei',dial:'+673'},
  {iso:'BG',fr:'Bulgarie',en:'Bulgaria',dial:'+359'},
  {iso:'BF',fr:'Burkina Faso',en:'Burkina Faso',dial:'+226'},
  {iso:'BI',fr:'Burundi',en:'Burundi',dial:'+257'},
  {iso:'CV',fr:'Cap-Vert',en:'Cape Verde',dial:'+238'},
  {iso:'KH',fr:'Cambodge',en:'Cambodia',dial:'+855'},
  {iso:'CM',fr:'Cameroun',en:'Cameroon',dial:'+237'},
  {iso:'CA',fr:'Canada',en:'Canada',dial:'+1'},
  {iso:'CF',fr:'Rép. centrafricaine',en:'Central African Republic',dial:'+236'},
  {iso:'CL',fr:'Chili',en:'Chile',dial:'+56'},
  {iso:'CN',fr:'Chine',en:'China',dial:'+86'},
  {iso:'CY',fr:'Chypre',en:'Cyprus',dial:'+357'},
  {iso:'CO',fr:'Colombie',en:'Colombia',dial:'+57'},
  {iso:'KM',fr:'Comores',en:'Comoros',dial:'+269'},
  {iso:'CG',fr:'Congo',en:'Republic of the Congo',dial:'+242'},
  {iso:'CD',fr:'RD Congo',en:'Democratic Republic of the Congo',dial:'+243'},
  {iso:'KP',fr:'Corée du Nord',en:'North Korea',dial:'+850'},
  {iso:'KR',fr:'Corée du Sud',en:'South Korea',dial:'+82'},
  {iso:'CR',fr:'Costa Rica',en:'Costa Rica',dial:'+506'},
  {iso:'CI',fr:"Côte d'Ivoire",en:'Ivory Coast',dial:'+225'},
  {iso:'HR',fr:'Croatie',en:'Croatia',dial:'+385'},
  {iso:'CU',fr:'Cuba',en:'Cuba',dial:'+53'},
  {iso:'DK',fr:'Danemark',en:'Denmark',dial:'+45'},
  {iso:'DJ',fr:'Djibouti',en:'Djibouti',dial:'+253'},
  {iso:'DM',fr:'Dominique',en:'Dominica',dial:'+1767'},
  {iso:'EG',fr:'Égypte',en:'Egypt',dial:'+20'},
  {iso:'SV',fr:'Salvador',en:'El Salvador',dial:'+503'},
  {iso:'AE',fr:'Émirats arabes unis',en:'United Arab Emirates',dial:'+971'},
  {iso:'EC',fr:'Équateur',en:'Ecuador',dial:'+593'},
  {iso:'ER',fr:'Érythrée',en:'Eritrea',dial:'+291'},
  {iso:'ES',fr:'Espagne',en:'Spain',dial:'+34'},
  {iso:'EE',fr:'Estonie',en:'Estonia',dial:'+372'},
  {iso:'SZ',fr:'Eswatini',en:'Eswatini',dial:'+268'},
  {iso:'ET',fr:'Éthiopie',en:'Ethiopia',dial:'+251'},
  {iso:'FJ',fr:'Fidji',en:'Fiji',dial:'+679'},
  {iso:'FI',fr:'Finlande',en:'Finland',dial:'+358'},
  {iso:'FR',fr:'France',en:'France',dial:'+33'},
  {iso:'GA',fr:'Gabon',en:'Gabon',dial:'+241'},
  {iso:'GM',fr:'Gambie',en:'Gambia',dial:'+220'},
  {iso:'GE',fr:'Géorgie',en:'Georgia',dial:'+995'},
  {iso:'GH',fr:'Ghana',en:'Ghana',dial:'+233'},
  {iso:'GR',fr:'Grèce',en:'Greece',dial:'+30'},
  {iso:'GD',fr:'Grenade',en:'Grenada',dial:'+1473'},
  {iso:'GT',fr:'Guatemala',en:'Guatemala',dial:'+502'},
  {iso:'GN',fr:'Guinée',en:'Guinea',dial:'+224'},
  {iso:'GW',fr:'Guinée-Bissau',en:'Guinea-Bissau',dial:'+245'},
  {iso:'GQ',fr:'Guinée équatoriale',en:'Equatorial Guinea',dial:'+240'},
  {iso:'GY',fr:'Guyana',en:'Guyana',dial:'+592'},
  {iso:'HT',fr:'Haïti',en:'Haiti',dial:'+509'},
  {iso:'HN',fr:'Honduras',en:'Honduras',dial:'+504'},
  {iso:'HU',fr:'Hongrie',en:'Hungary',dial:'+36'},
  {iso:'IN',fr:'Inde',en:'India',dial:'+91'},
  {iso:'ID',fr:'Indonésie',en:'Indonesia',dial:'+62'},
  {iso:'IQ',fr:'Irak',en:'Iraq',dial:'+964'},
  {iso:'IR',fr:'Iran',en:'Iran',dial:'+98'},
  {iso:'IE',fr:'Irlande',en:'Ireland',dial:'+353'},
  {iso:'IS',fr:'Islande',en:'Iceland',dial:'+354'},
  {iso:'IL',fr:'Israël',en:'Israel',dial:'+972'},
  {iso:'IT',fr:'Italie',en:'Italy',dial:'+39'},
  {iso:'JM',fr:'Jamaïque',en:'Jamaica',dial:'+1876'},
  {iso:'JP',fr:'Japon',en:'Japan',dial:'+81'},
  {iso:'JO',fr:'Jordanie',en:'Jordan',dial:'+962'},
  {iso:'KZ',fr:'Kazakhstan',en:'Kazakhstan',dial:'+7'},
  {iso:'KE',fr:'Kenya',en:'Kenya',dial:'+254'},
  {iso:'KG',fr:'Kirghizistan',en:'Kyrgyzstan',dial:'+996'},
  {iso:'KI',fr:'Kiribati',en:'Kiribati',dial:'+686'},
  {iso:'XK',fr:'Kosovo',en:'Kosovo',dial:'+383'},
  {iso:'KW',fr:'Koweït',en:'Kuwait',dial:'+965'},
  {iso:'LA',fr:'Laos',en:'Laos',dial:'+856'},
  {iso:'LS',fr:'Lesotho',en:'Lesotho',dial:'+266'},
  {iso:'LV',fr:'Lettonie',en:'Latvia',dial:'+371'},
  {iso:'LB',fr:'Liban',en:'Lebanon',dial:'+961'},
  {iso:'LR',fr:'Libéria',en:'Liberia',dial:'+231'},
  {iso:'LY',fr:'Libye',en:'Libya',dial:'+218'},
  {iso:'LI',fr:'Liechtenstein',en:'Liechtenstein',dial:'+423'},
  {iso:'LT',fr:'Lituanie',en:'Lithuania',dial:'+370'},
  {iso:'LU',fr:'Luxembourg',en:'Luxembourg',dial:'+352'},
  {iso:'MG',fr:'Madagascar',en:'Madagascar',dial:'+261'},
  {iso:'MY',fr:'Malaisie',en:'Malaysia',dial:'+60'},
  {iso:'MW',fr:'Malawi',en:'Malawi',dial:'+265'},
  {iso:'MV',fr:'Maldives',en:'Maldives',dial:'+960'},
  {iso:'ML',fr:'Mali',en:'Mali',dial:'+223'},
  {iso:'MT',fr:'Malte',en:'Malta',dial:'+356'},
  {iso:'MA',fr:'Maroc',en:'Morocco',dial:'+212'},
  {iso:'MH',fr:'Îles Marshall',en:'Marshall Islands',dial:'+692'},
  {iso:'MU',fr:'Maurice',en:'Mauritius',dial:'+230'},
  {iso:'MR',fr:'Mauritanie',en:'Mauritania',dial:'+222'},
  {iso:'MX',fr:'Mexique',en:'Mexico',dial:'+52'},
  {iso:'FM',fr:'Micronésie',en:'Micronesia',dial:'+691'},
  {iso:'MD',fr:'Moldavie',en:'Moldova',dial:'+373'},
  {iso:'MC',fr:'Monaco',en:'Monaco',dial:'+377'},
  {iso:'MN',fr:'Mongolie',en:'Mongolia',dial:'+976'},
  {iso:'ME',fr:'Monténégro',en:'Montenegro',dial:'+382'},
  {iso:'MZ',fr:'Mozambique',en:'Mozambique',dial:'+258'},
  {iso:'MM',fr:'Myanmar',en:'Myanmar',dial:'+95'},
  {iso:'NA',fr:'Namibie',en:'Namibia',dial:'+264'},
  {iso:'NR',fr:'Nauru',en:'Nauru',dial:'+674'},
  {iso:'NP',fr:'Népal',en:'Nepal',dial:'+977'},
  {iso:'NI',fr:'Nicaragua',en:'Nicaragua',dial:'+505'},
  {iso:'NE',fr:'Niger',en:'Niger',dial:'+227'},
  {iso:'NG',fr:'Nigéria',en:'Nigeria',dial:'+234'},
  {iso:'NO',fr:'Norvège',en:'Norway',dial:'+47'},
  {iso:'NZ',fr:'Nouvelle-Zélande',en:'New Zealand',dial:'+64'},
  {iso:'OM',fr:'Oman',en:'Oman',dial:'+968'},
  {iso:'UG',fr:'Ouganda',en:'Uganda',dial:'+256'},
  {iso:'UZ',fr:'Ouzbékistan',en:'Uzbekistan',dial:'+998'},
  {iso:'PK',fr:'Pakistan',en:'Pakistan',dial:'+92'},
  {iso:'PW',fr:'Palaos',en:'Palau',dial:'+680'},
  {iso:'PS',fr:'Palestine',en:'Palestine',dial:'+970'},
  {iso:'PA',fr:'Panama',en:'Panama',dial:'+507'},
  {iso:'PG',fr:'Papouasie-Nvle-Guinée',en:'Papua New Guinea',dial:'+675'},
  {iso:'PY',fr:'Paraguay',en:'Paraguay',dial:'+595'},
  {iso:'NL',fr:'Pays-Bas',en:'Netherlands',dial:'+31'},
  {iso:'PE',fr:'Pérou',en:'Peru',dial:'+51'},
  {iso:'PH',fr:'Philippines',en:'Philippines',dial:'+63'},
  {iso:'PL',fr:'Pologne',en:'Poland',dial:'+48'},
  {iso:'PT',fr:'Portugal',en:'Portugal',dial:'+351'},
  {iso:'QA',fr:'Qatar',en:'Qatar',dial:'+974'},
  {iso:'DO',fr:'Rép. dominicaine',en:'Dominican Republic',dial:'+1809'},
  {iso:'RO',fr:'Roumanie',en:'Romania',dial:'+40'},
  {iso:'GB',fr:'Royaume-Uni',en:'United Kingdom',dial:'+44'},
  {iso:'RU',fr:'Russie',en:'Russia',dial:'+7'},
  {iso:'RW',fr:'Rwanda',en:'Rwanda',dial:'+250'},
  {iso:'KN',fr:'Saint-Kitts-et-Nevis',en:'Saint Kitts and Nevis',dial:'+1869'},
  {iso:'LC',fr:'Sainte-Lucie',en:'Saint Lucia',dial:'+1758'},
  {iso:'VC',fr:'Saint-Vincent',en:'Saint Vincent and the Grenadines',dial:'+1784'},
  {iso:'WS',fr:'Samoa',en:'Samoa',dial:'+685'},
  {iso:'SM',fr:'Saint-Marin',en:'San Marino',dial:'+378'},
  {iso:'ST',fr:'Sao Tomé-et-Principe',en:'Sao Tome and Principe',dial:'+239'},
  {iso:'SN',fr:'Sénégal',en:'Senegal',dial:'+221'},
  {iso:'RS',fr:'Serbie',en:'Serbia',dial:'+381'},
  {iso:'SC',fr:'Seychelles',en:'Seychelles',dial:'+248'},
  {iso:'SL',fr:'Sierra Leone',en:'Sierra Leone',dial:'+232'},
  {iso:'SG',fr:'Singapour',en:'Singapore',dial:'+65'},
  {iso:'SK',fr:'Slovaquie',en:'Slovakia',dial:'+421'},
  {iso:'SI',fr:'Slovénie',en:'Slovenia',dial:'+386'},
  {iso:'SB',fr:'Îles Salomon',en:'Solomon Islands',dial:'+677'},
  {iso:'SO',fr:'Somalie',en:'Somalia',dial:'+252'},
  {iso:'SD',fr:'Soudan',en:'Sudan',dial:'+249'},
  {iso:'SS',fr:'Soudan du Sud',en:'South Sudan',dial:'+211'},
  {iso:'LK',fr:'Sri Lanka',en:'Sri Lanka',dial:'+94'},
  {iso:'SE',fr:'Suède',en:'Sweden',dial:'+46'},
  {iso:'CH',fr:'Suisse',en:'Switzerland',dial:'+41'},
  {iso:'SR',fr:'Suriname',en:'Suriname',dial:'+597'},
  {iso:'SY',fr:'Syrie',en:'Syria',dial:'+963'},
  {iso:'TJ',fr:'Tadjikistan',en:'Tajikistan',dial:'+992'},
  {iso:'TW',fr:'Taïwan',en:'Taiwan',dial:'+886'},
  {iso:'TZ',fr:'Tanzanie',en:'Tanzania',dial:'+255'},
  {iso:'TD',fr:'Tchad',en:'Chad',dial:'+235'},
  {iso:'CZ',fr:'Tchéquie',en:'Czech Republic',dial:'+420'},
  {iso:'TH',fr:'Thaïlande',en:'Thailand',dial:'+66'},
  {iso:'TL',fr:'Timor-Leste',en:'Timor-Leste',dial:'+670'},
  {iso:'TG',fr:'Togo',en:'Togo',dial:'+228'},
  {iso:'TO',fr:'Tonga',en:'Tonga',dial:'+676'},
  {iso:'TT',fr:'Trinité-et-Tobago',en:'Trinidad and Tobago',dial:'+1868'},
  {iso:'TN',fr:'Tunisie',en:'Tunisia',dial:'+216'},
  {iso:'TM',fr:'Turkménistan',en:'Turkmenistan',dial:'+993'},
  {iso:'TR',fr:'Turquie',en:'Turkey',dial:'+90'},
  {iso:'TV',fr:'Tuvalu',en:'Tuvalu',dial:'+688'},
  {iso:'UA',fr:'Ukraine',en:'Ukraine',dial:'+380'},
  {iso:'UY',fr:'Uruguay',en:'Uruguay',dial:'+598'},
  {iso:'VU',fr:'Vanuatu',en:'Vanuatu',dial:'+678'},
  {iso:'VA',fr:'Vatican',en:'Vatican',dial:'+379'},
  {iso:'VE',fr:'Venezuela',en:'Venezuela',dial:'+58'},
  {iso:'VN',fr:'Viêt Nam',en:'Vietnam',dial:'+84'},
  {iso:'YE',fr:'Yémen',en:'Yemen',dial:'+967'},
  {iso:'ZM',fr:'Zambie',en:'Zambia',dial:'+260'},
  {iso:'ZW',fr:'Zimbabwe',en:'Zimbabwe',dial:'+263'},
  {iso:'US',fr:'États-Unis',en:'United States',dial:'+1'},
];

/* ── Flag emoji ──────────────────────────────────── */
function flag(iso){
  if(!iso||iso.length!==2) return '🌐';
  return iso.toUpperCase().split('').map(function(c){return String.fromCodePoint(c.charCodeAt(0)+127397);}).join('');
}

/* ── Tom Select ──────────────────────────────────── */
var countryTS, currentCountry=null;

function initCountrySelect(){
  var opts=COUNTRIES.map(function(c){
    return {value:c.iso,text:flag(c.iso)+' '+c.fr,fr:c.fr,en:c.en,dial:c.dial};
  });
  countryTS=new TomSelect('#pays_select',{
    valueField:'value',labelField:'text',searchField:['fr','en'],
    options:opts,
    render:{
      option:function(d,e){
        return '<div style="display:flex;align-items:center;gap:8px">'+
          '<span style="font-size:18px">'+d.text.split(' ')[0]+'</span>'+
          '<span>'+e(d.fr)+'</span>'+
          '<span style="color:rgba(255,255,255,.35);font-size:11px;margin-left:auto">'+e(d.dial)+'</span></div>';
      },
      item:function(d,e){return '<div>'+d.text.split(' ')[0]+' '+e(d.fr)+'</div>';}
    },
    onChange:function(v){onCountryChange(v);}
  });
}

function onCountryChange(iso){
  if(!iso) return;
  var c=COUNTRIES.find(function(x){return x.iso===iso;});
  if(!c) return;
  currentCountry=c;
  document.getElementById('h_iso').value=iso;
  document.getElementById('h_dial').value=c.dial;
  document.getElementById('h_nat').value=c.fr;
  var b=document.getElementById('dialBadge');
  b.textContent=flag(iso)+' '+c.dial; b.classList.add('loaded');
  resetSel('region_sel','— Chargement des régions... —');
  resetSel('dept_sel','— Choisir une région d\'abord —');
  resetSel('ville_sel','— Choisir un département d\'abord —');
  resetArrond();
  loadRegions(c.en);
}

function loadRegions(countryEn){
  var sel=document.getElementById('region_sel');
  var ld=document.getElementById('regLoader');
  sel.disabled=true; ld.classList.add('show');
  fetch('/api/geo/states/'+encodeURIComponent(countryEn))
    .then(function(r){return r.json();})
    .then(function(data){
      ld.classList.remove('show');
      sel.innerHTML='<option value="">— Sélectionner une région —</option>';
      if(data&&data.length){
        data.forEach(function(s){sel.innerHTML+='<option value="'+xss(s)+'">'+xss(s)+'</option>';});
        sel.disabled=false;
      } else {
        sel.innerHTML='<option value="">— Aucune région —</option>';
        loadCities(currentCountry.en,null);
      }
    }).catch(function(){ld.classList.remove('show');});
}

document.addEventListener('DOMContentLoaded',function(){
  // Région → Département
  document.getElementById('region_sel').addEventListener('change',function(){
    var reg = this.value;
    resetSel('dept_sel','— Choisir une région d\'abord —');
    resetSel('ville_sel','— Choisir un département d\'abord —');
    resetArrond();
    if(reg && currentCountry) loadDepts(currentCountry.en, reg);
  });
  // Département → Arrondissement + Ville
  document.getElementById('dept_sel').addEventListener('change',function(){
    var dept = this.value;
    resetSel('ville_sel','— Choisir un département d\'abord —');
    resetArrond();
    if(dept && currentCountry){
      loadArronds(currentCountry.en, dept);
      loadVilles(currentCountry.en, dept);
    }
  });
  // Sync arrondissement hidden input
  document.getElementById('arrond_sel').addEventListener('change',function(){
    document.getElementById('h_arrond').value=this.value;
  });
  document.getElementById('arrond_txt').addEventListener('input',function(){
    document.getElementById('h_arrond').value=this.value;
  });
});

/* ── Niveau 3 : Département (via state-cities) ── */
function loadDepts(countryEn, region){
  var sel=document.getElementById('dept_sel');
  var ld=document.getElementById('deptLoader');
  sel.disabled=true; sel.innerHTML='<option value="">— Chargement... —</option>';
  ld.classList.add('show');
  fetch('/api/geo/state-cities/'+encodeURIComponent(countryEn)+'/'+encodeURIComponent(region))
    .then(function(r){return r.json();})
    .then(function(data){
      ld.classList.remove('show');
      sel.innerHTML='<option value="">— Sélectionner un département —</option>';
      if(data && data.length){
        data.slice(0,500).forEach(function(v){sel.innerHTML+='<option value="'+xss(v)+'">'+xss(v)+'</option>';});
        sel.disabled=false;
      } else {
        sel.innerHTML='<option value="">— Aucune donnée (saisie libre) —</option>';
        sel.disabled=false;
      }
    }).catch(function(){ld.classList.remove('show'); sel.disabled=false;});
}

/* ── Niveau 4 : Arrondissement (via subcities, fallback text) ── */
function loadArronds(countryEn, dept){
  var sel=document.getElementById('arrond_sel');
  var txt=document.getElementById('arrond_txt');
  var ld=document.getElementById('arrondLoader');
  var hid=document.getElementById('h_arrond');
  sel.style.display='none'; txt.style.display='none';
  ld.style.display='flex'; hid.value='';
  fetch('/api/geo/subcities/'+encodeURIComponent(countryEn)+'/'+encodeURIComponent(dept))
    .then(function(r){return r.json();})
    .then(function(data){
      ld.style.display='none';
      if(data && data.length){
        sel.innerHTML='<option value="">— Sélectionner un arrondissement —</option>';
        data.slice(0,400).forEach(function(v){sel.innerHTML+='<option value="'+xss(v)+'">'+xss(v)+'</option>';});
        sel.style.display=''; txt.style.display='none';
      } else {
        sel.style.display='none'; txt.style.display='';
        txt.placeholder='Saisir l\'arrondissement / commune...';
      }
    }).catch(function(){ld.style.display='none'; txt.style.display='';});
}

/* ── Niveau 5 : Ville (via state-cities du département) ── */
function loadVilles(countryEn, dept){
  var sel=document.getElementById('ville_sel');
  var ld=document.getElementById('villeLoader');
  sel.disabled=true; sel.innerHTML='<option value="">— Chargement... —</option>';
  ld.classList.add('show');
  fetch('/api/geo/state-cities/'+encodeURIComponent(countryEn)+'/'+encodeURIComponent(dept))
    .then(function(r){return r.json();})
    .then(function(data){
      ld.classList.remove('show');
      sel.innerHTML='<option value="">— Sélectionner une ville —</option>';
      if(data && data.length){
        data.slice(0,300).forEach(function(v){sel.innerHTML+='<option value="'+xss(v)+'">'+xss(v)+'</option>';});
        sel.disabled=false;
      } else {
        sel.innerHTML='<option value="">— Saisir manuellement —</option>';
        sel.disabled=false;
      }
    }).catch(function(){ld.classList.remove('show'); sel.disabled=false;});
}

function resetArrond(){
  var sel=document.getElementById('arrond_sel');
  var txt=document.getElementById('arrond_txt');
  sel.style.display='none'; sel.innerHTML='<option value="">—</option>';
  txt.style.display=''; txt.value='';
  document.getElementById('h_arrond').value='';
}

function xss(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
function resetSel(id,ph){var s=document.getElementById(id);if(s){s.innerHTML='<option value="">'+ph+'</option>';s.disabled=true;}}

/* ── Steps ───────────────────────────────────────── */
var curStep=1,TOTAL=5;
function goStep(n){
  document.getElementById('pane'+curStep).classList.remove('active');
  document.querySelectorAll('.step-item').forEach(function(el){
    var s=parseInt(el.dataset.s);
    el.classList.remove('active','done');
    if(s<n) el.classList.add('done');
    if(s===n) el.classList.add('active');
  });
  curStep=n;
  document.getElementById('pane'+n).classList.add('active');
  document.getElementById('progFill').style.width=(n/TOTAL*100)+'%';
  if(n===5) buildRecap();
  window.scrollTo({top:0,behavior:'smooth'});
}
function nextStep(){if(validateStep(curStep)&&curStep<TOTAL) goStep(curStep+1);}

function validateStep(n){
  if(n===1){
    var p=document.getElementById('prenom').value.trim();
    var nm=document.getElementById('nom').value.trim();
    if(!p){markErr('prenom','Le prénom est requis');return false;}
    if(!nm){markErr('nom','Le nom est requis');return false;}
    return true;
  }
  if(n===2){
    if(!countryTS||!countryTS.getValue()){alert('Veuillez sélectionner un pays.');return false;}
    if(!document.getElementById('telephone').value.trim()){markErr('telephone','Le téléphone est requis');return false;}
    return true;
  }
  if(n===4){
    var em=document.getElementById('email').value.trim();
    var pw=document.getElementById('password').value;
    var cf=document.getElementById('pwdConf').value;
    if(!em||!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(em)){document.getElementById('emailErr').textContent='Email invalide';return false;}
    if(pw.length<8){alert('Mot de passe: minimum 8 caractères');return false;}
    if(pw!==cf){document.getElementById('matchErr').textContent='Mots de passe différents';return false;}
    return true;
  }
  return true;
}
function markErr(id,msg){
  var el=document.getElementById(id);
  if(el){el.classList.add('err');el.focus();setTimeout(function(){el.classList.remove('err');},3000);}
}

/* ── Recap ───────────────────────────────────────── */
function buildRecap(){
  var prenom=document.getElementById('prenom').value;
  var nom=document.getElementById('nom').value;
  var roleEl=document.getElementById('role_sel');
  document.getElementById('recapName').textContent=prenom+' '+nom;
  document.getElementById('recapRole').textContent=roleEl.options[roleEl.selectedIndex].text;
  document.getElementById('r_email').textContent=document.getElementById('email').value||'—';
  document.getElementById('r_tel').textContent=(document.getElementById('h_dial').value||'')+' '+document.getElementById('telephone').value;
  document.getElementById('r_pays').textContent=currentCountry?flag(currentCountry.iso)+' '+currentCountry.fr:'—';
  document.getElementById('r_region').textContent=document.getElementById('region_sel').value||'—';
  document.getElementById('r_dept').textContent=document.getElementById('dept_sel').value||'—';
  document.getElementById('r_arrond').textContent=document.getElementById('h_arrond').value||'—';
  document.getElementById('r_prof').textContent=document.querySelector('[name=profession]').value||'—';
  document.getElementById('r_org').textContent=document.querySelector('[name=organisation]').value||'—';
  var prevImg=document.getElementById('photoPrev');
  var rImg=document.getElementById('recapAvatarImg');
  if(prevImg.querySelector('img')){rImg.innerHTML=prevImg.querySelector('img').outerHTML;}
  else{rImg.textContent=(prenom.charAt(0)+nom.charAt(0)).toUpperCase()||'👤';}
}

/* ── Password ────────────────────────────────────── */
function checkPwd(v){
  var s=0;
  if(v.length>=8)s++;if(v.length>=12)s++;
  if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
  var labels=['','Très faible','Faible','Moyen','Fort','Très fort'];
  var cols=['','s1','s2','s3','s4','s5'];
  for(var i=1;i<=5;i++){var b=document.getElementById('pb'+i);b.className='pwd-bar'+(i<=s?' '+cols[s]:'');}
  var h=document.getElementById('pwdHint');
  h.textContent=s>0?labels[s]:'Minimum 8 caractères';
  h.style.color=s>=4?'#39ff14':s>=3?'#ffaa00':'#ff5050';
  checkMatch();
}
function checkMatch(){
  var p1=document.getElementById('password').value;
  var p2=document.getElementById('pwdConf').value;
  var e=document.getElementById('matchErr');
  if(!p2){e.textContent='';return;}
  if(p1===p2){e.style.color='#39ff14';e.textContent='✅ Identiques';}
  else{e.style.color='#ff5050';e.textContent='❌ Ne correspondent pas';}
}
function checkEmail(v){
  var e=document.getElementById('emailErr');
  if(!v){e.textContent='';return;}
  e.textContent=/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v)?'':'Format invalide';
}
function toggleEye(id,btn){
  var i=document.getElementById(id);
  i.type=i.type==='password'?'text':'password';
  btn.textContent=i.type==='text'?'🙈':'👁';
}
function previewPhoto(inp){
  if(!inp.files||!inp.files[0]) return;
  if(inp.files[0].size>2*1024*1024){alert('Max 2 Mo');inp.value='';return;}
  var r=new FileReader();
  r.onload=function(e){
    var p=document.getElementById('photoPrev');
    p.innerHTML='<img src="'+e.target.result+'" style="width:100%;height:100%;object-fit:cover;border-radius:50%">';
  };
  r.readAsDataURL(inp.files[0]);
}

/* ── Init ────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded',function(){
  initCountrySelect();
  @if(old('iso_pays'))
    countryTS.setValue('{{ old("iso_pays") }}');
  @endif
});
</script>
</body>
</html>
