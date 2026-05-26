<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="mobile-web-app-capable" content="no">
<meta name="apple-mobile-web-app-capable" content="no">
<title>Surveillance</title>
<link rel="stylesheet" href="/css/noselect.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>

/* ─── RESET ─── */
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif;}
/* user-select géré par /css/noselect.css */

/* ─── SCROLLBAR VERTICAL (fine, thème sombre) ─── */
::-webkit-scrollbar{width:6px;height:6px;}
::-webkit-scrollbar-track{background:transparent;}
::-webkit-scrollbar-thumb{background:#1e3050;border-radius:4px;}
::-webkit-scrollbar-thumb:hover{background:#2fa84f;}
::-webkit-scrollbar-corner{background:transparent;}
html{scrollbar-width:thin;scrollbar-color:#1e3050 transparent;}
/* Sidebar : pas de scrollbar visible */
.sidebar::-webkit-scrollbar{width:0;height:0;}
.sidebar{scrollbar-width:none;}

:root{
  --accent:#2fa84f;
  --accent-h:#249040;
  --accent-dim:rgba(47,168,79,0.12);
  --bg:#060c1a;
  --sidebar:#080f1e;
  --card:#0d1a2e;
  --border:#182640;
  --text:#d4dced;
  --muted:#6b7fa0;
  --danger:#c0392b;
  --info:#2e86c1;
  --sidebar-w:230px;
}

html,body{height:100%;overflow:hidden;}
body{background:var(--bg);color:var(--text);}

/* ─── OVERLAY MOBILE ─── */
.sb-overlay{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.65);
  z-index:199;
}
.sb-overlay.open{display:block;}

/* ─── SIDEBAR ─── */
.sidebar{
  width:var(--sidebar-w);
  background:var(--sidebar);
  padding:16px 12px;
  position:fixed;
  top:0;left:0;bottom:0;
  overflow-y:auto;
  z-index:200;
  transition:transform .28s ease;
  border-right:1px solid var(--border);
}

.logo{
  font-size:20px;
  font-weight:bold;
  color:var(--accent);
  margin-bottom:20px;
  padding:4px 6px;
  letter-spacing:1px;
  white-space:nowrap;
}

.sidebar a{
  display:flex;
  align-items:center;
  gap:10px;
  padding:9px 11px;
  margin-bottom:5px;
  background:#0c1828;
  border-radius:9px;
  text-decoration:none;
  color:var(--text);
  font-weight:bold;
  font-size:13px;
  transition:.2s;
  border:1px solid transparent;
}

.sidebar a:hover,
.sidebar a.active{
  background:#122035;
  border-color:var(--accent);
  color:var(--accent);
}

.sidebar a i{
  width:16px;
  text-align:center;
  font-size:13px;
  color:var(--accent);
  flex-shrink:0;
  opacity:.8;
}

.sidebar a:hover i,
.sidebar a.active i{opacity:1;}

/* ─── MAIN ─── */
.wrapper{display:flex;width:100%;height:100vh;overflow:hidden;}

.main{
  margin-left:var(--sidebar-w);
  width:calc(100% - var(--sidebar-w));
  padding:16px 20px;
  height:100vh;
  overflow-y:auto;
  overflow-x:hidden;
}

/* ─── TOPBAR ─── */
.topbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  background:var(--card);
  padding:11px 16px;
  border-radius:10px;
  margin-bottom:16px;
  border:1px solid var(--border);
  flex-wrap:wrap;
  gap:8px;
}

.topbar-left{
  display:flex;
  align-items:center;
  gap:10px;
}

.hamburger{
  display:none;
  background:none;
  border:none;
  color:var(--text);
  font-size:20px;
  cursor:pointer;
  padding:4px 7px;
  border-radius:6px;
  transition:.2s;
}
.hamburger:hover{color:var(--accent);}

.datetime{
  font-size:14px;
  font-weight:bold;
  color:#4a9fc4;
}

.logout{
  background:var(--danger);
  padding:8px 14px;
  border:none;
  border-radius:8px;
  color:white;
  font-weight:bold;
  cursor:pointer;
  text-decoration:none;
  display:inline-flex;
  align-items:center;
  gap:7px;
  font-size:13px;
  transition:.2s;
}
.logout:hover{background:#a93226;color:white;}

/* ─── RESPONSIVE ─── */
@media(max-width:900px){
  .sidebar{transform:translateX(-100%);}
  .sidebar.open{transform:translateX(0);}
  .main{margin-left:0;width:100%;padding:12px 14px;height:100vh;}
  .hamburger{display:block;}
}

@media(max-width:480px){
  .topbar{padding:9px 12px;}
  .datetime{font-size:12px;}
  .logout{padding:7px 11px;font-size:12px;}
  .logo{font-size:18px;}
}

/* ─── TABLEAUX RESPONSIVE (scroll horizontal sur petits écrans) ─── */
.table-wrap{overflow-x:auto !important;}
.tbl-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch;}
.tbl{min-width:500px;}

/* ─── RESPONSIVE LARGEUR (<600px) ─── */
@media(max-width:600px){
  .main{padding:10px;}
  .topbar{padding:8px 10px;gap:6px;}
  .logout{padding:6px 10px;font-size:11px;gap:5px;}
  .datetime{font-size:11px;}
}
/* ─── RESPONSIVE LARGEUR (<380px) ─── */
@media(max-width:380px){
  .main{padding:8px;}
  .logout i~*{display:none;}
  .datetime{font-size:10px;}
}

/* ─── RESPONSIVE HAUTEUR ─── */
@media(max-height:600px){
  .sidebar{padding:10px 8px;}
  .sidebar a{padding:7px 9px;font-size:12px;margin-bottom:3px;}
  .logo{font-size:17px;margin-bottom:12px;}
  .topbar{padding:8px 14px;margin-bottom:10px;}
}
@media(max-height:480px){
  .sidebar a{padding:5px 7px;margin-bottom:2px;}
  .logo{margin-bottom:6px;font-size:15px;}
  .topbar{margin-bottom:8px;}
}

/* ─── CYBERCONFIRM MODAL ─── */
#cc-overlay{
  display:none;position:fixed;inset:0;z-index:99999;
  background:rgba(4,8,18,0.78);
  backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
  align-items:center;justify-content:center;
}
#cc-overlay.cc-open{display:flex;}
#cc-modal{
  background:rgba(10,20,38,0.97);
  border:1px solid rgba(47,168,79,0.2);
  border-radius:18px;padding:36px 30px 28px;
  max-width:420px;width:90%;text-align:center;
  box-shadow:0 0 60px rgba(0,0,0,.7),0 0 0 1px rgba(47,168,79,0.08),inset 0 1px 0 rgba(255,255,255,0.04);
  animation:ccZoomIn .24s cubic-bezier(.34,1.28,.64,1) forwards;
}
#cc-icon-wrap{
  width:70px;height:70px;border-radius:50%;margin:0 auto 20px;
  display:flex;align-items:center;justify-content:center;font-size:28px;
}
#cc-icon-wrap.cc-danger{background:rgba(192,57,43,0.13);border:2px solid rgba(192,57,43,0.7);box-shadow:0 0 22px rgba(192,57,43,0.35);}
#cc-icon-wrap.cc-warning{background:rgba(230,126,34,0.13);border:2px solid rgba(230,126,34,0.7);box-shadow:0 0 22px rgba(230,126,34,0.35);}
#cc-icon-wrap.cc-success{background:rgba(47,168,79,0.13);border:2px solid rgba(47,168,79,0.7);box-shadow:0 0 22px rgba(47,168,79,0.35);}
#cc-icon-wrap.cc-info{background:rgba(46,134,193,0.13);border:2px solid rgba(46,134,193,0.7);box-shadow:0 0 22px rgba(46,134,193,0.35);}
#cc-icon-wrap.cc-danger #cc-icon{color:#e74c3c;}
#cc-icon-wrap.cc-warning #cc-icon{color:#f39c12;}
#cc-icon-wrap.cc-success #cc-icon{color:#2fa84f;}
#cc-icon-wrap.cc-info #cc-icon{color:#3498db;}
#cc-title{font-size:18px;font-weight:bold;color:#d4dced;margin-bottom:10px;line-height:1.3;}
#cc-message{font-size:14px;color:#8090b0;margin-bottom:28px;line-height:1.6;}
#cc-buttons{display:flex;gap:12px;justify-content:center;}
#cc-cancel{
  flex:1;padding:12px 16px;border-radius:10px;cursor:pointer;font-size:14px;font-weight:bold;
  background:rgba(20,40,72,0.5);border:1.5px solid #2e4a7a;color:#4a9fc4;transition:.2s;
}
#cc-cancel:hover{background:rgba(30,60,100,0.7);border-color:#4a9fc4;color:#7ec8e3;}
#cc-confirm{
  flex:1;padding:12px 16px;border-radius:10px;cursor:pointer;font-size:14px;font-weight:bold;
  border:none;color:#fff;transition:.2s;
}
#cc-confirm.cc-danger{background:linear-gradient(135deg,#c0392b,#a93226);box-shadow:0 0 16px rgba(192,57,43,0.45);}
#cc-confirm.cc-danger:hover{background:linear-gradient(135deg,#e74c3c,#c0392b);box-shadow:0 0 28px rgba(192,57,43,0.65);}
#cc-confirm.cc-warning{background:linear-gradient(135deg,#e67e22,#d35400);box-shadow:0 0 16px rgba(230,126,34,0.45);}
#cc-confirm.cc-warning:hover{background:linear-gradient(135deg,#f39c12,#e67e22);box-shadow:0 0 28px rgba(230,126,34,0.65);}
#cc-confirm.cc-success{background:linear-gradient(135deg,#2fa84f,#219a40);box-shadow:0 0 16px rgba(47,168,79,0.45);}
#cc-confirm.cc-success:hover{background:linear-gradient(135deg,#3dbf5e,#2fa84f);box-shadow:0 0 28px rgba(47,168,79,0.65);}
#cc-confirm.cc-info{background:linear-gradient(135deg,#2e86c1,#1a6fa5);box-shadow:0 0 16px rgba(46,134,193,0.45);}
#cc-confirm.cc-info:hover{background:linear-gradient(135deg,#3498db,#2e86c1);box-shadow:0 0 28px rgba(46,134,193,0.65);}
@keyframes ccZoomIn{from{opacity:0;transform:scale(.86);}to{opacity:1;transform:scale(1);}}
@media(max-width:480px){
  #cc-modal{padding:24px 16px 20px;}
  #cc-buttons{flex-direction:column-reverse;}
  #cc-icon-wrap{width:56px;height:56px;font-size:22px;}
  #cc-title{font-size:16px;}
}

