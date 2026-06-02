<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nouveau mot de passe — Surveillance</title>
<link rel="stylesheet" href="/css/noselect.css">
<link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;}
::-webkit-scrollbar{width:6px;height:6px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:#1e3050;border-radius:4px;}
::-webkit-scrollbar-thumb:hover{background:#2fa84f;}
html{scrollbar-width:thin;scrollbar-color:#1e3050 transparent;}

body{
  background:#060c1a;color:#d4dced;
  min-height:100vh;display:flex;flex-direction:column;
}

.navbar{
  display:flex;justify-content:space-between;align-items:center;
  padding:15px 32px;background:rgba(8,15,30,0.97);
  border-bottom:1px solid #182640;position:sticky;top:0;z-index:100;
  flex-wrap:wrap;gap:10px;
}
.logo{font-size:19px;font-weight:bold;color:#2fa84f;letter-spacing:2px;text-decoration:none;}
.btn-nav{
  padding:8px 20px;border:1.5px solid #2fa84f;background:transparent;
  color:#2fa84f;border-radius:8px;font-size:13px;font-weight:bold;
  text-decoration:none;transition:.2s;
}
.btn-nav:hover{background:#2fa84f;color:#060c1a;}

.page-center{flex:1;display:flex;justify-content:center;align-items:center;padding:30px 16px;}

.card{
  width:100%;max-width:440px;background:#0d1a2e;
  padding:36px 32px;border-radius:16px;
  border:1px solid #182640;box-shadow:0 8px 32px rgba(0,0,0,0.4);
}

.card-icon{text-align:center;font-size:44px;margin-bottom:14px;color:#2fa84f;opacity:.8;}
.card-title{color:#2fa84f;text-align:center;margin-bottom:6px;font-size:24px;font-weight:bold;}
.card-sub{text-align:center;color:#6b7fa0;font-size:13px;margin-bottom:26px;line-height:1.6;}

.alert{padding:11px 14px;border-radius:8px;margin-bottom:16px;font-size:13px;font-weight:bold;}
.alert-error{background:#3a0a0a;border:1px solid #c0392b;color:#f1948a;}
.alert-success{background:#062010;border:1px solid #27ae60;color:#82e0aa;}

.field{margin-bottom:14px;}
.field label{display:block;font-size:12px;color:#8090b0;font-weight:bold;margin-bottom:5px;}

.input-wrap{position:relative;}
.input-wrap input{
  width:100%;padding:11px 44px 11px 14px;
  background:#0a1525;border:1.5px solid #1e3050;
  border-radius:9px;font-size:14px;color:#d4dced;
  outline:none;transition:border-color .2s;
}
.input-wrap input:focus{border-color:#2fa84f;}
.input-wrap input::placeholder{color:#3d5070;}

.eye-btn{
  position:absolute;right:12px;top:50%;transform:translateY(-50%);
  background:none;border:none;color:#6b7fa0;cursor:pointer;
  font-size:15px;padding:2px;transition:color .2s;
}
.eye-btn:hover{color:#2fa84f;}

/* Barre de force */
.strength-bar{height:4px;border-radius:4px;background:#182640;margin-top:6px;overflow:hidden;}
.strength-fill{height:100%;width:0;transition:width .3s,background .3s;border-radius:4px;}

.strength-text{font-size:11px;color:#6b7fa0;margin-top:4px;}

.btn-submit{
  width:100%;padding:13px;background:#2fa84f;
  color:#060c1a;border:none;border-radius:9px;
  font-size:15px;font-weight:bold;cursor:pointer;
  margin-top:8px;transition:.2s;
}
.btn-submit:hover{background:#249040;}
.btn-submit:disabled{background:#1e5030;color:#4a7060;cursor:not-allowed;}

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
  <a href="/login" class="btn-nav"><i class="fa-solid fa-arrow-left"></i> Connexion</a>
</nav>

<div class="page-center">
  <div class="card">

    <div class="card-icon"><i class="fa-solid fa-shield-halved"></i></div>
    <div class="card-title">Nouveau mot de passe</div>
    <div class="card-sub">Choisissez un mot de passe sécurisé d'au moins 8 caractères.</div>

    @if(session('error'))
      <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
    @endif
    @if(session('success'))
      <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <form method="POST" action="/reset-password" id="resetForm">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="email" value="{{ $email }}">

      <div class="field">
        <label><i class="fa-solid fa-lock"></i> Nouveau mot de passe</label>
        <div class="input-wrap">
          <input type="password" name="password" id="pwd" placeholder="Minimum 8 caractères"
                 required minlength="8" oninput="checkStrength(this.value);checkMatch()">
          <button type="button" class="eye-btn" onclick="togglePwd('pwd','eye1')">
            <i class="fa-solid fa-eye" id="eye1"></i>
          </button>
        </div>
        <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
        <div class="strength-text" id="strength-text"></div>
      </div>

      <div class="field">
        <label><i class="fa-solid fa-lock"></i> Confirmer le mot de passe</label>
        <div class="input-wrap">
          <input type="password" name="password_confirmation" id="pwd2"
                 placeholder="Répétez le mot de passe" required minlength="8" oninput="checkMatch()">
          <button type="button" class="eye-btn" onclick="togglePwd('pwd2','eye2')">
            <i class="fa-solid fa-eye" id="eye2"></i>
          </button>
        </div>
        <div class="strength-text" id="match-text"></div>
      </div>

      <button type="submit" class="btn-submit" id="submit-btn" disabled>
        <i class="fa-solid fa-floppy-disk"></i> ENREGISTRER LE MOT DE PASSE
      </button>
    </form>

    <a href="/login" class="back-link">
      <i class="fa-solid fa-arrow-left"></i> Retour à la connexion
    </a>

  </div>
</div>

<script>
function togglePwd(id,iconId){
  const inp=document.getElementById(id);
  const ico=document.getElementById(iconId);
  if(inp.type==='password'){inp.type='text';ico.classList.replace('fa-eye','fa-eye-slash');}
  else{inp.type='password';ico.classList.replace('fa-eye-slash','fa-eye');}
}

function checkStrength(val){
  const fill=document.getElementById('strength-fill');
  const txt=document.getElementById('strength-text');
  let score=0;
  if(val.length>=8) score++;
  if(/[A-Z]/.test(val)) score++;
  if(/[0-9]/.test(val)) score++;
  if(/[^A-Za-z0-9]/.test(val)) score++;
  const levels=[
    {w:'0%',c:'#c0392b',l:''},
    {w:'25%',c:'#c0392b',l:'Faible'},
    {w:'50%',c:'#d97706',l:'Moyen'},
    {w:'75%',c:'#2e86c1',l:'Bon'},
    {w:'100%',c:'#2fa84f',l:'Excellent'},
  ];
  const lv=levels[score];
  fill.style.width=lv.w;fill.style.background=lv.c;
  txt.textContent=lv.l;txt.style.color=lv.c;
}

function checkMatch(){
  const p1=document.getElementById('pwd').value;
  const p2=document.getElementById('pwd2').value;
  const mt=document.getElementById('match-text');
  const btn=document.getElementById('submit-btn');
  if(!p2){mt.textContent='';btn.disabled=true;return;}
  if(p1===p2 && p1.length>=8){
    mt.textContent='Les mots de passe correspondent';mt.style.color='#2fa84f';btn.disabled=false;
  } else {
    mt.textContent='Les mots de passe ne correspondent pas';mt.style.color='#c0392b';btn.disabled=true;
  }
}
</script>
</body>
</html>
