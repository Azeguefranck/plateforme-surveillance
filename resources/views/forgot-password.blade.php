<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mot de passe oublié — Surveillance</title>
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

.box-top {
  text-align:center;
  margin-bottom:28px;
}

.box-icon {
  width:64px;height:64px;
  border-radius:50%;
  background:rgba(51,181,255,.1);
  border:1.5px solid rgba(51,181,255,.3);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 16px;
  font-size:26px;color:#33b5ff;
}

h1 {
  font-size:22px;font-weight:800;color:#fff;
  letter-spacing:.5px;margin:0 0 8px;
}

.subtitle {
  color:#5a6a99;font-size:13px;line-height:1.5;
}

.alert-box {
  display:flex;align-items:flex-start;gap:10px;
  padding:12px 14px;border-radius:10px;
  margin-bottom:16px;font-size:13px;font-weight:600;line-height:1.4;
  animation:fadeUp .3s ease;
}
.alert-error  {background:rgba(255,87,51,.1);border:1px solid rgba(255,87,51,.3);color:#ff7755}
.alert-success{background:rgba(51,255,136,.1);border:1px solid rgba(51,255,136,.3);color:#33ff88}

.field {
  margin-bottom:18px;
}
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
  user-select:text;-webkit-user-select:text;
}
.field input:focus {
  border-color:#33b5ff;
  box-shadow:0 0 0 3px rgba(51,181,255,.08);
}
.field input::placeholder{color:#3a4a6a}

.btn-submit {
  width:100%;padding:13px;
  background:linear-gradient(135deg,rgba(51,181,255,.15),rgba(51,181,255,.08));
  border:1px solid rgba(51,181,255,.35);border-radius:9px;
  color:#33b5ff;font-size:15px;font-weight:800;
  letter-spacing:1.2px;cursor:pointer;transition:.25s;
  text-transform:uppercase;margin-top:4px;
  display:flex;align-items:center;justify-content:center;gap:8px;
}
.btn-submit:hover {
  background:linear-gradient(135deg,rgba(51,181,255,.22),rgba(51,181,255,.12));
  box-shadow:0 0 24px rgba(51,181,255,.3);
  transform:translateY(-1px);
}
.btn-submit:active{transform:scale(.97)}

.links {
  margin-top:22px;
  display:flex;flex-direction:column;gap:10px;align-items:center;
}
.links a {
  color:#5a6a99;text-decoration:none;font-size:13px;transition:.2s;
}
.links a:hover{color:#33ff88}

.sep {
  height:1px;margin:20px 0;
  background:linear-gradient(90deg,transparent,#1e2f5a,transparent);
}

.info-box {
  background:rgba(51,181,255,.06);
  border:1px solid rgba(51,181,255,.2);
  border-radius:10px;
  padding:12px 14px;
  margin-bottom:20px;
  font-size:12px;
  color:#6a88bb;
  line-height:1.6;
  display:flex;
  gap:10px;
  align-items:flex-start;
}
.info-box i {color:#33b5ff;margin-top:2px;flex-shrink:0}

*{overflow-wrap:break-word;word-break:break-word}
img,video,iframe{max-width:100%;height:auto}
@media(max-width:640px){
body{overflow-x:hidden}
input,select,textarea{max-width:100%!important;width:100%!important}
[class*="card"],[class*="box"],[class*="form"],[class*="container"]{max-width:100%!important}
}
@media(max-width:400px){
h1,h2{font-size:clamp(15px,5vw,24px)!important}
}

</style>
</head>
<body>

<div class="box">

  <div class="box-top">
    <div class="box-icon"><i class="fa-solid fa-key"></i></div>
    <h1>Mot de passe oublié</h1>
    <p class="subtitle">Entrez votre adresse email pour recevoir un nouveau mot de passe</p>
  </div>

  @if(session('error'))
    <div class="alert-box alert-error">
      <i class="fa-solid fa-circle-exclamation"></i>
      <span>{{ session('error') }}</span>
    </div>
  @endif

  @if(session('success'))
    <div class="alert-box alert-success">
      <i class="fa-solid fa-circle-check"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if(!session('success'))
  <div class="info-box">
    <i class="fa-solid fa-circle-info"></i>
    <span>Un nouveau mot de passe sera généré automatiquement et envoyé à votre adresse email.</span>
  </div>

  <form method="POST" action="/forgot-password">
    @csrf

    <div class="field">
      <label><i class="fa-solid fa-envelope" style="margin-right:4px"></i>Adresse email</label>
      <input type="email" name="email" placeholder="votre@email.com" required autofocus value="{{ old('email') }}">
    </div>

    <button type="submit" class="btn-submit">
      <i class="fa-solid fa-paper-plane"></i>
      Envoyer le nouveau mot de passe
    </button>

  </form>
  @endif

  <div class="sep"></div>

  <div class="links">
    <a href="/login"><i class="fa-solid fa-arrow-left" style="margin-right:4px"></i>Retour à la connexion</a>
  </div>

</div>

</body>
</html>
