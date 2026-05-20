<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Surveillance</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial, Helvetica, sans-serif;
}

body{
background:#050816;
color:white;
overflow-x:hidden;
}

.wrapper{
display:flex;
width:100%;
min-height:100vh;
}

.sidebar{
width:240px;
background:#081126;
padding:16px 12px;
position:fixed;
top:0;
left:0;
bottom:0;
overflow-y:auto;
overflow-x:hidden;
z-index:999;
transition:left .28s cubic-bezier(.4,0,.2,1);
scrollbar-width:thin;
scrollbar-color:#1e2f5a #040e22;
}
.sidebar::-webkit-scrollbar{width:4px}
.sidebar::-webkit-scrollbar-track{background:#040e22}
.sidebar::-webkit-scrollbar-thumb{background:#1e2f5a;border-radius:2px}

.logo{
font-size:20px;
font-weight:900;
color:#39ff14;
margin-bottom:18px;
white-space:nowrap;
letter-spacing:2px;
display:flex;
align-items:center;
gap:8px;
padding:6px 4px;
}

.sidebar a{
display:flex;
align-items:center;
gap:10px;
padding:11px 12px;
margin-bottom:4px;
background:#111c3d;
border-radius:10px;
text-decoration:none;
color:#c7d5f0;
font-weight:600;
font-size:13px;
transition:.2s;
border:1px solid transparent;
white-space:nowrap;
overflow:hidden;
}
.sidebar a i{
width:16px;
text-align:center;
font-size:14px;
color:#39ff14;
flex-shrink:0;
}
.sidebar a:hover{
background:#1f2d5e;
border-color:#1e3a6e;
color:#fff;
}
.sidebar a.active{
background:rgba(57,255,20,.08);
border-color:rgba(57,255,20,.25);
color:#39ff14;
}

.main{
margin-left:240px;
width:calc(100% - 240px);
padding:20px;
min-width:0;
}

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
background:#111c3d;
padding:12px 16px;
border-radius:12px;
margin-bottom:20px;
flex-wrap:wrap;
gap:10px;
}

.topbar-left{
display:flex;
align-items:center;
gap:12px;
}

.datetime{
font-size:16px;
font-weight:bold;
color:#00ffcc;
}

.logout{
background:#1a0808;
border:1.5px solid #ff3333;
padding:10px 16px;
border-radius:10px;
color:#ff5555;
font-weight:700;
cursor:pointer;
font-size:13px;
display:flex;
align-items:center;
gap:7px;
transition:.2s;
}
.logout:hover{
background:#ff3333;
color:#fff;
}

/* ── Hamburger button ── */
.menu-toggle{
display:none;
background:#111c3d;
border:1px solid #1e2f5a;
border-radius:8px;
padding:8px 10px;
color:#39ff14;
font-size:16px;
cursor:pointer;
align-items:center;
justify-content:center;
transition:.2s;
}
.menu-toggle:hover{background:#1f2d5e}

/* ── Sidebar overlay (mobile) ── */
.sidebar-overlay{
display:none;
position:fixed;
inset:0;
background:rgba(0,0,0,.65);
z-index:998;
backdrop-filter:blur(3px);
-webkit-backdrop-filter:blur(3px);
}
.sidebar-overlay.open{display:block}

/* ── Sidebar close button (mobile) ── */
.sidebar-close{
display:none;
background:none;
border:none;
color:#8899cc;
font-size:18px;
cursor:pointer;
padding:2px 6px;
}

/* ── Global responsive ── */
*{box-sizing:border-box}
img{max-width:100%;height:auto}
table{max-width:100%}

/* ── Responsive breakpoints ── */
@media(max-width:900px){
.sidebar{
  left:-260px;
  position:fixed;
  width:240px;
}
.sidebar.open{left:0}
.sidebar-close{display:block}
.main{margin-left:0;width:100%;padding:12px}
.wrapper{flex-direction:column}
.menu-toggle{display:flex}
}

@media(max-width:640px){
.topbar{padding:10px 12px;border-radius:8px}
.datetime{font-size:13px}
.logout{padding:8px 12px;font-size:12px}
.logout span{display:none}
/* Tables scroll on small screens */
.table-wrap,.card-body,[class*="table"]{overflow-x:auto}
}

@media(max-width:400px){
.main{padding:8px}
.logo{font-size:16px}
}

/* ── Global UI: toasts, spinners, confirm ─────── */
@keyframes _spin{to{transform:rotate(360deg)}}
@keyframes _tIn{from{transform:translateX(110%);opacity:0}to{transform:none;opacity:1}}
@keyframes _tOut{from{opacity:1;transform:none}to{opacity:0;transform:translateX(110%)}}
#_toasts{position:fixed;top:18px;right:18px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none}
._t{pointer-events:auto;min-width:230px;max-width:370px;padding:11px 14px;border-radius:10px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:9px;backdrop-filter:blur(14px);box-shadow:0 4px 24px rgba(0,0,0,.55);animation:_tIn .28s cubic-bezier(.21,1.02,.73,1) forwards;cursor:pointer;line-height:1.4}
._t.out{animation:_tOut .22s ease forwards}
._t.s{background:rgba(6,20,14,.92);border:1px solid #33ff88;color:#33ff88}
._t.e{background:rgba(22,6,6,.92);border:1px solid #ff5733;color:#ff5733}
._t.w{background:rgba(22,18,6,.92);border:1px solid #ffd633;color:#ffd633}
._t.i{background:rgba(6,14,22,.92);border:1px solid #33b5ff;color:#33b5ff}
._spin-ico{width:13px;height:13px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;display:inline-block;animation:_spin .65s linear infinite;flex-shrink:0}
@keyframes _dlgIn{from{opacity:0;transform:scale(.86) translateY(14px)}to{opacity:1;transform:none}}
@keyframes _dlgBg{from{opacity:0}to{opacity:1}}
@keyframes _icoFloat{0%,100%{transform:scale(1)}50%{transform:scale(1.1)}}
@keyframes _glowBar{0%,100%{opacity:.5}50%{opacity:1}}
#_cdlg{display:none;position:fixed;inset:0;background:rgba(2,5,18,.88);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);z-index:10000;align-items:center;justify-content:center;animation:_dlgBg .22s ease}
#_cdlg.open{display:flex}
._cbox{background:rgba(6,11,30,.98);border:1px solid rgba(255,255,255,.07);border-radius:20px;padding:0;text-align:center;max-width:420px;width:92%;box-shadow:0 0 0 1px rgba(255,255,255,.04),0 12px 80px rgba(0,0,0,.9),inset 0 1px 0 rgba(255,255,255,.05);animation:_dlgIn .32s cubic-bezier(.21,1.02,.73,1) forwards;position:relative;overflow:hidden}
._cbox::after{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 50% -20%,rgba(255,255,255,.03),transparent 60%);pointer-events:none}
._cbox-top{height:3px;background:linear-gradient(90deg,transparent 0%,var(--dc,#ff5733) 30%,var(--dc,#ff5733) 70%,transparent 100%);box-shadow:0 0 20px var(--dc,#ff5733);animation:_glowBar 2.5s ease infinite}
._cbox-head{padding:28px 28px 4px}
._cico{width:68px;height:68px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-size:30px;background:radial-gradient(circle,rgba(var(--dr,255,87,51),.16) 0%,transparent 70%);border:1.5px solid rgba(var(--dr,255,87,51),.3);box-shadow:0 0 18px rgba(var(--dr,255,87,51),.12);animation:_icoFloat 3.5s ease infinite}
._ctitl{color:#fff;font-size:14px;font-weight:800;letter-spacing:.9px;text-transform:uppercase;margin:0 0 10px}
._cmsg{color:#8899bb;font-size:13px;margin:0;line-height:1.65;padding:0 2px}
._cbox-body{padding:20px 28px 28px}
._cdivider{height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.06),transparent);margin:0 0 20px}
._cbtns{display:flex;gap:12px}
._cno{flex:1;background:rgba(18,30,68,.65);border:1px solid rgba(51,181,255,.2);color:#7788aa;padding:12px 18px;border-radius:11px;font-weight:700;cursor:pointer;font-size:13px;letter-spacing:.3px;transition:.2s}
._cno:hover{background:rgba(51,181,255,.12);border-color:rgba(51,181,255,.55);color:#33b5ff;transform:translateY(-1px)}
._cok{flex:1;background:rgba(var(--dr,255,87,51),.1);border:1px solid rgba(var(--dr,255,87,51),.35);color:var(--dc,#ff5733);padding:12px 18px;border-radius:11px;font-weight:700;cursor:pointer;font-size:13px;letter-spacing:.3px;transition:.2s}
._cok:hover{background:var(--dc,#ff5733);color:#fff;box-shadow:0 0 24px rgba(var(--dr,255,87,51),.5);transform:translateY(-1px)}
._cok:active,._cno:active{transform:scale(.95)!important;transition:transform .08s}

</style>

</head>

<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="wrapper">

<div class="sidebar" id="sidebar">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding:0 4px">
    <div class="logo"><i class="fa-solid fa-bolt"></i> SUPSERVER</div>
    <button class="sidebar-close" id="sidebarClose" title="Fermer"><i class="fa-solid fa-xmark"></i></button>
  </div>

  <a href="/dashboard"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
  <a href="/accueil"><i class="fa-solid fa-house"></i> Accueil</a>
  <a href="/surveillance"><i class="fa-solid fa-eye"></i> Surveillance</a>
  <a href="/alertes"><i class="fa-solid fa-bell"></i> Alertes</a>
  <a href="/historique"><i class="fa-solid fa-clock-rotate-left"></i> Historique</a>
  <a href="/statistiques"><i class="fa-solid fa-chart-line"></i> Statistiques</a>
  <a href="/sms"><i class="fa-solid fa-comment-sms"></i> SMS GSM</a>
  <a href="/anomalies"><i class="fa-solid fa-triangle-exclamation"></i> Anomalies</a>
  <a href="/profil"><i class="fa-solid fa-user"></i> Mon Profil</a>
  <a href="/utilisateurs"><i class="fa-solid fa-users"></i> Utilisateurs</a>
  <a href="/cameras-ip"><i class="fa-solid fa-video"></i> Caméras IP</a>
  <a href="/salles"><i class="fa-solid fa-building-server"></i> Salles Serveurs</a>
  <a href="/serveurs"><i class="fa-solid fa-server"></i> Serveurs</a>
  <a href="/parametres"><i class="fa-solid fa-gear"></i> Paramètres</a>
  <a href="/rapports"><i class="fa-solid fa-file-lines"></i> Rapports</a>
</div>

<div class="main">

<div class="topbar">

  <div class="topbar-left">
    <button class="menu-toggle" id="menuToggle" aria-label="Menu navigation"><i class="fa-solid fa-bars"></i></button>
    <div class="datetime">
      <i class="fa-regular fa-calendar" style="margin-right:4px;opacity:.6"></i><span id="date"></span>
      <span style="opacity:.4;margin:0 6px">|</span>
      <i class="fa-regular fa-clock" style="margin-right:4px;opacity:.6"></i><span id="heure"></span>
    </div>
  </div>

  <button class="logout" onclick="doLogout()">
    <i class="fa-solid fa-right-from-bracket"></i>
    <span>Se Déconnecter</span>
  </button>

</div>

@yield('content')

</div>

</div>

<script>

function updateDateTime(){

const now = new Date();

document.getElementById("heure").innerHTML =
now.toLocaleTimeString();

document.getElementById("date").innerHTML =
now.toLocaleDateString('fr-FR');

}

setInterval(updateDateTime,1000);

updateDateTime();

</script>


<!-- ── Global toasts ──────────────────────── -->
<div id="_toasts"></div>

<!-- ── Confirm dialog ────────────────────── -->
<div id="_cdlg">
  <div class="_cbox" id="_cboxEl">
    <div class="_cbox-top"></div>
    <div class="_cbox-head">
      <div class="_cico" id="_cico">⚠️</div>
      <div class="_ctitl" id="_ctitle">Confirmation requise</div>
      <p class="_cmsg" id="_cmsg">Êtes-vous sûr de vouloir effectuer cette action ?</p>
    </div>
    <div class="_cbox-body">
      <div class="_cdivider"></div>
      <div class="_cbtns">
        <button class="_cno" id="_cno">Annuler</button>
        <button class="_cok" id="_cok">Confirmer</button>
      </div>
    </div>
  </div>
</div>

<script>
/* ── Notifications toast ── */
function notify(msg, type, dur) {
    type = type||'s'; dur = dur||3600;
    var icons={s:'✓',e:'✗',w:'⚠',i:'ℹ'};
    var t=document.createElement('div');
    t.className='_t '+type;
    t.innerHTML='<span>'+icons[type]+'</span><span style="flex:1">'+msg+'</span><span onclick="_dismiss(this.parentNode)" style="opacity:.45;font-size:17px;margin-left:4px;line-height:1">×</span>';
    t.onclick=function(e){if(e.target!==t.lastElementChild)_dismiss(t)};
    document.getElementById('_toasts').prepend(t);
    setTimeout(function(){_dismiss(t);},dur);
    return t;
}
function _dismiss(el){if(!el||el.classList.contains('out'))return;el.classList.add('out');setTimeout(function(){if(el.parentNode)el.remove();},230);}

/* ── Button loader ── */
function btnLoad(btn,on){
    if(!btn)return;
    if(on===false){if(btn._orig!==undefined){btn.innerHTML=btn._orig;btn.disabled=false;btn.style.opacity='';}return;}
    btn._orig=btn.innerHTML;
    btn.innerHTML='<span class="_spin-ico"></span> Chargement...';
    btn.disabled=true; btn.style.opacity='.72';
}

/* ── CSRF fetch ── */
function csrfFetch(url,opts){
    opts=opts||{};
    var tok=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
    var hdrs=Object.assign({'X-CSRF-TOKEN':tok,'Content-Type':'application/json','Accept':'application/json'},opts.headers||{});
    return fetch(url,Object.assign({},opts,{headers:hdrs}));
}

/* ── Confirm dialog ── */
var _cdlgBusy = false;
function confirmDlg(title, msg, opts) {
    return new Promise(function(res) {
        if (_cdlgBusy) { res(false); return; }
        _cdlgBusy = true;
        opts = opts || {};
        var type = opts.type || 'danger';
        var icoMap = {danger:'🗑️',warning:'⚠️',success:'✅',info:'ℹ️',logout:'🔒',user:'👤',reset:'🔄',stop:'⏹️',valide:'✔️',refuse:'✗',bloque:'🚫'};
        var clrMap = {danger:'#ff5733',warning:'#ffd633',success:'#33ff88',info:'#33b5ff',logout:'#ff9933',user:'#ff5733',reset:'#ffd633',stop:'#ff5733',valide:'#33ff88',refuse:'#ffd633',bloque:'#ff5733'};
        var rgbMap = {danger:'255,87,51',warning:'255,214,51',success:'51,255,136',info:'51,181,255',logout:'255,153,51',user:'255,87,51',reset:'255,214,51',stop:'255,87,51',valide:'51,255,136',refuse:'255,214,51',bloque:'255,87,51'};
        var icon = opts.icon  || icoMap[type] || '⚠️';
        var clr  = clrMap[type] || clrMap.danger;
        var rgb  = rgbMap[type] || rgbMap.danger;
        var box  = document.getElementById('_cboxEl');
        box.style.setProperty('--dc', clr);
        box.style.setProperty('--dr', rgb);
        document.getElementById('_cico').textContent   = icon;
        document.getElementById('_ctitle').textContent = title || 'Confirmation requise';
        document.getElementById('_cmsg').textContent   = msg   || 'Êtes-vous sûr de vouloir effectuer cette action ?';
        document.getElementById('_cok').textContent    = opts.confirmText || 'Confirmer';
        document.getElementById('_cno').textContent    = opts.cancelText  || 'Annuler';
        var dlg = document.getElementById('_cdlg');
        dlg.classList.add('open');
        box.style.animation = 'none'; void box.offsetHeight; box.style.animation = '';
        setTimeout(function(){ var n=document.getElementById('_cno'); if(n) n.focus(); }, 80);
        var ok=document.getElementById('_cok'), no=document.getElementById('_cno');
        function done(v) {
            _cdlgBusy = false;
            dlg.classList.remove('open');
            ok.onclick = no.onclick = dlg.onclick = null;
            document.removeEventListener('keydown', _dlgKey);
            res(v);
        }
        function _dlgKey(e) {
            if (e.key === 'Escape') { e.preventDefault(); done(false); }
        }
        ok.onclick   = function(){ done(true);  };
        no.onclick   = function(){ done(false); };
        dlg.onclick  = function(e){ if(e.target===dlg) done(false); };
        document.addEventListener('keydown', _dlgKey);
    });
}

/* ── Hamburger / sidebar toggle ── */
(function(){
  var toggle=document.getElementById('menuToggle');
  var close=document.getElementById('sidebarClose');
  var overlay=document.getElementById('sidebarOverlay');
  var sidebar=document.getElementById('sidebar');
  function openSidebar(){sidebar.classList.add('open');overlay.classList.add('open');}
  function closeSidebar(){sidebar.classList.remove('open');overlay.classList.remove('open');}
  if(toggle) toggle.addEventListener('click',openSidebar);
  if(close)  close.addEventListener('click',closeSidebar);
  if(overlay) overlay.addEventListener('click',closeSidebar);
  // Mark active link
  var cur=window.location.pathname;
  document.querySelectorAll('.sidebar a').forEach(function(a){
    if(a.getAttribute('href')===cur) a.classList.add('active');
  });
})();

/* ── Logout ── */
function doLogout() {
    confirmDlg(
        'Se déconnecter ?',
        'Vous allez quitter la plateforme de surveillance des salles serveurs. Votre session active sera fermée.',
        {type:'logout', icon:'🔒', confirmText:'Se déconnecter', cancelText:'Rester connecté'}
    ).then(function(ok) {
        if (ok) {
            notify('Déconnexion en cours…', 'w', 2000);
            setTimeout(function(){ window.location.href='/login'; }, 700);
        }
    });
}
</script>
</body>
</html>
