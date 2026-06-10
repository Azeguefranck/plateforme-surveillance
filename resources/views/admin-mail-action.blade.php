<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Action administrateur — Surveillance</title>
<link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;}
::-webkit-scrollbar{width:6px;} ::-webkit-scrollbar-thumb{background:#1e3050;border-radius:4px;}
body{background:#060c1a;color:#d4dced;min-height:100vh;display:flex;flex-direction:column;}

.navbar{display:flex;justify-content:space-between;align-items:center;padding:15px 32px;
  background:rgba(8,15,30,.97);border-bottom:1px solid #182640;flex-wrap:wrap;gap:10px;}
.logo{font-size:19px;font-weight:bold;color:#2fa84f;letter-spacing:2px;text-decoration:none;}
.btn-panel{padding:8px 20px;border:1.5px solid #2fa84f;background:transparent;color:#2fa84f;
  border-radius:8px;font-size:13px;font-weight:bold;text-decoration:none;transition:.2s;}
.btn-panel:hover{background:#2fa84f;color:#060c1a;}

.page-center{flex:1;display:flex;justify-content:center;align-items:center;padding:30px 16px;}

.card{width:100%;max-width:500px;background:#0d1a2e;padding:40px 36px;border-radius:18px;
  border:1px solid #182640;box-shadow:0 8px 40px rgba(0,0,0,.5);text-align:center;}

.result-icon{width:90px;height:90px;border-radius:50%;display:flex;align-items:center;
  justify-content:center;margin:0 auto 24px;font-size:36px;}
.icon-valide{background:rgba(47,168,79,.13);border:2px solid rgba(47,168,79,.7);
  box-shadow:0 0 28px rgba(47,168,79,.35);color:#2fa84f;}
.icon-refuse{background:rgba(192,57,43,.13);border:2px solid rgba(192,57,43,.7);
  box-shadow:0 0 28px rgba(192,57,43,.35);color:#e74c3c;}
.icon-attente{background:rgba(217,119,6,.13);border:2px solid rgba(217,119,6,.7);
  box-shadow:0 0 28px rgba(217,119,6,.35);color:#f39c12;}
.icon-error{background:rgba(107,127,160,.1);border:2px solid rgba(107,127,160,.4);color:#6b7fa0;}

.result-title{font-size:22px;font-weight:bold;margin-bottom:8px;}
.title-valide{color:#2fa84f;}
.title-refuse{color:#e74c3c;}
.title-attente{color:#f39c12;}
.title-error{color:#6b7fa0;}

.result-msg{font-size:14px;color:#8090b0;margin-bottom:24px;line-height:1.7;}

.user-card{background:#08121f;border:1px solid #182640;border-radius:12px;
  padding:18px 20px;margin-bottom:24px;text-align:left;}
.user-card-row{display:flex;align-items:center;gap:10px;margin-bottom:10px;}
.user-card-row:last-child{margin-bottom:0;}
.user-card-row i{width:18px;text-align:center;color:#2fa84f;font-size:13px;}
.user-card-label{color:#6b7fa0;font-size:12px;min-width:90px;}
.user-card-value{color:#d4dced;font-size:13px;font-weight:bold;}

.badge{display:inline-block;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:bold;letter-spacing:.5px;}
.badge-valide{background:rgba(47,168,79,.15);color:#2fa84f;border:1px solid rgba(47,168,79,.3);}
.badge-refuse{background:rgba(192,57,43,.15);color:#e74c3c;border:1px solid rgba(192,57,43,.3);}
.badge-attente{background:rgba(217,119,6,.15);color:#f39c12;border:1px solid rgba(217,119,6,.3);}

.already-note{background:rgba(46,134,193,.08);border:1px solid rgba(46,134,193,.2);
  border-radius:8px;padding:10px 14px;margin-bottom:20px;font-size:13px;color:#4a9fc4;}

.btn-primary{display:inline-block;padding:13px 28px;background:#2fa84f;color:#060c1a;
  text-decoration:none;border-radius:10px;font-weight:bold;font-size:14px;
  transition:.2s;margin:6px;}
.btn-primary:hover{background:#249040;}
.btn-secondary{display:inline-block;padding:12px 24px;background:transparent;
  border:1.5px solid #2e4a7a;color:#4a9fc4;text-decoration:none;border-radius:10px;
  font-weight:bold;font-size:14px;transition:.2s;margin:6px;}
.btn-secondary:hover{background:rgba(46,74,122,.3);color:#7ec8e3;}

.footer-note{margin-top:24px;font-size:12px;color:#3a5070;}

@media(max-width:480px){
  .card{padding:28px 18px;}
  .navbar{padding:12px 16px;}
}
</style>
</head>
<body>

<nav class="navbar">
  <a class="logo" href="/accueil">SURVEILLANCE</a>
  <a href="/dashboard" class="btn-panel">
    <i class="fa-solid fa-users"></i> Panneau admin
  </a>
</nav>

<div class="page-center">
  <div class="card">

    @if(!$ok)
      <div class="result-icon icon-error">
        <i class="fa-solid fa-triangle-exclamation"></i>
      </div>
      <div class="result-title title-error">Lien invalide</div>
      <div class="result-msg">{{ $msg }}</div>
      <a href="/dashboard" class="btn-primary">
        <i class="fa-solid fa-users"></i> Gérer les utilisateurs
      </a>

    @else
      @if($action === 'valide')
        <div class="result-icon icon-valide"><i class="fa-solid fa-user-check"></i></div>
        <div class="result-title title-valide">Compte validé</div>
      @elseif($action === 'refuse')
        <div class="result-icon icon-refuse"><i class="fa-solid fa-user-xmark"></i></div>
        <div class="result-title title-refuse">Compte refusé</div>
      @else
        <div class="result-icon icon-attente"><i class="fa-solid fa-clock"></i></div>
        <div class="result-title title-attente">Mis en attente</div>
      @endif

      @if(!empty($already))
        <div class="already-note">
          <i class="fa-solid fa-circle-info"></i> {{ $msg }}
        </div>
      @else
        <div class="result-msg">{{ $msg }}</div>
      @endif

      @if($user)
      <div class="user-card">
        <div class="user-card-row">
          <i class="fa-solid fa-user"></i>
          <span class="user-card-label">Nom</span>
          <span class="user-card-value">{{ $user->nom ?? '' }} {{ $user->prenom ?? '' }}</span>
        </div>
        <div class="user-card-row">
          <i class="fa-solid fa-envelope"></i>
          <span class="user-card-label">Email</span>
          <span class="user-card-value">{{ $user->email ?? '' }}</span>
        </div>
        <div class="user-card-row">
          <i class="fa-solid fa-phone"></i>
          <span class="user-card-label">Téléphone</span>
          <span class="user-card-value">{{ $user->telephone ?? '—' }}</span>
        </div>
        <div class="user-card-row">
          <i class="fa-solid fa-briefcase"></i>
          <span class="user-card-label">Profession</span>
          <span class="user-card-value">{{ $user->profession ?? '—' }}</span>
        </div>
        <div class="user-card-row">
          <i class="fa-solid fa-circle-dot"></i>
          <span class="user-card-label">Statut</span>
          <span class="badge badge-{{ $action === 'valide' ? 'valide' : ($action === 'refuse' ? 'refuse' : 'attente') }}">
            {{ strtoupper($action === 'valide' ? 'VALIDÉ' : ($action === 'refuse' ? 'REFUSÉ' : 'EN ATTENTE')) }}
          </span>
        </div>
      </div>
      @endif

      <div>
        <a href="/dashboard" class="btn-primary">
          <i class="fa-solid fa-users"></i> Gérer les utilisateurs
        </a>
        <a href="/login" class="btn-secondary">
          <i class="fa-solid fa-arrow-right-to-bracket"></i> Se connecter
        </a>
      </div>

      <div class="footer-note">
        <i class="fa-solid fa-envelope-circle-check"></i>
        Un email de notification a été envoyé automatiquement à l'utilisateur.
      </div>
    @endif

  </div>
</div>

</body>
</html>
