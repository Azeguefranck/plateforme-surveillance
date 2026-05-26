<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="mobile-web-app-capable" content="no">
<meta name="apple-mobile-web-app-capable" content="no">
<title>Connexion — Plateforme Surveillance</title>
<link rel="stylesheet" href="/css/noselect.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;}
/* Scrollbar fine thémée */
::-webkit-scrollbar{width:6px;height:6px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:#1e3050;border-radius:4px;}
::-webkit-scrollbar-thumb:hover{background:#2fa84f;}
html{scrollbar-width:thin;scrollbar-color:#1e3050 transparent;}

body{
  background:#060c1a;
  color:#d4dced;
  min-height:100vh;
  display:flex;
  flex-direction:column;
}

/* ─── NAVBAR ─── */
.navbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:15px 32px;
  background:rgba(8,15,30,0.97);
  border-bottom:1px solid #182640;
  position:sticky;
  top:0;
  z-index:100;
  flex-wrap:wrap;
  gap:10px;
}
.logo{font-size:19px;font-weight:bold;color:#2fa84f;letter-spacing:2px;text-decoration:none;}

.btn-nav{
  padding:8px 20px;
  border:1.5px solid #2fa84f;
  background:transparent;
  color:#2fa84f;
  border-radius:8px;
  font-size:13px;
  font-weight:bold;
  text-decoration:none;
  transition:.2s;
}
.btn-nav:hover{background:#2fa84f;color:#060c1a;}

/* ─── PAGE CENTER ─── */
.page-center{
  flex:1;
  display:flex;
  justify-content:center;
  align-items:center;
  padding:30px 16px;
}

/* ─── CARD ─── */
.card{
  width:100%;
  max-width:440px;
  background:#0d1a2e;
  padding:36px 32px;
  border-radius:16px;
  border:1px solid #182640;
  box-shadow:0 8px 32px rgba(0,0,0,0.4);
}

.card-title{
  color:#2fa84f;
  text-align:center;
  margin-bottom:5px;
  font-size:26px;
  font-weight:bold;
}
.card-sub{text-align:center;color:#6b7fa0;font-size:13px;margin-bottom:26px;}

/* ─── ALERTES ─── */
.alert{padding:11px 14px;border-radius:8px;margin-bottom:16px;font-size:13px;font-weight:bold;}
.alert-error{background:#3a0a0a;border:1px solid #c0392b;color:#f1948a;}
.alert-success{background:#062010;border:1px solid #27ae60;color:#82e0aa;}

/* ─── CHAMPS ─── */
.field{margin-bottom:14px;}
.field label{
  display:block;font-size:12px;color:#8090b0;
  font-weight:bold;margin-bottom:5px;
}

.input-wrap{position:relative;}
.input-wrap input{
  width:100%;
  padding:11px 44px 11px 14px;
  background:#0a1525;
  border:1.5px solid #1e3050;
  border-radius:9px;
  font-size:14px;
  color:#d4dced;
  outline:none;
  transition:border-color .2s;
}
.input-wrap input:focus{border-color:#2fa84f;}
.input-wrap input::placeholder{color:#3d5070;}

.eye-btn{
  position:absolute;
  right:12px;top:50%;
  transform:translateY(-50%);
  background:none;border:none;
  color:#6b7fa0;cursor:pointer;
  font-size:15px;
  padding:2px;
  transition:color .2s;
}
.eye-btn:hover{color:#2fa84f;}

/* ─── BOUTONS ─── */
.btn-submit{
  width:100%;
  padding:13px;
  background:#2fa84f;
  color:#060c1a;
  border:none;
  border-radius:9px;
  font-size:16px;
  font-weight:bold;
  cursor:pointer;
  margin-top:6px;
  transition:.2s;
}
.btn-submit:hover{background:#249040;}

.forgot-link{
  display:block;
  text-align:center;
  color:#5a7090;
  text-decoration:none;
  font-size:12px;
  margin-top:10px;
  transition:color .2s;
}
.forgot-link:hover{color:#2fa84f;}

.divider{display:flex;align-items:center;gap:10px;margin:16px 0 10px;}
.divider-line{flex:1;height:1px;background:#182640;}
.divider-text{font-size:11px;color:#3d5070;}

.links-bar{display:flex;flex-direction:column;gap:8px;}

.btn-link-outline{
  display:block;text-align:center;
  padding:11px;
  border:1.5px solid #182640;
  border-radius:9px;
  color:#d4dced;
  text-decoration:none;
  font-size:13px;font-weight:bold;
  transition:.2s;background:transparent;
}
.btn-link-outline:hover{border-color:#2fa84f;color:#2fa84f;background:rgba(47,168,79,0.06);}

/* ─── RESPONSIVE ─── */
@media(max-width:480px){
  .navbar{padding:12px 18px;}
  .card{padding:24px 18px;}
  .card-title{font-size:22px;}
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <a class="logo" href="/accueil">SURVEILLANCE</a>
  <a href="/accueil" class="btn-nav">Accueil</a>
</nav>

<!-- CENTRE -->
<div class="page-center">
  <div class="card">

    <div class="card-title">Connexion</div>
    <div class="card-sub">Accédez à la plateforme de surveillance</div>

    @if(session('error'))
      <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
    @endif
    @if(session('success'))
      <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <form method="POST" action="/login-user">
      @csrf

      <div class="field">
        <label><i class="fa-solid fa-lock"></i> Mot de passe</label>
        <div class="input-wrap">
          <input type="password" name="password" id="pwd" placeholder="Entrez le mot de passe" required autocomplete="current-password" autofocus>
          <button type="button" class="eye-btn" onclick="togglePwd('pwd','eye1')" title="Afficher/masquer">
            <i class="fa-solid fa-eye" id="eye1"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-submit">
        <i class="fa-solid fa-right-to-bracket"></i> SE CONNECTER
      </button>
    </form>

  </div>
</div>

<script>
window.addEventListener('beforeinstallprompt',function(e){e.preventDefault();});
function togglePwd(id, iconId){
  const inp=document.getElementById(id);
  const ico=document.getElementById(iconId);
  if(inp.type==='password'){
    inp.type='text';
    ico.classList.replace('fa-eye','fa-eye-slash');
  } else {
    inp.type='password';
    ico.classList.replace('fa-eye-slash','fa-eye');
  }
}
</script>

</body>
</html>
