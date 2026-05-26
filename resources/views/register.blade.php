<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription — Plateforme Surveillance</title>

<link rel="stylesheet" href="/css/noselect.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">

<style>
/* ── Reset & base ───────────────────────────────────────── */
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;}
:root{
  --bg:#050816;--card:#101935;--card2:#0b1225;
  --border:#1f2d5e;--vert:#39ff14;--amber:#f59e0b;
  --rouge:#ef4444;--bleu:#33b5ff;--text:#d1d5db;--muted:#6b7280;
}
body{background:var(--bg);color:white;min-height:100vh;padding-bottom:40px;}

/* ── Navbar ─────────────────────────────────────────────── */
.navbar{
  display:flex;justify-content:space-between;align-items:center;
  padding:16px 40px;background:rgba(8,17,38,.97);
  border-bottom:1px solid var(--border);position:sticky;top:0;z-index:200;
  flex-wrap:wrap;gap:10px;
}
.logo{font-size:20px;font-weight:bold;color:var(--vert);letter-spacing:2px;text-decoration:none;}
.nav-btns{display:flex;gap:10px;}
.btn-nav{
  padding:9px 22px;border-radius:9px;font-size:14px;font-weight:bold;
  text-decoration:none;transition:.25s;border:2px solid var(--vert);
}
.btn-nav.outline{color:var(--vert);background:transparent;}
.btn-nav.outline:hover{background:var(--vert);color:var(--bg);}
.btn-nav.solid{background:var(--vert);color:var(--bg);}
.btn-nav.solid:hover{background:#25cc0e;}

/* ── Carte formulaire ───────────────────────────────────── */
.form-wrap{max-width:920px;margin:30px auto;padding:0 16px;}
.card{background:var(--card);border-radius:20px;padding:44px;border:1px solid var(--border);}
.card-title{font-size:26px;font-weight:bold;color:var(--vert);text-align:center;margin-bottom:6px;}
.card-sub{color:var(--muted);text-align:center;font-size:14px;margin-bottom:32px;}

/* ── Sections ───────────────────────────────────────────── */
.section{
  border-top:1px solid var(--border);
  margin-top:28px;padding-top:20px;
}
.section-head{
  display:flex;align-items:center;gap:10px;
  font-size:12px;font-weight:bold;letter-spacing:2px;
  text-transform:uppercase;color:var(--vert);margin-bottom:18px;
}
.section-head i{font-size:14px;}

/* ── Grilles ────────────────────────────────────────────── */
.g2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.g3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
.full{grid-column:1/-1;}

/* ── Champ ──────────────────────────────────────────────── */
.field{display:flex;flex-direction:column;gap:6px;}
.field label{
  display:flex;align-items:center;gap:7px;
  font-size:12px;font-weight:bold;color:var(--muted);
  text-transform:uppercase;letter-spacing:.8px;
}
.field label i{color:var(--vert);font-size:12px;width:14px;text-align:center;}
.opt{font-weight:normal;color:#4b5563;font-size:10px;margin-left:3px;}

input,select,textarea{
  background:var(--card2);border:1.5px solid var(--border);
  border-radius:9px;padding:11px 14px;color:white;font-size:14px;
  width:100%;outline:none;transition:border-color .2s;
}
input:focus,select:focus,textarea:focus{border-color:var(--vert);}
input::placeholder,textarea::placeholder{color:#4b5563;}
select option{background:var(--card2);color:white;}
textarea{resize:vertical;min-height:80px;}

/* ── Tom Select ─────────────────────────────────────────── */
.ts-wrapper.single .ts-control{
  background:var(--card2)!important;border:1.5px solid var(--border)!important;
  border-radius:9px!important;color:white!important;
  padding:11px 14px!important;min-height:44px!important;cursor:text!important;
}
.ts-wrapper.single.focus .ts-control{border-color:var(--vert)!important;box-shadow:none!important;}
.ts-dropdown{background:var(--card2)!important;border:1.5px solid var(--border)!important;border-radius:9px!important;}
.ts-dropdown .option{color:white!important;padding:10px 14px!important;}
.ts-dropdown .option:hover,.ts-dropdown .option.active{background:var(--border)!important;}
.ts-dropdown .option.selected{background:#0f3020!important;}
.ts-control input{background:transparent!important;color:white!important;border:none!important;padding:0!important;}
.ts-control .placeholder{color:#4b5563!important;}
.ts-control .item{color:white!important;}

/* ── intl-tel-input ─────────────────────────────────────── */
.iti{width:100%;}
.iti__tel-input{
  background:var(--card2)!important;border:1.5px solid var(--border)!important;
  border-radius:9px!important;color:white!important;
  padding:11px 14px 11px 62px!important;width:100%!important;
}
.iti__tel-input:focus{border-color:var(--vert)!important;outline:none;}
.iti__selected-flag{background:transparent!important;padding-left:10px!important;}
.iti__country-list{background:var(--card2)!important;border:1.5px solid var(--border)!important;color:white!important;}
.iti__country:hover,.iti__country.iti__highlight{background:var(--border)!important;}
.iti__search-input{background:var(--card)!important;color:white!important;border-color:var(--border)!important;}
.iti__divider{border-color:var(--border)!important;}
.iti__dial-code{color:var(--muted)!important;}

/* ── Photo ──────────────────────────────────────────────── */
.photo-zone{
  display:flex;flex-direction:column;align-items:center;gap:12px;margin-bottom:6px;
}
#photo-preview{
  width:110px;height:110px;border-radius:50%;object-fit:cover;
  border:3px solid var(--vert);cursor:pointer;background:var(--card2);
  transition:box-shadow .3s;
}
#photo-preview:hover{box-shadow:0 0 20px rgba(57,255,20,.4);}
.photo-btn{
  display:inline-flex;align-items:center;gap:7px;cursor:pointer;
  color:var(--vert);font-size:13px;font-weight:bold;
  background:rgba(57,255,20,.08);border:1px solid rgba(57,255,20,.3);
  border-radius:8px;padding:7px 16px;transition:.2s;
}
.photo-btn:hover{background:rgba(57,255,20,.15);}
#photo-input{display:none;}

/* ── Indicateur force mot de passe ──────────────────────── */
.strength-bar{height:4px;background:var(--border);border-radius:2px;margin-top:6px;overflow:hidden;}
.strength-fill{height:100%;width:0;border-radius:2px;transition:width .3s,background .3s;}
.strength-label{font-size:11px;margin-top:4px;color:var(--muted);}

/* ── Spinner de chargement ──────────────────────────────── */
.geo-row{display:flex;align-items:center;gap:8px;width:100%;}
.geo-row>*:first-child{flex:1;min-width:0;}
.spinner{
  display:none;width:20px;height:20px;flex-shrink:0;
  border:2px solid var(--border);border-top-color:var(--vert);
  border-radius:50%;animation:spin .7s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg);}}

/* État champ géo : select vs text */
.geo-select{display:none;}
.geo-text{display:block;}
.geo-select.active,.geo-text.active{display:block;}
.geo-select:not(.active){display:none!important;}
.geo-text:not(.active){display:none!important;}

/* ── Alertes ────────────────────────────────────────────── */
.alert{padding:13px 16px;border-radius:10px;margin-bottom:20px;font-size:14px;}
.alert-error{background:#450a0a;border:1px solid var(--rouge);color:#fca5a5;}
.alert-success{background:#052e16;border:1px solid #22c55e;color:#86efac;}

/* ── Bouton soumettre ────────────────────────────────────── */
.btn-submit{
  width:100%;padding:16px;background:var(--vert);color:var(--bg);
  border:none;border-radius:11px;font-size:16px;font-weight:bold;
  cursor:pointer;margin-top:28px;transition:.25s;
  display:flex;align-items:center;justify-content:center;gap:10px;
}
.btn-submit:hover{background:#25cc0e;box-shadow:0 0 20px rgba(57,255,20,.3);}

/* ── Responsive ─────────────────────────────────────────── */
@media(max-width:650px){
  .navbar{padding:14px 20px;}
  .card{padding:24px 16px;}
  .g2,.g3{grid-template-columns:1fr;}
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <a class="logo" href="/accueil">SURVEILLANCE</a>
  <div class="nav-btns">
    <a href="/accueil" class="btn-nav outline"><i class="fa-solid fa-house" style="margin-right:6px;"></i>Accueil</a>
    <a href="/login"   class="btn-nav solid"><i class="fa-solid fa-right-to-bracket" style="margin-right:6px;"></i>Connexion</a>
  </div>
</nav>

<div class="form-wrap">
<div class="card">

  <div class="card-title"><i class="fa-solid fa-user-plus" style="margin-right:10px;"></i>Créer un compte</div>
  <div class="card-sub">Remplissez le formulaire — accès activé après validation administrateur</div>

  @if($errors->any())
  <div class="alert alert-error">
    <i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i><strong>Erreurs :</strong><br>
    @foreach($errors->all() as $e)&nbsp;&nbsp;• {{ $e }}<br>@endforeach
  </div>
  @endif

  @if(session('error'))
  <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>{{ session('error') }}</div>
  @endif

  <form method="POST" action="/register-user" enctype="multipart/form-data" id="reg-form">
  @csrf

  {{-- ── PHOTO ────────────────────────────────────────── --}}
  <div class="section" style="border:none;margin-top:0;padding-top:0;">
    <div class="section-head"><i class="fa-solid fa-camera"></i>Photo de profil <span class="opt">(optionnel)</span></div>
    <div class="photo-zone">
      <img id="photo-preview"
        src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 110 110'%3E%3Ccircle cx='55' cy='40' r='22' fill='%231f2d5e'/%3E%3Cellipse cx='55' cy='90' rx='35' ry='25' fill='%231f2d5e'/%3E%3C/svg%3E"
        alt="Photo" onclick="document.getElementById('photo-input').click()">
      <label class="photo-btn" for="photo-input">
        <i class="fa-solid fa-upload"></i> Choisir une photo
      </label>
      <input type="file" id="photo-input" name="photo_profil" accept="image/*">
      <small style="color:var(--muted);font-size:11px;">JPG, PNG, WEBP — max 3 Mo</small>
    </div>
  </div>

  {{-- ── IDENTITÉ ──────────────────────────────────────── --}}
  <div class="section">
    <div class="section-head"><i class="fa-solid fa-id-card"></i>Informations personnelles</div>
    <div class="g2">

      <div class="field">
        <label><i class="fa-solid fa-user"></i>Nom <span style="color:var(--rouge)">*</span></label>
        <input type="text" name="nom" value="{{ old('nom') }}" required placeholder="Ex: DUPONT">
      </div>

      <div class="field">
        <label><i class="fa-solid fa-user"></i>Prénom <span style="color:var(--rouge)">*</span></label>
        <input type="text" name="prenom" value="{{ old('prenom') }}" required placeholder="Ex: Jean">
      </div>

      <div class="field">
        <label><i class="fa-solid fa-venus-mars"></i>Sexe <span style="color:var(--rouge)">*</span></label>
        <select name="sexe" required>
          <option value="">— Sélectionner —</option>
          <option value="homme"  {{ old('sexe')=='homme'  ?'selected':'' }}>Homme</option>
          <option value="femme"  {{ old('sexe')=='femme'  ?'selected':'' }}>Femme</option>
          <option value="autre"  {{ old('sexe')=='autre'  ?'selected':'' }}>Autre / Non précisé</option>
        </select>
      </div>

      <div class="field">
        <label><i class="fa-solid fa-cake-candles"></i>Date de naissance <span style="color:var(--rouge)">*</span></label>
        <input type="date" name="date_naissance" value="{{ old('date_naissance') }}" required
               max="{{ date('Y-m-d', strtotime('-16 years')) }}">
      </div>

      <div class="field">
        <label><i class="fa-solid fa-ring"></i>Situation matrimoniale <span class="opt">(optionnel)</span></label>
        <select name="statut_matrimonial">
          <option value="">— Sélectionner —</option>
          <option value="celibataire" {{ old('statut_matrimonial')=='celibataire'?'selected':'' }}>Célibataire</option>
          <option value="marie"       {{ old('statut_matrimonial')=='marie'      ?'selected':'' }}>Marié(e)</option>
          <option value="divorce"     {{ old('statut_matrimonial')=='divorce'    ?'selected':'' }}>Divorcé(e)</option>
          <option value="veuf"        {{ old('statut_matrimonial')=='veuf'       ?'selected':'' }}>Veuf / Veuve</option>
        </select>
      </div>

      <div class="field">
        <label><i class="fa-solid fa-flag"></i>Nationalité <span class="opt">(auto)</span></label>
        <input type="text" name="nationalite" id="field-nationalite"
               value="{{ old('nationalite') }}" placeholder="Auto-rempli selon le pays" readonly
               style="cursor:default;background:#080f1e;">
      </div>

    </div>
  </div>

  {{-- ── CONTACT ───────────────────────────────────────── --}}
  <div class="section">
    <div class="section-head"><i class="fa-solid fa-address-book"></i>Contact</div>
    <div class="g2">

      <div class="field">
        <label><i class="fa-solid fa-envelope"></i>Adresse email <span style="color:var(--rouge)">*</span></label>
        <input type="email" name="email" value="{{ old('email') }}" required placeholder="exemple@email.com" autocomplete="email">
      </div>

      <div class="field">
        <label><i class="fa-solid fa-phone"></i>Téléphone <span style="color:var(--rouge)">*</span></label>
        <input type="tel" id="phone-input" name="telephone" value="{{ old('telephone') }}"
               required placeholder="6XX XXX XXX">
        <input type="hidden" id="phone-full" name="telephone_international">
      </div>

    </div>
  </div>

  {{-- ── LOCALISATION ──────────────────────────────────── --}}
  <div class="section">
    <div class="section-head"><i class="fa-solid fa-location-dot"></i>Localisation géographique</div>

    {{-- Pays --}}
    <div class="field" style="margin-bottom:16px;">
      <label><i class="fa-solid fa-earth-africa"></i>Pays <span style="color:var(--rouge)">*</span></label>
      <div id="country-loading" style="color:var(--muted);font-size:13px;padding:8px 0;">
        <i class="fa-solid fa-spinner fa-spin" style="margin-right:6px;color:var(--vert);"></i>Chargement des pays…
      </div>
      <select id="country-select" name="pays" required style="display:none;">
        <option value="">🌍  Rechercher un pays…</option>
      </select>
      <input type="hidden" id="field-code-pays" name="code_pays" value="{{ old('code_pays') }}">
    </div>

    <div class="g2">

      {{-- Région / Province / État --}}
      <div class="field">
        <label><i class="fa-solid fa-map"></i><span id="lbl-region">Région / Province / État</span></label>
        <div class="geo-row">
          <select id="sel-region" name="etat" class="geo-select">
            <option value="">— Sélectionner un pays d'abord —</option>
          </select>
          <input type="text" id="txt-region" name="etat_libre" class="geo-text active"
                 value="{{ old('etat_libre') }}" placeholder="Région / Province / État">
          <div class="spinner" id="spin-region"></div>
        </div>
      </div>

      {{-- Département --}}
      <div class="field">
        <label><i class="fa-solid fa-map-pin"></i><span id="lbl-dept">Département</span></label>
        <div class="geo-row">
          <select id="sel-dept" name="departement" class="geo-select">
            <option value="">— Sélectionner une région d'abord —</option>
          </select>
          <input type="text" id="txt-dept" name="departement_libre" class="geo-text active"
                 value="{{ old('departement_libre') }}" placeholder="Département / District">
          <div class="spinner" id="spin-dept"></div>
        </div>
      </div>

      {{-- Arrondissement / Commune --}}
      <div class="field">
        <label><i class="fa-solid fa-city"></i><span id="lbl-arro">Arrondissement / Commune</span></label>
        <div class="geo-row">
          <select id="sel-arro" name="arrondissement" class="geo-select">
            <option value="">— Sélectionner un département d'abord —</option>
          </select>
          <input type="text" id="txt-arro" name="arrondissement_libre" class="geo-text active"
                 value="{{ old('arrondissement_libre') }}" placeholder="Arrondissement / Commune">
          <div class="spinner" id="spin-arro"></div>
        </div>
      </div>

      {{-- Ville --}}
      <div class="field">
        <label><i class="fa-solid fa-building"></i>Ville</label>
        <div class="geo-row">
          <select id="sel-ville" name="ville" class="geo-select">
            <option value="">— Sélectionner d'abord une région —</option>
          </select>
          <input type="text" id="txt-ville" name="ville_libre" class="geo-text active"
                 value="{{ old('ville_libre') }}" placeholder="Ville">
          <div class="spinner" id="spin-ville"></div>
        </div>
      </div>

      {{-- Quartier --}}
      <div class="field">
        <label><i class="fa-solid fa-house-flag"></i>Quartier <span class="opt">(optionnel)</span></label>
        <input type="text" name="quartier" value="{{ old('quartier') }}" placeholder="Quartier / Secteur">
      </div>

      {{-- Adresse --}}
      <div class="field">
        <label><i class="fa-solid fa-road"></i>Adresse complète <span class="opt">(optionnel)</span></label>
        <input type="text" name="adresse" value="{{ old('adresse') }}" placeholder="Rue, numéro, boîte postale…">
      </div>

    </div>
  </div>

  {{-- ── PROFESSIONNEL ─────────────────────────────────── --}}
  <div class="section">
    <div class="section-head"><i class="fa-solid fa-briefcase"></i>Informations professionnelles</div>
    <div class="g3">

      <div class="field">
        <label><i class="fa-solid fa-hammer"></i>Profession <span style="color:var(--rouge)">*</span></label>
        <input type="text" name="profession" value="{{ old('profession') }}" required placeholder="Ex: Ingénieur réseaux">
      </div>

      <div class="field">
        <label><i class="fa-solid fa-building-columns"></i>Organisation <span class="opt">(optionnel)</span></label>
        <input type="text" name="organisation" value="{{ old('organisation') }}" placeholder="Entreprise / Université">
      </div>

      <div class="field">
        <label><i class="fa-solid fa-user-shield"></i>Rôle demandé</label>
        <select name="role">
          <option value="utilisateur" {{ old('role','utilisateur')=='utilisateur'?'selected':'' }}>Utilisateur</option>
          <option value="technicien"  {{ old('role')=='technicien'                ?'selected':'' }}>Technicien</option>
          <option value="superviseur" {{ old('role')=='superviseur'               ?'selected':'' }}>Superviseur</option>
        </select>
      </div>

    </div>
  </div>

  {{-- ── SÉCURITÉ ──────────────────────────────────────── --}}
  <div class="section">
    <div class="section-head"><i class="fa-solid fa-shield-halved"></i>Sécurité</div>
    <div class="g2">

      <div class="field">
        <label><i class="fa-solid fa-lock"></i>Mot de passe <span style="color:var(--rouge)">*</span></label>
        <input type="password" name="password" id="password-input" required
               placeholder="Minimum 8 caractères" minlength="8" autocomplete="new-password">
        <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
        <div class="strength-label" id="strength-label">Saisissez un mot de passe</div>
      </div>

      <div class="field">
        <label><i class="fa-solid fa-lock"></i>Confirmer le mot de passe <span style="color:var(--rouge)">*</span></label>
        <input type="password" name="password_confirmation" id="confirm-input" required
               placeholder="Répétez le mot de passe" minlength="8" autocomplete="new-password">
        <div class="strength-label" id="confirm-label">&nbsp;</div>
      </div>

    </div>
  </div>

  <button type="submit" class="btn-submit">
    <i class="fa-solid fa-paper-plane"></i>
    Envoyer la demande d'inscription
  </button>

  </form>
</div>
</div>

<!-- ─── SCRIPTS ──────────────────────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

<script>
// ════════════════════════════════════════════════════════
//  DONNÉES CAMEROUN COMPLÈTES (Régions → Départements → Villes)
// ════════════════════════════════════════════════════════

const CM_DATA = {
  "Adamaoua": {
    lbl: "Région de l'Adamaoua",
    depts: {
      "Djerem":      ["Tibati","Ngaoundal","Mbitom"],
      "Faro et Déo": ["Tignère","Mayo-Baléo","Galim-Tignère"],
      "Mayo-Banyo":  ["Banyo","Gashiga","Kontcha","Mayo-Darlé","Tignère"],
      "Mbéré":       ["Meiganga","Djohong","Ngaoui","Ngan-Ha"],
      "Vina":        ["Ngaoundéré","Martap","Mbé","Belel","Nyambaka"]
    }
  },
  "Centre": {
    lbl: "Région du Centre",
    depts: {
      "Haute-Sanaga": ["Nanga Eboko","Minta","Nsem","Lembe-Yezoum"],
      "Lékié":        ["Monatélé","Sa'a","Obala","Evodoula","Elig-Mfomo","Bot-Makak"],
      "Mbam-et-Inoubou":["Bafia","Bokito","Kiiki","Kon-Yambetta","Makénéné","Nitoukou","Ombessa"],
      "Mbam-et-Kim":  ["Ntui","Mbangassina","Ngambe-Tikar","Ngoro"],
      "Méfou-et-Afamba":["Mfou","Nkolafamba","Awae","Esse","Mbankomo","Ngoumou","Soa"],
      "Méfou-et-Akono":["Mbalmayo","Akono","Dzeng","Ngomedzap","Nkolmetet"],
      "Mfoundi":      ["Yaoundé I","Yaoundé II","Yaoundé III","Yaoundé IV","Yaoundé V","Yaoundé VI","Yaoundé VII"],
      "Nyong-et-Kellé":["Eseka","Makak","Bondjock","Dibang","Ngog-Mapubi","Messondo"],
      "Nyong-et-Mfoumou":["Akonolinga","Endom","Ayos","Mengueme","Nyakokombo"],
      "Nyong-et-So'o":["Mbalmayo","Ngomedzap","Bikok","Dzeng","Mengueme"]
    }
  },
  "Est": {
    lbl: "Région de l'Est",
    depts: {
      "Boumba-et-Ngoko":["Moloundou","Salapoumbé","Gari Gombo","Yokadouma"],
      "Haut-Nyong":["Abong-Mbang","Lomié","Doumaintang","Mboma","Ngoura","Nguelemendouka","Somalomo"],
      "Kadey":["Batouri","Kette","Mbang","Ndélélé","Ndelele","Ouli"],
      "Lom-et-Djérem":["Bertoua","Doumé","Batouri","Bélabo","Mandjou","Ngoura"]
    }
  },
  "Extrême-Nord": {
    lbl: "Région de l'Extrême-Nord",
    depts: {
      "Diamaré":["Maroua","Kaélé","Bogo","Dargala","Gazawa","Meri","Mindif","Moutouroua","Ndoukoula","Petté"],
      "Logone-et-Chari":["Kousseri","Blangoua","Darak","Fotokol","Goulfey","Makary","Waza"],
      "Mayo-Danay":["Yagoua","Kar-Hay","Kalfou","Maga","Tchatibali","Vélé","Wina"],
      "Mayo-Kani":["Kaélé","Guidiguis","Mindif","Moulvoudaye","Moutourwa"],
      "Mayo-Sava":["Mora","Kolofata","Tokombéré"],
      "Mayo-Tsanaga":["Mokolo","Bourha","Hina","Koza","Mozogo","Roua","Touloum"]
    }
  },
  "Littoral": {
    lbl: "Région du Littoral",
    depts: {
      "Moungo":["Nkongsamba","Loum","Mbanga","Baré-Bakem","Dibombari","Ébone","Manjo","Melong","Njombe-Penja"],
      "Nkam":["Yabassi","Bafang","Ndom","Ndoumbé","Nékongsamba","Yingui"],
      "Sanaga-Maritime":["Edea","Dibamba","Dizangue","Mouanko","Ngwei","Nyanon","Pouma"],
      "Wouri":["Douala I","Douala II","Douala III","Douala IV","Douala V"]
    }
  },
  "Nord": {
    lbl: "Région du Nord",
    depts: {
      "Bénoué":["Garoua","Demsa","Lagdo","Mayo-Hourna","Ngong","Pitoa","Rey-Bouba","Tchéboa","Touroua"],
      "Faro":["Poli","Beka"],
      "Mayo-Louti":["Guider","Figuil","Mayo-Oulo"],
      "Mayo-Rey":["Tcholliré","Madingring","Mayo-Galké","Rey-Bouba","Touboro"]
    }
  },
  "Nord-Ouest": {
    lbl: "Région du Nord-Ouest",
    depts: {
      "Boyo":["Fundong","Belo","Fonfuka","Ndu","Njinikom"],
      "Bui":["Kumbo","Jakiri","Mbven","Nkambe","Noni","Oku"],
      "Donga-Mantung":["Nkambe","Ako","Misaje","Ndu","Nwa","Nwa"],
      "Menchum":["Wum","Furu-Awa","Befang","Esu","Fungom","Zhoa"],
      "Mezam":["Bamenda","Santa","Bafut","Bali","Tubah","Tubah"],
      "Momo":["Mbengwi","Batibo","Njikwa","Widikum"],
      "Ngo-Ketunjia":["Ndop","Babessi","Balikumbat"]
    }
  },
  "Ouest": {
    lbl: "Région de l'Ouest",
    depts: {
      "Bamboutos":["Mbouda","Batcham","Babadjou","Galim","Penka-Michel"],
      "Haut-Nkam":["Bafang","Bakou","Bana","Bangangté","Bassamba","Bazou","Kekem"],
      "Hauts-Plateaux":["Baham","Bamendjou","Bangou","Bansoa"],
      "Koung-Khi":["Bafoussam","Bamenda","Bayangam","Kékem"],
      "Menoua":["Dschang","Fokoue","Fongo-Tongo","Nkong-Ni","Penka-Michel","Santchou"],
      "Mifi":["Bafoussam","Balessing","Bamougoum","Batoufam"],
      "Ndé":["Bangangté","Bazou","Batchingou","Dschang","Tonga"],
      "Noun":["Foumban","Foumbot","Bangourain","Koutaba","Malantouen","Massangam","Njimom"]
    }
  },
  "Sud": {
    lbl: "Région du Sud",
    depts: {
      "Dja-et-Lobo":["Sangmélima","Bengbis","Djoum","Meyomessala","Meyomessi","Mintom","Oveng"],
      "Mvila":["Ebolowa","Ambam","Biwong-Bané","Biwong-Mbaï","Efoulan","Kinkala","Ma'an","Ngoulemakong","Olamze"],
      "Océan":["Kribi","Campo","Grand Batanga","Lokoundjé","Lolodorf","Mvengue","Niété"],
      "Vallée-du-Ntem":["Ambam","Ma'an","Kye-Ossi","Mengong"]
    }
  },
  "Sud-Ouest": {
    lbl: "Région du Sud-Ouest",
    depts: {
      "Fako":["Buea","Limbe","Muyuka","Tiko","Idenau","Muea"],
      "Koupé-Manengouba":["Bangem","Nguti","Tombel"],
      "Lebialem":["Fontem","Alou","Wabane"],
      "Manyu":["Mamfe","Akwaya","Eyumojock","Tali"],
      "Meme":["Kumba","Konye","Mbonge","Mundemba"],
      "Ndian":["Mundemba","Isangele","Ekondo-Titi","Idabato","Kombo-Abedimo","Kombo-Itindi"]
    }
  }
};

// Correspondances noms API ↔ clés CM_DATA
const CM_API_NAME = "Cameroon";
const CM_ALIASES  = ["Cameroon","Cameroun","CAMEROUN","CAMEROON"];

// ════════════════════════════════════════════════════════
//  UTILITAIRES
// ════════════════════════════════════════════════════════

function flagEmoji(iso2) {
  if (!iso2 || iso2.length !== 2) return '🌍';
  try { return String.fromCodePoint(...[...iso2.toUpperCase()].map(c => 0x1F1E0 + c.charCodeAt(0) - 65)); }
  catch(e) { return '🌍'; }
}

function showSpin(id, v) {
  const el = document.getElementById(id);
  if (el) el.style.display = v ? 'inline-block' : 'none';
}

function switchGeo(selectId, textId, useSelect) {
  const sel = document.getElementById(selectId);
  const txt = document.getElementById(textId);
  if (!sel || !txt) return;
  if (useSelect) {
    sel.classList.add('active');   txt.classList.remove('active');
    sel.style.display = 'block';   txt.style.display = 'none';
  } else {
    txt.classList.add('active');   sel.classList.remove('active');
    txt.style.display = 'block';   sel.style.display = 'none';
  }
}

function fillSelect(selectId, items, placeholder) {
  const sel = document.getElementById(selectId);
  if (!sel) return;
  sel.innerHTML = `<option value="">${placeholder}</option>`;
  items.forEach(item => {
    const opt = document.createElement('option');
    opt.value = opt.textContent = item;
    sel.appendChild(opt);
  });
}

function resetField(selectId, textId, placeholder, defaultPlaceholder) {
  switchGeo(selectId, textId, false);
  const txt = document.getElementById(textId);
  if (txt) { txt.value = ''; txt.placeholder = placeholder || 'Entrez une valeur'; }
  const sel = document.getElementById(selectId);
  if (sel) sel.innerHTML = `<option value="">${defaultPlaceholder || '—'}</option>`;
}

// ════════════════════════════════════════════════════════
//  CHARGEMENT DES PAYS (restcountries.com + cache)
// ════════════════════════════════════════════════════════

let countriesData = [];
let tsCountry = null;
let currentISO2 = '';
let isCameroon  = false;

async function loadCountries() {
  const CACHE_KEY = 'surv_countries_v4';
  const CACHE_TTL = 86400000;

  try {
    const raw = localStorage.getItem(CACHE_KEY);
    if (raw) {
      const p = JSON.parse(raw);
      if (Date.now() - p.ts < CACHE_TTL) countriesData = p.data;
    }
  } catch(e) {}

  if (!countriesData.length) {
    try {
      const res = await fetch('https://restcountries.com/v3.1/all?fields=name,cca2,idd,translations');
      if (!res.ok) throw new Error();
      const raw = await res.json();
      countriesData = raw.map(c => {
        const root = c.idd?.root || '';
        const suf  = c.idd?.suffixes;
        const code = suf && suf.length === 1 ? root + suf[0] : root;
        return {
          iso2: c.cca2,
          name: c.name?.common || c.cca2,
          nameFr: c.translations?.fra?.common || c.name?.common || c.cca2,
          phone: code,
        };
      }).sort((a, b) => a.nameFr.localeCompare(b.nameFr, 'fr'));
      try { localStorage.setItem(CACHE_KEY, JSON.stringify({ts: Date.now(), data: countriesData})); } catch(e) {}
    } catch(err) {
      // Fallback minimal
      countriesData = [
        {iso2:'CM',name:'Cameroon',nameFr:'Cameroun',phone:'+237'},
        {iso2:'FR',name:'France',nameFr:'France',phone:'+33'},
        {iso2:'SN',name:'Senegal',nameFr:'Sénégal',phone:'+221'},
        {iso2:'CI',name:'Ivory Coast',nameFr:"Côte d'Ivoire",phone:'+225'},
        {iso2:'GA',name:'Gabon',nameFr:'Gabon',phone:'+241'},
        {iso2:'CD',name:'DR Congo',nameFr:'Congo (RDC)',phone:'+243'},
        {iso2:'CG',name:'Congo',nameFr:'Congo',phone:'+242'},
        {iso2:'NG',name:'Nigeria',nameFr:'Nigéria',phone:'+234'},
        {iso2:'MA',name:'Morocco',nameFr:'Maroc',phone:'+212'},
        {iso2:'DZ',name:'Algeria',nameFr:'Algérie',phone:'+213'},
        {iso2:'TN',name:'Tunisia',nameFr:'Tunisie',phone:'+216'},
        {iso2:'US',name:'United States',nameFr:'États-Unis',phone:'+1'},
        {iso2:'GB',name:'United Kingdom',nameFr:'Royaume-Uni',phone:'+44'},
        {iso2:'DE',name:'Germany',nameFr:'Allemagne',phone:'+49'},
        {iso2:'ML',name:'Mali',nameFr:'Mali',phone:'+223'},
        {iso2:'BJ',name:'Benin',nameFr:'Bénin',phone:'+229'},
        {iso2:'TG',name:'Togo',nameFr:'Togo',phone:'+228'},
        {iso2:'GN',name:'Guinea',nameFr:'Guinée',phone:'+224'},
        {iso2:'NE',name:'Niger',nameFr:'Niger',phone:'+227'},
        {iso2:'BF',name:'Burkina Faso',nameFr:'Burkina Faso',phone:'+226'},
      ];
    }
  }

  // Initialiser Tom Select
  document.getElementById('country-loading').style.display = 'none';
  const selEl = document.getElementById('country-select');
  selEl.style.display = 'block';

  const options = countriesData.map(c => ({
    value: c.iso2,
    label: `${flagEmoji(c.iso2)} ${c.nameFr}`,
    search: `${c.nameFr} ${c.name} ${c.iso2} ${c.phone}`,
    phone: c.phone,
    nameEn: c.name,
    nameFr: c.nameFr,
  }));

  const oldVal = '{{ old("code_pays") }}';

  tsCountry = new TomSelect('#country-select', {
    options,
    items: oldVal ? [oldVal] : [],
    valueField: 'value',
    labelField: 'label',
    searchField: ['search'],
    placeholder: '🌍  Rechercher un pays…',
    render: {
      option: (d, esc) =>
        `<div style="display:flex;justify-content:space-between;align-items:center;">
           <span>${esc(d.label)}</span>
           <span style="color:var(--muted);font-size:12px;">${esc(d.phone||'')}</span>
         </div>`,
      item: (d, esc) =>
        `<span>${esc(d.label)} <span style="color:var(--muted);font-size:12px;">${esc(d.phone||'')}</span></span>`,
    },
    onChange: onCountryChange,
  });

  if (oldVal) onCountryChange(oldVal);
}

// ════════════════════════════════════════════════════════
//  TÉLÉPHONE — intl-tel-input
// ════════════════════════════════════════════════════════

const phoneEl = document.getElementById('phone-input');
const iti = window.intlTelInput(phoneEl, {
  utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js',
  initialCountry: 'cm',
  preferredCountries: ['cm','fr','sn','ci','ng','gh','ga','cd','cg','ma','dz'],
  separateDialCode: true,
});

// ════════════════════════════════════════════════════════
//  CHANGEMENT DE PAYS
// ════════════════════════════════════════════════════════

async function onCountryChange(iso2) {
  if (!iso2) return;
  currentISO2 = iso2;

  const found = countriesData.find(c => c.iso2 === iso2);
  if (!found) return;

  // Nationalité + code
  document.getElementById('field-nationalite').value  = found.nameFr;
  document.getElementById('field-code-pays').value    = iso2;

  // Téléphone
  try { iti.setCountry(iso2.toLowerCase()); } catch(e) {}

  // Réinitialiser niveaux 2-4
  resetField('sel-region','txt-region','Région / Province / État','— Sélectionner un pays —');
  resetField('sel-dept',  'txt-dept',  'Département / District',  '— Sélectionner une région —');
  resetField('sel-arro',  'txt-arro',  'Arrondissement / Commune','— Sélectionner un département —');
  resetField('sel-ville', 'txt-ville', 'Ville','— Sélectionner —');

  isCameroon = (iso2 === 'CM');

  if (isCameroon) {
    // Données Cameroun embarquées
    document.getElementById('lbl-region').textContent = 'Région';
    document.getElementById('lbl-dept').textContent   = 'Département';
    document.getElementById('lbl-arro').textContent   = 'Arrondissement';
    fillSelect('sel-region', Object.keys(CM_DATA), '— Choisir une région —');
    switchGeo('sel-region','txt-region', true);
    document.getElementById('sel-region').onchange = onRegionChangeCM;
  } else {
    // API countriesnow.space
    document.getElementById('lbl-region').textContent = 'Région / Province / État';
    document.getElementById('lbl-dept').textContent   = 'Département / District';
    document.getElementById('lbl-arro').textContent   = 'Arrondissement / Commune';
    await loadRegionsAPI(found.nameEn);
  }
}

// ════════════════════════════════════════════════════════
//  CAMEROUN — cascade embarquée
// ════════════════════════════════════════════════════════

function onRegionChangeCM() {
  const region = this.value;
  resetField('sel-dept',  'txt-dept',  'Département','— Choisir un département —');
  resetField('sel-arro',  'txt-arro',  'Arrondissement','— Choisir un arrondissement —');
  resetField('sel-ville', 'txt-ville', 'Ville','— Sélectionner —');

  if (!region || !CM_DATA[region]) return;

  const depts = Object.keys(CM_DATA[region].depts);
  fillSelect('sel-dept', depts, '— Choisir un département —');
  switchGeo('sel-dept','txt-dept', true);

  document.getElementById('sel-dept').onchange = function() {
    const dept = this.value;
    resetField('sel-arro',  'txt-arro',  'Arrondissement','— Choisir un arrondissement —');
    resetField('sel-ville', 'txt-ville', 'Ville','—');
    if (!dept || !CM_DATA[region]?.depts[dept]) return;
    const arros = CM_DATA[region].depts[dept];
    fillSelect('sel-arro', arros, '— Choisir un arrondissement —');
    switchGeo('sel-arro','txt-arro', true);
    // Villes = texte libre pour Cameroun
    switchGeo('sel-ville','txt-ville', false);
    document.getElementById('txt-ville').placeholder = 'Entrez votre ville';
  };
}

// ════════════════════════════════════════════════════════
//  AUTRES PAYS — API countriesnow.space
// ════════════════════════════════════════════════════════

async function loadRegionsAPI(countryNameEn) {
  showSpin('spin-region', true);
  try {
    const res  = await fetch('https://countriesnow.space/api/v0.1/countries/states', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({country: countryNameEn}),
    });
    const json = await res.json();
    const states = json?.data?.states;
    if (!states || !states.length) throw new Error('no states');

    fillSelect('sel-region', states.map(s => s.name), '— Sélectionner une région —');
    switchGeo('sel-region','txt-region', true);

    document.getElementById('sel-region').onchange = async function() {
      const state = this.value;
      resetField('sel-dept', 'txt-dept', 'Département','—');
      resetField('sel-arro', 'txt-arro', 'Arrondissement','—');
      resetField('sel-ville','txt-ville','Ville','—');
      if (!state) return;
      // Pour les autres pays: charger les villes (pas de département API)
      switchGeo('sel-dept','txt-dept',false);
      switchGeo('sel-arro','txt-arro',false);
      await loadVillesAPI(countryNameEn, state);
    };
  } catch(e) {
    switchGeo('sel-region','txt-region', false);
    document.getElementById('txt-region').placeholder = 'Région / Province / État';
  } finally {
    showSpin('spin-region', false);
  }
}

async function loadVillesAPI(countryNameEn, state) {
  showSpin('spin-ville', true);
  try {
    const res  = await fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({country: countryNameEn, state}),
    });
    const json = await res.json();
    const cities = json?.data;
    if (!cities || !cities.length) throw new Error('no cities');

    fillSelect('sel-ville', cities, '— Sélectionner une ville —');
    switchGeo('sel-ville','txt-ville', true);
  } catch(e) {
    switchGeo('sel-ville','txt-ville', false);
    document.getElementById('txt-ville').placeholder = 'Ville';
  } finally {
    showSpin('spin-ville', false);
  }
}

// ════════════════════════════════════════════════════════
//  FORCE MOT DE PASSE
// ════════════════════════════════════════════════════════

document.getElementById('password-input').addEventListener('input', function() {
  const v = this.value;
  let s = 0;
  if (v.length >= 8)           s++;
  if (v.length >= 12)          s++;
  if (/[A-Z]/.test(v))        s++;
  if (/[0-9]/.test(v))        s++;
  if (/[^A-Za-z0-9]/.test(v)) s++;

  const fill   = document.getElementById('strength-fill');
  const label  = document.getElementById('strength-label');
  const pct    = [0,25,45,65,85,100][s];
  const colors = ['#1f2d5e','#ef4444','#f97316','#eab308','#22c55e','#39ff14'];
  const labels = ['','Très faible','Faible','Moyen','Fort','Très fort ✓'];

  fill.style.width      = pct + '%';
  fill.style.background = colors[s];
  label.textContent     = v ? labels[s] : 'Saisissez un mot de passe';
  label.style.color     = colors[s];
});

document.getElementById('confirm-input').addEventListener('input', function() {
  const pwd  = document.getElementById('password-input').value;
  const lbl  = document.getElementById('confirm-label');
  if (!this.value) { lbl.textContent = ' '; return; }
  if (this.value === pwd) {
    lbl.textContent = '✓ Les mots de passe correspondent';
    lbl.style.color = '#22c55e';
  } else {
    lbl.textContent = '✗ Les mots de passe ne correspondent pas';
    lbl.style.color = '#ef4444';
  }
});

// ════════════════════════════════════════════════════════
//  PHOTO — prévisualisation
// ════════════════════════════════════════════════════════

document.getElementById('photo-input').addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;
  if (file.size > 3 * 1024 * 1024) {
    alert('La photo ne doit pas dépasser 3 Mo.');
    this.value = '';
    return;
  }
  const reader = new FileReader();
  reader.onload = e => document.getElementById('photo-preview').src = e.target.result;
  reader.readAsDataURL(file);
});

// ════════════════════════════════════════════════════════
//  SOUMISSION — fusion champs géo
// ════════════════════════════════════════════════════════

document.getElementById('reg-form').addEventListener('submit', function() {
  // Téléphone international
  try { document.getElementById('phone-full').value = iti.getNumber(); } catch(e) {}

  // Région
  const selReg = document.getElementById('sel-region');
  const txtReg = document.getElementById('txt-region');
  if (selReg.style.display !== 'none' && selReg.value) txtReg.value = selReg.value;

  // Département
  const selDep = document.getElementById('sel-dept');
  const txtDep = document.getElementById('txt-dept');
  if (selDep.style.display !== 'none' && selDep.value) txtDep.value = selDep.value;

  // Arrondissement
  const selArr = document.getElementById('sel-arro');
  const txtArr = document.getElementById('txt-arro');
  if (selArr.style.display !== 'none' && selArr.value) txtArr.value = selArr.value;

  // Ville
  const selVil = document.getElementById('sel-ville');
  const txtVil = document.getElementById('txt-ville');
  if (selVil.style.display !== 'none' && selVil.value) txtVil.value = selVil.value;
});

// ════════════════════════════════════════════════════════
//  INIT
// ════════════════════════════════════════════════════════

loadCountries();
</script>
</body>
</html>