</style>
</head>
<body>

<div class="sb-overlay" id="sb-overlay" onclick="closeSidebar()"></div>

<div class="wrapper">

<div class="sidebar" id="sidebar">

<div class="logo">SURVEILLANCE</div>

<a href="/accueil"><i class="fa-solid fa-house"></i>Accueil</a>
<a href="/dashboard"><i class="fa-solid fa-gauge-high"></i>Dashboard</a>
<a href="/alertes"><i class="fa-solid fa-bell"></i>Alertes</a>
<a href="/historique"><i class="fa-solid fa-clock-rotate-left"></i>Historique</a>
<a href="/statistiques"><i class="fa-solid fa-chart-line"></i>Statistiques</a>
<a href="/sms"><i class="fa-solid fa-mobile-screen-button"></i>SMS/MAILS</a>
<a href="/anomalies"><i class="fa-solid fa-triangle-exclamation"></i>Anomalies</a>
<a href="/salles"><i class="fa-solid fa-server"></i>Salles Serveurs</a>
<a href="/serveurs"><i class="fa-solid fa-server"></i>Serveurs</a>
<a href="/parametres"><i class="fa-solid fa-gear"></i>Paramètres</a>
<a href="/rapports"><i class="fa-solid fa-file-lines"></i>Rapports</a>

