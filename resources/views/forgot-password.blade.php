<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mot de passe oublié — Surveillance</title>
<link rel="stylesheet" href="/css/noselect.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;}
::-webkit-scrollbar{width:6px;height:6px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:#1e3050;border-radius:4px;}
::-webkit-scrollbar-thumb:hover{background:#2fa84f;}
html{scrollbar-width:thin;scrollbar-color:#1e3050 transparent;}

body{
  background:#060c1a;
  color:#d4dced;
  min-height:100vh;
  display:flex;flex-direction:column;
}

.navbar{
  display:flex;justify-content:space-between;align-items:center;
  padding:15px 32px;
  background:rgba(8,15,30,0.97);
  border-bottom:1px solid #182640;
  position:sticky;top:0;z-index:100;
  flex-wrap:wrap;gap:10px;
}
.logo{font-size:19px;font-weight:bold;color:#2fa84f;letter-spacing:2px;text-decoration:none;}
.btn-nav{
  padding:8px 20px;border:1.5px solid #2fa84f;
  background:transparent;color:#2fa84f;
  border-radius:8px;font-size:13px;font-weight:bold;
  text-decoration:none;transition:.2s;
}
.btn-nav:hover{background:#2fa84f;color:#060c1a;}

.page-center{
  flex:1;display:flex;justify-content:center;align-items:center;padding:30px 16px;
}

.card{
  width:100%;max-width:420px;
  background:#0d1a2e;
  padding:36px 32px;
  border-radius:16px;
  border:1px solid #182640;
  box-shadow:0 8px 32px rgba(0,0,0,0.4);
}

.card-icon{
  text-align:center;font-size:44px;margin-bottom:14px;color:#2fa84f;opacity:.8;
}
.card-title{color:#2fa84f;text-align:center;margin-bottom:6px;font-size:24px;font-weight:bold;}
.card-sub{text-align:center;color:#6b7fa0;font-size:13px;margin-bottom:26px;line-height:1.6;}

.alert{padding:11px 14px;border-radius:8px;margin-bottom:16px;font-size:13px;font-weight:bold;}
.alert-error{background:#3a0a0a;border:1px solid #c0392b;color:#f1948a;}
.alert-success{background:#062010;border:1px solid #27ae60;color:#82e0aa;}

.field{margin-bottom:16px;}
.field label{display:block;font-size:12px;color:#8090b0;font-weight:bold;margin-bottom:5px;}
.field input{
  width:100%;padding:11px 14px;
  background:#0a1525;border:1.5px solid #1e3050;
  border-radius:9px;font-size:14px;color:#d4dced;
  outline:none;transition:border-color .2s;
}
.field input:focus{border-color:#2fa84f;}
.field input::placeholder{color:#3d5070;}

.btn-submit{
  width:100%;padding:13px;background:#2fa84f;
  color:#060c1a;border:none;border-radius:9px;
  font-size:15px;font-weight:bold;cursor:pointer;
  margin-top:4px;transition:.2s;
}
.btn-submit:hover{background:#249040;}

.back-link{
  display:block;text-align:center;margin-top:18px;
  color:#5a7090;text-decoration:none;font-size:13px;transition:color .2s;
}
.back-link:hover{color:#2fa84f;}

@media(max-width:480px){
  .navbar{padding:12px 18px;}
  .card{padding:24px 18px;}
}
</style>
</head>
<body>

<nav class="navbar">
  <a class="logo" href="/accueil">SURVEILLANCE</a>
  <a href="/login" class="btn-nav"><i class="fa-solid fa-arrow-left"></i> Retour</a>
</nav>

<div class="page-center">
  <div class="card">

    <div class="card-icon"><i class="fa-solid fa-key"></i></div>
    <div class="card-title">Mot de passe oublié</div>
    <div class="card-sub">
      Saisissez votre adresse email. Vous recevrez un lien pour réinitialiser votre mot de passe.
    </div>

    @if(session('error'))
      <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
    @endif
    @if(session('success'))
      <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    @if(!session('success'))
    <form method="POST" action="/forgot-password">
      @csrf
      <div class="field">
        <label><i class="fa-solid fa-envelope"></i> Adresse email</label>
        <input type="email" name="email" placeholder="Votre adresse email" required autocomplete="email">
      </div>
      <button type="submit" class="btn-submit">
        <i class="fa-solid fa-paper-plane"></i> ENVOYER LE LIEN
      </button>
    </form>
    @endif

    <a href="/login" class="back-link">
      <i class="fa-solid fa-arrow-left"></i> Retour à la connexion
    </a>

  </div>
</div>

</body>
</html>
