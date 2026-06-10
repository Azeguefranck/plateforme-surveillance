<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription — Plateforme de Surveillance</title>
<link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
<link rel="stylesheet" href="/vendor/tom-select/css/tom-select.css">
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

.reg-header{text-align:center;margin-bottom:28px;animation:fadeDown .6s ease;}
.reg-logo{display:inline-flex;align-items:center;gap:10px;margin-bottom:8px;}
.reg-logo-icon{width:38px;height:38px;border-radius:8px;background:rgba(57,255,20,.08);border:1.5px solid var(--green);
  box-shadow:0 0 14px rgba(57,255,20,.25);display:flex;align-items:center;justify-content:center;font-size:18px;}
.reg-logo-text{font-size:14px;font-weight:700;letter-spacing:4px;color:var(--green);}
.reg-title{font-size:26px;font-weight:900;letter-spacing:.1em;color:#fff;text-transform:uppercase;}
.reg-sub{font-size:12px;color:var(--muted);letter-spacing:.04em;margin-top:4px;}

.reg-card{width:100%;max-width:860px;background:var(--card);border:1px solid var(--bd);border-radius:20px;
  overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,.5);animation:fadeUp .7s ease .1s both;}

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

.reg-body{padding:30px 32px;}
@media(max-width:600px){.reg-body{padding:20px 16px;}}
.step-pane{display:none;animation:fadeIn .35s ease;}
.step-pane.active{display:block;}
.pane-title{font-size:18px;font-weight:700;color:#fff;margin-bottom:4px;letter-spacing:.04em;}
.pane-sub{font-size:12px;color:var(--muted);margin-bottom:22px;}

.fg{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.fg.col1{grid-template-columns:1fr;}
@media(max-width:640px){.fg{grid-template-columns:1fr;}}
.f-full{grid-column:1/-1;}

.fld{display:flex;flex-direction:column;gap:5px;}
.flbl{font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:rgba(57,255,20,.7);font-weight:700;}
.flbl .req{color:#ff5a5a;margin-left:2px;}
.finp{background:rgba(0,0,0,.35);border:1px solid rgba(57,255,20,.15);border-radius:8px;padding:11px 13px;
  color:#fff;font-size:13px;transition:border-color .25s,box-shadow .25s;outline:none;width:100%;}
.finp:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(57,255,20,.08);}
.finp::placeholder{color:rgba(255,255,255,.2);}
.finp[readonly]{color:rgba(255,255,255,.4);cursor:default;}
select.finp option{background:#0c1c34;color:#fff;}
.dob-grid{display:grid;grid-template-columns:1fr 1.6fr 1.3fr;gap:6px;}

.ts-wrapper .ts-control{background:rgba(0,0,0,.35)!important;border:1px solid rgba(57,255,20,.15)!important;
  border-radius:8px!important;color:#fff!important;padding:10px 12px!important;min-height:44px!important;font-size:13px!important;}
.ts-wrapper.focus .ts-control{border-color:var(--green)!important;box-shadow:0 0 0 3px rgba(57,255,20,.08)!important;}
.ts-wrapper .ts-dropdown{background:#0c1c34!important;border:1px solid var(--bd)!important;border-radius:10px!important;
  margin-top:4px!important;box-shadow:0 8px 30px rgba(0,0,0,.6)!important;overflow:hidden;}
.ts-wrapper .ts-dropdown .option{padding:10px 14px!important;color:var(--text)!important;font-size:13px!important;display:flex;align-items:center;gap:8px;}
.ts-wrapper .ts-dropdown .option:hover,.ts-wrapper .ts-dropdown .option.active{background:rgba(57,255,20,.1)!important;color:#fff!important;}
.ts-wrapper .ts-control input{color:#fff!important;background:transparent!important;}

.photo-zone{display:flex;align-items:center;gap:16px;padding:16px;background:rgba(0,0,0,.25);
  border:2px dashed rgba(57,255,20,.2);border-radius:12px;cursor:pointer;transition:border-color .25s;grid-column:1/-1;}
.photo-zone:hover{border-color:var(--green);}
.photo-preview{width:72px;height:72px;border-radius:50%;background:rgba(57,255,20,.07);
  border:2px solid var(--bd);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;font-size:28px;}
.photo-preview img{width:100%;height:100%;object-fit:cover;border-radius:50%;}
.photo-info .photo-title{font-size:13px;color:#fff;margin-bottom:4px;}
.photo-info .photo-sub{font-size:11px;color:var(--muted);}

.dial-row{display:flex;gap:8px;align-items:flex-end;}
.dial-badge{flex-shrink:0;height:44px;min-width:80px;background:rgba(57,255,20,.07);border:1px solid var(--bd);
  border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;
  color:var(--green);letter-spacing:.04em;transition:all .25s;}
.dial-badge.loaded{box-shadow:0 0 10px rgba(57,255,20,.2);}

.loader{display:none;align-items:center;gap:6px;font-size:11px;color:var(--muted);margin-top:4px;}
.loader.show{display:flex;}
.spin{width:12px;height:12px;border:2px solid rgba(57,255,20,.2);border-top-color:var(--green);
  border-radius:50%;animation:spin .8s linear infinite;}
@keyframes spin{to{transform:rotate(360deg)}}

.sec-div{grid-column:1/-1;display:flex;align-items:center;gap:10px;margin:8px 0 2px;}
.sec-div-line{flex:1;height:1px;background:var(--bd);}
.sec-div-text{font-size:9px;letter-spacing:.14em;text-transform:uppercase;color:rgba(57,255,20,.5);}

.pwd-bars{display:flex;gap:3px;margin-top:5px;}
.pwd-bar{flex:1;height:3px;border-radius:2px;background:rgba(255,255,255,.07);transition:background .3s;}
.s1,.s2{background:#ff4444;}.s3{background:#ffaa00;}.s4,.s5{background:#39ff14;}
.pwd-hint{font-size:10px;margin-top:3px;color:var(--muted);}
.eye-wrap{position:relative;}
.eye-wrap .finp{padding-right:40px;}
.eye-btn{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;
  color:rgba(57,255,20,.45);font-size:15px;line-height:1;transition:color .2s;padding:0;}
.eye-btn:hover{color:var(--green);}

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

.flash{padding:12px 16px;border-radius:10px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:10px;}
.flash-err{background:rgba(255,60,60,.1);border:1px solid rgba(255,60,60,.3);color:#ff6060;}

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

.reg-footer{text-align:center;margin-top:22px;font-size:13px;color:var(--muted);animation:fadeUp .6s ease .3s both;}
.reg-footer a{color:var(--cyan);text-decoration:none;}
.reg-footer a:hover{color:#fff;}

.finp.err{border-color:#ff5a5a!important;}
.err-msg{font-size:11px;color:#ff6060;margin-top:3px;}

@keyframes fadeDown{from{opacity:0;transform:translateY(-16px)}to{opacity:1;transform:none}}
@keyframes fadeUp  {from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:none}}
@keyframes fadeIn  {from{opacity:0;transform:scale(.98)}       to{opacity:1;transform:none}}

*{overflow-wrap:break-word;word-break:break-word}
img,video,iframe{max-width:100%;height:auto}
@media(max-width:640px){
body{overflow-x:hidden}
input,select,textarea{max-width:100%!important;width:100%!important}
[class*="card"],[class*="box"],[class*="form"],[class*="step"],[class*="wrap"]{max-width:100%!important}
.steps-row{flex-wrap:wrap!important;gap:6px!important}
}
@media(max-width:400px){
h1,h2,h3{font-size:clamp(14px,4vw,20px)!important}
}
</style>
</head>
<body>

<div class="bg-grid"></div>
<div class="bg-glow"></div>

<div class="reg-page">

  <div class="reg-header">
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
        <div class="flash flash-err"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
      @endif

      <form action="/register-user" method="POST" enctype="multipart/form-data" id="regForm" novalidate>
        @csrf
        <input type="hidden" name="iso_pays"        id="h_iso"      value="CM">
        <input type="hidden" name="indicatif_tel"  id="h_dial"     value="+237">
        <input type="hidden" name="nationalite"    id="h_nat"      value="Camerounaise">
        <input type="hidden" name="pays"           id="h_pays"     value="Cameroun">
        <input type="hidden" name="pays_geoname_id" id="h_pays_gid" value="2233387">
        <input type="hidden" name="pays_nom"        id="h_pays_nom" value="Cameroun">

        <div class="step-pane active" id="pane1">
          <div class="pane-title"><i class="fa-solid fa-user"></i> Identité personnelle</div>
          <div class="pane-sub">Vos informations de base</div>
          <div class="fg">
            <div class="photo-zone" onclick="document.getElementById('photoInput').click()">
              <div class="photo-preview" id="photoPrev"><i class="fa-solid fa-camera"></i></div>
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
              <div class="dob-grid">
                <select class="finp" id="dp-day">
                  <option value="">Jour</option>
                </select>
                <select class="finp" id="dp-month">
                  <option value="">Mois</option>
                  <option value="01">Janvier</option>
                  <option value="02">Février</option>
                  <option value="03">Mars</option>
                  <option value="04">Avril</option>
                  <option value="05">Mai</option>
                  <option value="06">Juin</option>
                  <option value="07">Juillet</option>
                  <option value="08">Août</option>
                  <option value="09">Septembre</option>
                  <option value="10">Octobre</option>
                  <option value="11">Novembre</option>
                  <option value="12">Décembre</option>
                </select>
                <select class="finp" id="dp-year">
                  <option value="">Année</option>
                </select>
              </div>
              <input type="hidden" name="date_naissance" id="dob-hidden" value="{{ old('date_naissance') }}">
            </div>
            <div class="fld">
              <label class="flbl">Lieu de naissance</label>
              <input class="finp" type="text" name="lieu_naissance" placeholder="Ville et pays de naissance" value="{{ old('lieu_naissance') }}">
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

        <div class="step-pane" id="pane2">
          <div class="pane-title"><i class="fa-solid fa-earth-africa"></i> Localisation géographique</div>
          <div class="pane-sub">Votre pays, région et adresse</div>
          <div class="fg">
            <div class="fld f-full">
              <label class="flbl">Pays</label>
              <div style="display:flex;align-items:center;gap:10px;background:rgba(0,0,0,.35);border:1px solid rgba(57,255,20,.25);border-radius:8px;padding:11px 13px;">
                <span style="font-size:20px">🇨🇲</span>
                <span style="color:#fff;font-size:13px;font-weight:600;">Cameroun</span>
                <span style="color:rgba(255,255,255,.35);font-size:11px;margin-left:auto">+237</span>
              </div>
            </div>
            <div class="fld f-full">
              <label class="flbl">Téléphone <span class="req">*</span></label>
              <div class="dial-row">
                <div class="dial-badge loaded" id="dialBadge">🇨🇲 +237</div>
                <input class="finp" type="tel" name="telephone" id="telephone" placeholder="Numéro de téléphone" style="flex:1" value="{{ old('telephone') }}">
              </div>
            </div>
            <div class="fld">
              <label class="flbl"><i class="fa-solid fa-globe"></i> Région / Province / État</label>
              <div class="loader" id="regLoader"><div class="spin"></div> Chargement des régions...</div>
              <select id="select-region" placeholder="— Choisir un pays d'abord —"></select>
              <input class="finp" id="txt-region" type="text" placeholder="Saisir la région..." style="display:none">
              <input type="hidden" name="region"            id="h_region"  value="{{ old('region') }}">
              <input type="hidden" name="region_geoname_id" id="h_reg_gid" value="{{ old('region_geoname_id') }}">
            </div>
            <div class="fld">
              <label class="flbl"><i class="fa-solid fa-building-columns"></i> Département / District / Comté</label>
              <div class="loader" id="deptLoader"><div class="spin"></div> Chargement...</div>
              <select id="select-dept" placeholder="— Choisir une région d'abord —"></select>
              <input class="finp" id="txt-dept" type="text" placeholder="Saisir le département..." style="display:none">
              <input type="hidden" name="departement"     id="h_dept"     value="{{ old('departement') }}">
              <input type="hidden" name="dept_geoname_id" id="h_dept_gid" value="{{ old('dept_geoname_id') }}">
            </div>
            <div class="fld">
              <label class="flbl"><i class="fa-solid fa-house-chimney"></i> Arrondissement / Commune</label>
              <div class="loader" id="arrondLoader"><div class="spin"></div> Chargement...</div>
              <select id="select-arr" placeholder="— Choisir un département d'abord —"></select>
              <input class="finp" id="txt-arr" type="text" placeholder="Saisir l'arrondissement..." style="display:none">
              <input type="hidden" name="arrondissement"  id="h_arrond"   value="{{ old('arrondissement') }}">
              <input type="hidden" name="arr_geoname_id"  id="h_arr_gid"  value="{{ old('arr_geoname_id') }}">
            </div>
            <div class="fld">
              <label class="flbl"><i class="fa-solid fa-city"></i> Ville / Résidence</label>
              <input class="finp" type="text" name="ville_residence" id="h_ville" placeholder="Ex: Yaoundé, Douala, Paris..." value="{{ old('ville_residence') }}">
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

        <div class="step-pane" id="pane3">
          <div class="pane-title"><i class="fa-solid fa-briefcase"></i> Informations professionnelles</div>
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
                <option value="utilisateur"    {{ old('role','utilisateur')=='utilisateur'   ?'selected':'' }}>Utilisateur standard</option>
                <option value="technicien"     {{ old('role')=='technicien'   ?'selected':'' }}>Technicien</option>
                <option value="superviseur"    {{ old('role')=='superviseur'  ?'selected':'' }}>Superviseur</option>
                <option value="administrateur" {{ old('role')=='administrateur'?'selected':'' }}>Administrateur</option>
                <option value="prestataire"    {{ old('role')=='prestataire'  ?'selected':'' }}>Prestataire</option>
                <option value="invite"         {{ old('role')=='invite'       ?'selected':'' }}>Invité (lecture seule)</option>
              </select>
            </div>
          </div>
          <div class="nav-row">
            <button type="button" class="btn btn-prev" onclick="goStep(2)">← Retour</button>
            <button type="button" class="btn btn-next" onclick="nextStep()">Suivant →</button>
          </div>
        </div>

        <div class="step-pane" id="pane4">
          <div class="pane-title"><i class="fa-solid fa-lock"></i> Informations de connexion</div>
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
                <button type="button" class="eye-btn" onclick="toggleEye('password',this)"><i class="fa-solid fa-eye"></i></button>
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
                <button type="button" class="eye-btn" onclick="toggleEye('pwdConf',this)"><i class="fa-solid fa-eye"></i></button>
              </div>
              <div class="err-msg" id="matchErr"></div>
            </div>
          </div>
          <div class="nav-row">
            <button type="button" class="btn btn-prev" onclick="goStep(3)">← Retour</button>
            <button type="button" class="btn btn-next" onclick="nextStep()">Vérifier →</button>
          </div>
        </div>

        <div class="step-pane" id="pane5">
          <div class="pane-title"><i class="fa-solid fa-circle-check"></i> Confirmation</div>
          <div class="pane-sub">Vérifiez vos informations avant de soumettre</div>
          <div class="conf-badge">
            <div class="conf-badge-icon"><i class="fa-solid fa-bell"></i></div>
            <div class="conf-badge-text">
              Votre compte sera soumis à <strong>validation par l'administrateur</strong>.<br>
              Un email de confirmation sera envoyé à l'adresse indiquée.
            </div>
          </div>
          <div class="recap-grid" id="recapGrid">
            <div class="recap-avatar">
              <div class="recap-avatar-img" id="recapAvatarImg"><i class="fa-solid fa-user"></i></div>
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
            <button type="submit" class="btn btn-submit"><i class="fa-solid fa-rocket"></i> Créer mon compte</button>
          </div>
        </div>

      </form>
    </div>
  </div>

  <div class="reg-footer">
    Déjà un compte ? <a href="/login">Se connecter →</a>
  </div>

</div>

<script src="/vendor/tom-select/js/tom-select.complete.min.js"></script>
<script>
var currentCountry = {iso:'CM', fr:'Cameroun', en:'Cameroon', dial:'+237'};

var GEO_LEVELS = {
  region: { sel:'select-region', txt:'txt-region', hid:'h_region',  gid:'h_reg_gid',  loader:'regLoader'    },
  dept:   { sel:'select-dept',   txt:'txt-dept',   hid:'h_dept',    gid:'h_dept_gid', loader:'deptLoader'   },
  arr:    { sel:'select-arr',    txt:'txt-arr',    hid:'h_arrond',  gid:'h_arr_gid',  loader:'arrondLoader' }
};

function resetGeoLevel(level){
  var cfg = GEO_LEVELS[level]; if(!cfg) return;
  var sel = document.getElementById(cfg.sel);
  var txt = document.getElementById(cfg.txt);
  if(sel){ if(sel.tomselect) sel.tomselect.destroy(); sel.innerHTML=''; sel.style.display='none'; }
  if(txt){ txt.style.display='none'; txt.value=''; txt.oninput=null; }
  var h = document.getElementById(cfg.hid); if(h) h.value='';
  var g = cfg.gid ? document.getElementById(cfg.gid) : null; if(g) g.value='';
}

function loadGeoLevel(endpoint, geonameId, level, onSelect){
  var cfg = GEO_LEVELS[level]; if(!cfg) return;
  var sel    = document.getElementById(cfg.sel);
  var txt    = document.getElementById(cfg.txt);
  var loader = document.getElementById(cfg.loader);

  if(sel){ if(sel.tomselect) sel.tomselect.destroy(); sel.innerHTML=''; sel.style.display='none'; }
  if(txt) txt.style.display='none';
  if(loader) loader.classList.add('show');

  fetch('/geo/'+endpoint+'/'+geonameId)
    .then(function(r){ return r.json(); })
    .then(function(data){
      if(loader) loader.classList.remove('show');
      if(data && data.length > 0){
        if(sel){
          sel.style.display = '';
          new TomSelect('#'+cfg.sel, {
            valueField:'id', labelField:'nom', searchField:['nom'],
            options: data,
            placeholder: '— Sélectionner —',
            allowEmptyOption: true,
            create: false,
            onChange: function(val){
              if(!val) return;
              var item = data.find(function(d){ return String(d.id)===String(val); });
              var h = document.getElementById(cfg.hid); if(h) h.value = item ? item.nom : '';
              var g = cfg.gid ? document.getElementById(cfg.gid) : null; if(g) g.value = val;
              if(onSelect) onSelect(val);
            }
          });
        }
      } else {
        if(sel) sel.style.display='none';
        if(txt){
          txt.style.display='';
          txt.disabled = false;
          txt.oninput = function(){
            var h = document.getElementById(cfg.hid); if(h) h.value=this.value;
            if(onSelect && this.value) onSelect('free_'+this.value);
          };
        }
      }
    })
    .catch(function(){
      if(loader) loader.classList.remove('show');
      if(sel) sel.style.display='none';
      if(txt){ txt.style.display=''; txt.disabled=false; }
    });
}

function xss(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}

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

function buildRecap(){
  var prenom=document.getElementById('prenom').value;
  var nom=document.getElementById('nom').value;
  var roleEl=document.getElementById('role_sel');
  document.getElementById('recapName').textContent=prenom+' '+nom;
  document.getElementById('recapRole').textContent=roleEl.options[roleEl.selectedIndex].text;
  document.getElementById('r_email').textContent=document.getElementById('email').value||'—';
  document.getElementById('r_tel').textContent='+237 '+document.getElementById('telephone').value;
  document.getElementById('r_pays').textContent='🇨🇲 Cameroun';
  document.getElementById('r_region').textContent=document.getElementById('h_region').value||'—';
  document.getElementById('r_dept').textContent=document.getElementById('h_dept').value||'—';
  document.getElementById('r_arrond').textContent=document.getElementById('h_arrond').value||'—';
  document.getElementById('r_prof').textContent=document.querySelector('[name=profession]').value||'—';
  document.getElementById('r_org').textContent=document.querySelector('[name=organisation]').value||'—';
  var prevImg=document.getElementById('photoPrev');
  var rImg=document.getElementById('recapAvatarImg');
  if(prevImg.querySelector('img')){rImg.innerHTML=prevImg.querySelector('img').outerHTML;}
  else{rImg.textContent=(prenom.charAt(0)+nom.charAt(0)).toUpperCase()||'';if(!rImg.textContent)rImg.innerHTML='<i class="fa-solid fa-user"></i>';}
}

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
  if(p1===p2){e.style.color='#39ff14';e.innerHTML='<i class="fa-solid fa-circle-check"></i> Identiques';}
  else{e.style.color='#ff5050';e.innerHTML='<i class="fa-solid fa-circle-xmark"></i> Ne correspondent pas';}
}
function checkEmail(v){
  var e=document.getElementById('emailErr');
  if(!v){e.textContent='';return;}
  e.textContent=/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v)?'':'Format invalide';
}
function toggleEye(id,btn){
  var i=document.getElementById(id);
  i.type=i.type==='password'?'text':'password';
  btn.innerHTML=i.type==='text'?'<i class="fa-solid fa-eye-slash"></i>':'<i class="fa-solid fa-eye"></i>';
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

(function(){
  function daysInMonth(m,y){return m?new Date(y||2000,parseInt(m),0).getDate():31;}
  function fillDays(n,cur){
    var s=document.getElementById('dp-day');if(!s)return;
    s.innerHTML='<option value="">Jour</option>';
    for(var d=1;d<=n;d++){var o=document.createElement('option');o.value=String(d).padStart(2,'0');o.textContent=d;s.appendChild(o);}
    if(cur)s.value=cur;
  }
  function syncHidden(){
    var d=document.getElementById('dp-day'),m=document.getElementById('dp-month'),y=document.getElementById('dp-year'),h=document.getElementById('dob-hidden');
    if(!d||!m||!y||!h)return;
    h.value=(y.value&&m.value&&d.value)?y.value+'-'+m.value+'-'+d.value:'';
  }
  function onMonthYear(){
    var d=document.getElementById('dp-day'),m=document.getElementById('dp-month'),y=document.getElementById('dp-year');
    if(!d||!m||!y)return;
    fillDays(daysInMonth(m.value,y.value),d.value);
    syncHidden();
  }
  var yearSel=document.getElementById('dp-year');
  if(yearSel){
    var now=new Date().getFullYear();
    for(var yr=now;yr>=now-120;yr--){var o=document.createElement('option');o.value=yr;o.textContent=yr;yearSel.appendChild(o);}
  }
  fillDays(31,'');
  var ms=document.getElementById('dp-month'),ys=document.getElementById('dp-year'),ds=document.getElementById('dp-day');
  if(ms)ms.onchange=onMonthYear;
  if(ys)ys.onchange=onMonthYear;
  if(ds)ds.onchange=syncHidden;
  var old=document.getElementById('dob-hidden');
  if(old&&old.value&&/^\d{4}-\d{2}-\d{2}$/.test(old.value)){
    var pts=old.value.split('-');
    if(ys)ys.value=pts[0];
    if(ms)ms.value=pts[1];
    fillDays(daysInMonth(pts[1],pts[0]),pts[2]);
  }
})();

document.addEventListener('DOMContentLoaded', function(){
  loadGeoLevel('regions', 2233387, 'region', function(regGid){
    ['dept','arr'].forEach(resetGeoLevel);
    loadGeoLevel('departements', regGid, 'dept', function(deptGid){
      resetGeoLevel('arr');
      loadGeoLevel('arrondissements', deptGid, 'arr', null);
    });
  });
});
</script>
</body>
</html>