</div>

<div class="main">

<div class="topbar">
  <div class="topbar-left">
    <button class="hamburger" onclick="toggleSidebar()" aria-label="Menu">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="datetime">
      <span id="date"></span> &nbsp;|&nbsp; <span id="heure"></span>
    </div>
  </div>
  <a href="/logout" class="logout" onclick="event.preventDefault();CyberConfirm.show({title:'Déconnexion',message:'Voulez-vous vraiment vous déconnecter ?',icon:'fa-solid fa-arrow-right-from-bracket',confirmText:'Déconnecter',confirmColor:'warning'}).then(ok=>{if(ok)window.location.href='/logout';})">
    <i class="fa-solid fa-arrow-right-from-bracket"></i> Déconnecter
  </a>
</div>

@yield('content')

</div>

</div>

<!-- CyberConfirm -->
<div id="cc-overlay" role="dialog" aria-modal="true" aria-labelledby="cc-title">
  <div id="cc-modal">
    <div id="cc-icon-wrap"><i id="cc-icon"></i></div>
    <div id="cc-title"></div>
    <div id="cc-message"></div>
    <div id="cc-buttons">
      <button id="cc-cancel" type="button"></button>
      <button id="cc-confirm" type="button"></button>
    </div>
  </div>
</div>

<script>
function updateDateTime(){
  const now=new Date();
  document.getElementById('heure').textContent=now.toLocaleTimeString('fr-FR');
  document.getElementById('date').textContent=now.toLocaleDateString('fr-FR');
}
setInterval(updateDateTime,1000);
updateDateTime();

