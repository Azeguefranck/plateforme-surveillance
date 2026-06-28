<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription — Plateforme Surveillance</title>
<link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
<style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

body {
  background:#020c1a;
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  font-family:'Segoe UI', Arial, sans-serif;
  padding:20px;
  position:relative;
  overflow:hidden;
}

body::before {
  content:'';
  position:fixed;inset:0;
  background-image:
    linear-gradient(rgba(0,255,136,.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0,255,136,.03) 1px, transparent 1px);
  background-size:45px 45px;
  pointer-events:none;
}

body::after {
  content:'';
  position:fixed;inset:0;
  background:radial-gradient(ellipse 60% 60% at 50% 50%, rgba(0,255,136,.05) 0%, transparent 70%);
  pointer-events:none;
}

.box {
  position:relative;z-index:1;
  width:100%;max-width:440px;
  background:linear-gradient(135deg,#0e1a38,#0a1225);
  padding:40px 38px;
  border-radius:20px;
  border:1px solid #1e2f5a;
  box-shadow:0 0 60px rgba(0,0,0,.6), 0 0 30px rgba(0,255,136,.05);
  animation:fadeUp .5s ease;
}

@keyframes fadeUp {
  from{opacity:0;transform:translateY(18px)}
  to{opacity:1;transform:translateY(0)}
}

.box-top { text-align:center; margin-bottom:28px; }

.box-brand {
  font-size:13px;letter-spacing:5px;color:#33ff88;
  text-transform:uppercase;font-weight:700;
  display:flex;align-items:center;justify-content:center;gap:10px;
  margin-bottom:10px;
}
.brand-line { width:28px;height:1px;background:linear-gradient(90deg,transparent,#33ff88); }
.brand-line.r{transform:scaleX(-1)}

h1 { font-size:26px;font-weight:800;color:#fff;letter-spacing:1px;margin:0; }
h1 span{color:#33ff88}

.alert-box {
  display:flex;align-items:flex-start;gap:10px;
  padding:12px 14px;border-radius:10px;
  margin-bottom:16px;font-size:13px;font-weight:600;line-height:1.4;
  animation:fadeUp .3s ease;
}
.alert-error  {background:rgba(255,87,51,.1);border:1px solid rgba(255,87,51,.3);color:#ff7755}
.alert-success{background:rgba(51,255,136,.1);border:1px solid rgba(51,255,136,.3);color:#33ff88}

.row { display:grid;grid-template-columns:1fr 1fr;gap:12px; }

.field { margin-bottom:16px; }
.field label {
  display:block;
  font-size:11px;font-weight:700;letter-spacing:.8px;
  color:#5a6a99;text-transform:uppercase;margin-bottom:6px;
}
.field input {
  width:100%;padding:12px 16px;
  background:rgba(255,255,255,.04);
  border:1px solid #1e2f5a;border-radius:9px;
  color:#e0e8ff;font-size:15px;outline:none;
  transition:.25s;font-family:inherit;
}
.field input:focus {
  border-color:#33ff88;
  box-shadow:0 0 0 3px rgba(51,255,136,.08);
}
.field input::placeholder{color:#3a4a6a}

.pw-wrap{position:relative}
.pw-wrap input{padding-right:44px}
.pw-eye{
  position:absolute;right:12px;top:50%;transform:translateY(-50%);
  background:none;border:none;color:#5a6a99;cursor:pointer;
  font-size:16px;transition:.2s;padding:0;
}
.pw-eye:hover{color:#33ff88}

.btn-submit {
  width:100%;padding:13px;
  background:linear-gradient(135deg,rgba(51,255,136,.15),rgba(51,255,136,.08));
  border:1px solid rgba(51,255,136,.35);border-radius:9px;
  color:#33ff88;font-size:15px;font-weight:800;
  letter-spacing:1.5px;cursor:pointer;transition:.25s;
  text-transform:uppercase;margin-top:4px;
}
.btn-submit:hover {
  background:linear-gradient(135deg,rgba(51,255,136,.22),rgba(51,255,136,.12));
  box-shadow:0 0 24px rgba(51,255,136,.3);
  transform:translateY(-1px);
}

.sep { height:1px;margin:20px 0;background:linear-gradient(90deg,transparent,#1e2f5a,transparent); }

.links { margin-top:12px;display:flex;flex-direction:column;gap:10px;align-items:center; }
.links a { color:#5a6a99;text-decoration:none;font-size:13px;transition:.2s; }
.links a:hover{color:#33ff88}

@media(max-width:640px){
  body{overflow-x:hidden}
  .row{grid-template-columns:1fr}
  input,select,textarea{max-width:100%!important;width:100%!important}
}
</style>
</head>
<body>

<div class="box">

  <div class="box-top">
    <h1><span>Inscription</span></h1>
  </div>

  @if(session('error'))
    <div class="alert-box alert-error">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <span>{{ session('error') }}</span>
    </div>
  @endif

  @if(session('success'))
    <div class="alert-box alert-success">
      <i class="fa-solid fa-circle-check"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  <form method="POST" action="/inscription">
    @csrf

    <div class="row">
      <div class="field">
        <label>Prénom</label>
        <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="Prénom" required>
      </div>
      <div class="field">
        <label>Nom</label>
        <input type="text" name="nom" value="{{ old('nom') }}" placeholder="Nom" required>
      </div>
    </div>

    <div class="field">
      <label>Adresse email</label>
      <input type="email" name="email" value="{{ old('email') }}" placeholder="votre@email.com" required autocomplete="email">
    </div>

    <div class="field">
      <label>Mot de passe</label>
      <div class="pw-wrap">
        <input type="password" name="password" id="pw1" placeholder="Minimum 8 caractères" required>
        <button type="button" class="pw-eye" onclick="pwToggle('pw1',this)"><i class="fa-solid fa-eye"></i></button>
      </div>
    </div>

    <div class="field">
      <label>Confirmer le mot de passe</label>
      <div class="pw-wrap">
        <input type="password" name="password_confirmation" id="pw2" placeholder="Répétez le mot de passe" required>
        <button type="button" class="pw-eye" onclick="pwToggle('pw2',this)"><i class="fa-solid fa-eye"></i></button>
      </div>
    </div>

    <input type="hidden" name="role" value="utilisateur">
    <input type="hidden" name="telephone" value="">
    <input type="hidden" name="pays" value="">

    <button type="submit" class="btn-submit"><i class="fa-solid fa-paper-plane"></i> S'inscrire</button>
  </form>

  <div class="sep"></div>

  <div class="links">
    <span style="font-size:13px;color:#8899cc">Déjà un compte ? <a href="/login" style="color:#33ff88;font-weight:700">S'authentifier</a></span>
    <a href="/accueil">← Retour à l'accueil</a>
  </div>

</div>

<script>
function pwToggle(id, btn) {
  var i = document.getElementById(id);
  if (i.type === 'password') {
    i.type = 'text';
    btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
  } else {
    i.type = 'password';
    btn.innerHTML = '<i class="fa-solid fa-eye"></i>';
  }
}
</script>
</body>
</html>
