<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription — Surveillance</title>
<link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:#060c1a;min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:Arial,Helvetica,sans-serif;padding:20px}
.card{background:#0e1a38;border:1px solid #1e2f5a;border-radius:16px;padding:36px 32px;width:100%;max-width:420px}
h1{font-size:20px;font-weight:800;color:#fff;text-align:center;margin-bottom:6px}
.sub{text-align:center;font-size:12px;color:#3a4a6a;margin-bottom:28px}
.field{margin-bottom:16px}
label{display:block;font-size:11px;color:#8899cc;margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px}
input{width:100%;background:#07102a;border:1px solid #1e2f5a;border-radius:8px;padding:11px 14px;color:#fff;font-size:13px;outline:none;transition:.2s}
input:focus{border-color:#33b5ff}
.row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.pw-wrap{position:relative}
.pw-wrap input{padding-right:40px}
.pw-eye{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#3a4a6a;cursor:pointer;font-size:14px}
.btn{width:100%;padding:13px;background:rgba(51,255,136,.1);border:1px solid rgba(51,255,136,.35);color:#33ff88;font-size:14px;font-weight:800;border-radius:10px;cursor:pointer;margin-top:8px;transition:.2s}
.btn:hover{background:rgba(51,255,136,.2)}
.alert{padding:10px 14px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:18px}
.alert-e{background:rgba(255,87,51,.1);border:1px solid rgba(255,87,51,.3);color:#ff5733}
.alert-s{background:rgba(51,255,136,.1);border:1px solid rgba(51,255,136,.3);color:#33ff88}
.sep{height:1px;background:#1e2f5a;margin:22px 0}
.links{text-align:center;font-size:13px;color:#556}
.links a{color:#33b5ff;text-decoration:none;font-weight:700}
</style>
</head>
<body>

<div class="card">
  <h1><i class="fa-solid fa-user-plus" style="color:#33ff88;margin-right:8px"></i>Créer un compte</h1>
  <div class="sub">Plateforme de Surveillance des Salles Serveurs</div>

  @if(session('error'))
  <div class="alert alert-e"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
  @endif
  @if(session('success'))
  <div class="alert alert-s"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
  @endif

  <form method="POST" action="/inscription">
    @csrf

    <div class="row">
      <div class="field">
        <label>Prénom *</label>
        <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="Prénom" required>
      </div>
      <div class="field">
        <label>Nom *</label>
        <input type="text" name="nom" value="{{ old('nom') }}" placeholder="Nom" required>
      </div>
    </div>

    <div class="field">
      <label>Adresse email *</label>
      <input type="email" name="email" value="{{ old('email') }}" placeholder="votre@email.com" required autocomplete="email">
    </div>

    <div class="field">
      <label>Mot de passe *</label>
      <div class="pw-wrap">
        <input type="password" name="password" id="pw1" placeholder="Minimum 8 caractères" required>
        <button type="button" class="pw-eye" onclick="toggle('pw1',this)"><i class="fa-solid fa-eye"></i></button>
      </div>
    </div>

    <div class="field">
      <label>Confirmer le mot de passe *</label>
      <div class="pw-wrap">
        <input type="password" name="password_confirmation" id="pw2" placeholder="Répétez le mot de passe" required>
        <button type="button" class="pw-eye" onclick="toggle('pw2',this)"><i class="fa-solid fa-eye"></i></button>
      </div>
    </div>

    <input type="hidden" name="role" value="utilisateur">
    <input type="hidden" name="telephone" value="">
    <input type="hidden" name="pays" value="">

    <button type="submit" class="btn"><i class="fa-solid fa-paper-plane"></i> S'inscrire</button>
  </form>

  <div class="sep"></div>

  <div class="links">
    Déjà un compte ? <a href="/login">S'authentifier</a>
  </div>
</div>

<script>
function toggle(id, btn) {
  var i = document.getElementById(id);
  if (i.type === 'password') { i.type = 'text'; btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>'; }
  else { i.type = 'password'; btn.innerHTML = '<i class="fa-solid fa-eye"></i>'; }
}
</script>
</body>
</html>
