<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Authentification — Plateforme de Surveillance</title>
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
.box-brand {
  font-size:13px;letter-spacing:5px;color:#33ff88;
  text-transform:uppercase;font-weight:700;
  display:flex;align-items:center;justify-content:center;gap:10px;
  margin-bottom:10px;
}
.brand-line {
  width:28px;height:1px;
  background:linear-gradient(90deg,transparent,#33ff88);
}
.brand-line.r{transform:scaleX(-1)}

h1 {
  font-size:26px;font-weight:800;color:#fff;
  letter-spacing:1px;margin:0;
}
h1 span{color:#33ff88}

.alert-box {
  display:flex;align-items:flex-start;gap:10px;
  padding:12px 14px;border-radius:10px;
  margin-bottom:16px;font-size:13px;font-weight:600;line-height:1.4;
  animation:fadeUp .3s ease;
}
.alert-error  {background:rgba(255,87,51,.1);border:1px solid rgba(255,87,51,.3);color:#ff7755}
.alert-success{background:rgba(51,255,136,.1);border:1px solid rgba(51,255,136,.3);color:#33ff88}
.alert-warn   {background:rgba(255,214,51,.1);border:1px solid rgba(255,214,51,.3);color:#ffd633}

.field {
  margin-bottom:16px;
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
    <h1><span>Authentification</span></h1>
  </div>

  @if(session('error'))
    @php
      $err = session('error');
      $cls = str_contains($err, 'attente') ? 'alert-warn' : (str_contains($err, 'refusé') ? 'alert-error' : 'alert-error');
      $ico = str_contains($err, 'attente') ? '<i class="fa-solid fa-clock"></i>' : (str_contains($err, 'refusé') ? '<i class="fa-solid fa-circle-xmark"></i>' : '<i class="fa-solid fa-triangle-exclamation"></i>');
    @endphp
    <div class="alert-box {{ $cls }}">
      <span>{!! $ico !!}</span>
      <span>{{ $err }}</span>
    </div>
  @endif

  @if(session('success'))
    <div class="alert-box alert-success">
      <span><i class="fa-solid fa-circle-check"></i></span>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  <form method="POST" action="/login-user">
    @csrf

    <div class="field">
      <label>Adresse email</label>
      <input type="email" name="email" placeholder="votre@email.com" required autocomplete="email">
    </div>

    <div class="field">
      <label>Mot de passe</label>
      <div class="pw-wrap">
        <input type="password" name="mot_de_passe" id="pw-input" placeholder="••••••••" required>
        <button type="button" class="pw-eye" onclick="var i=this.previousElementSibling;if(i.type==='password'){i.type='text';this.innerHTML='<i class=\'fa-solid fa-eye-slash\'></i>';}else{i.type='password';this.innerHTML='<i class=\'fa-solid fa-eye\'></i>';}"><i class="fa-solid fa-eye"></i></button>
      </div>
    </div>

    <div style="text-align:right;margin-bottom:16px">
      <a href="/forgot-password" style="font-size:12px;color:#8899cc;text-decoration:none">Mot de passe oublié ?</a>
    </div>

    <button type="submit" class="btn-submit">⊙ S'authentifier</button>

  </form>

  <div class="sep"></div>

  <div class="links" style="text-align:center;font-size:13px;color:#8899cc">
    Pas encore de compte ? <a href="/inscription" style="color:#33ff88;font-weight:700">S'inscrire</a>
  </div>
  <div class="links" style="margin-top:8px">
    <a href="/accueil">← Retour à l'accueil</a>
  </div>

</div>

</body>
</html>