window.addEventListener('beforeinstallprompt',function(e){e.preventDefault();});

function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sb-overlay').classList.toggle('open');
}
function closeSidebar(){
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sb-overlay').classList.remove('open');
}

// Lien actif dans la sidebar
(function(){
  const p=window.location.pathname;
  document.querySelectorAll('.sidebar a').forEach(a=>{
    if(a.getAttribute('href')===p) a.classList.add('active');
  });
})();

// ── Toast global ──
window.showToast=function(msg,type='success',dur=3500){
  let el=document.getElementById('global-toast');
  if(!el){
    el=document.createElement('div');
    el.id='global-toast';
    el.style.cssText='position:fixed;bottom:24px;right:24px;z-index:9999;padding:13px 20px;border-radius:10px;font-size:14px;font-weight:bold;max-width:340px;box-shadow:0 4px 20px rgba(0,0,0,.5);transition:opacity .3s;pointer-events:none;';
    document.body.appendChild(el);
  }
  el.textContent=msg;
  const colors={success:{bg:'rgba(47,168,79,.15)',border:'#2fa84f',color:'#2fa84f'},error:{bg:'rgba(192,57,43,.15)',border:'#c0392b',color:'#c0392b'},info:{bg:'rgba(46,134,193,.15)',border:'#2e86c1',color:'#2e86c1'}};
  const c=colors[type]||colors.info;
  el.style.background=c.bg; el.style.border='1px solid '+c.border; el.style.color=c.color; el.style.opacity='1'; el.style.display='block';
  clearTimeout(el._t);
  el._t=setTimeout(()=>{el.style.opacity='0';setTimeout(()=>el.style.display='none',300);},dur);
};

// CSRF token pour les requêtes AJAX
window.getCsrfToken=function(){
  const m=document.cookie.match(/XSRF-TOKEN=([^;]+)/);
  return m?decodeURIComponent(m[1]):'';
};

// ── CyberConfirm ──
window.CyberConfirm=(function(){
  const ov=document.getElementById('cc-overlay');
  const iconWrap=document.getElementById('cc-icon-wrap');
  const iconEl=document.getElementById('cc-icon');
  const titleEl=document.getElementById('cc-title');
  const msgEl=document.getElementById('cc-message');
  const btnCancel=document.getElementById('cc-cancel');
  const btnConfirm=document.getElementById('cc-confirm');
  let _res=null;
  function hide(val){
    ov.classList.remove('cc-open');
    if(_res){_res(val);_res=null;}
  }
  ov.addEventListener('click',function(e){if(e.target===ov)hide(false);});
  document.addEventListener('keydown',function(e){if(e.key==='Escape'&&ov.classList.contains('cc-open'))hide(false);});
  btnCancel.addEventListener('click',function(){hide(false);});
  btnConfirm.addEventListener('click',function(){hide(true);});
  return{
    show:function(opts){
      const color=opts.confirmColor||'danger';
      iconWrap.className='cc-'+color;
      iconEl.className=opts.icon||'fa-solid fa-circle-exclamation';
      titleEl.textContent=opts.title||'Confirmation';
      msgEl.textContent=opts.message||'';
      btnCancel.innerHTML='<i class="fa-solid fa-xmark"></i> '+(opts.cancelText||'Annuler');
      btnConfirm.innerHTML='<i class="fa-solid fa-check"></i> '+(opts.confirmText||'Confirmer');
      btnConfirm.className='cc-'+color;
      ov.classList.add('cc-open');
      setTimeout(()=>btnConfirm.focus(),80);
      return new Promise(function(resolve){_res=resolve;});
    }
  };
})();
</script>

</body>
</html>
