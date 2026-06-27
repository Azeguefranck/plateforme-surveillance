<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>SUPSERVER — Surveillance des salles serveurs</title>
<link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial, Helvetica, sans-serif;
user-select:none;
-webkit-user-select:none;
-moz-user-select:none;
overflow-wrap:break-word;
word-break:break-word;
}
input,textarea,select,[contenteditable]{
user-select:text;
-webkit-user-select:text;
-moz-user-select:text;
cursor:auto;
}

body{
background:#f4f6f9;
color:#1a2340;
overflow-x:hidden;
}

.wrapper{
display:flex;
width:100%;
min-height:100vh;
}

.sidebar{
width:240px;
background:#1e2d4a;
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
scrollbar-color:#2d4068 #172038;
box-shadow:2px 0 16px rgba(0,0,0,.12);
}
.sidebar::-webkit-scrollbar{width:4px}
.sidebar::-webkit-scrollbar-track{background:#172038}
.sidebar::-webkit-scrollbar-thumb{background:#2d4068;border-radius:2px}

.logo{
font-size:14px;
font-weight:900;
color:#fff;
margin-bottom:18px;
letter-spacing:1px;
line-height:1.35;
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
background:transparent;
border-radius:10px;
text-decoration:none;
color:#a8bbd8;
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
color:#60a5fa;
flex-shrink:0;
}
.sidebar a:hover{
background:rgba(255,255,255,.08);
border-color:rgba(255,255,255,.1);
color:#fff;
}
.sidebar a.active{
background:rgba(96,165,250,.15);
border-color:rgba(96,165,250,.35);
color:#60a5fa;
}

.main{
margin-left:240px;
width:calc(100% - 240px);
padding:20px;
min-width:0;
}

#_pbar{
  position:fixed;top:0;left:0;height:3px;width:0%;z-index:99999;
  background:linear-gradient(90deg,#3b82f6,#60efff);
  transition:width .25s ease,opacity .3s ease;
  opacity:0;box-shadow:0 0 8px rgba(59,130,246,.6);
}
#_ploader{
  display:none;position:fixed;inset:0;z-index:99998;
  pointer-events:none;background:rgba(244,246,249,.35);
}
#_ploader.on{display:block}

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
background:#fff;
padding:12px 16px;
border-radius:12px;
margin-bottom:20px;
flex-wrap:wrap;
gap:10px;
border:1px solid #e2e8f0;
box-shadow:0 2px 8px rgba(0,0,0,.06);
}

.topbar-left{
display:flex;
align-items:center;
gap:12px;
}

.datetime{
font-size:16px;
font-weight:bold;
color:#1e2d4a;
}

.logout{
background:#f1f5f9;
border:1.5px solid #e2e8f0;
padding:10px 16px;
border-radius:10px;
color:#64748b;
font-weight:600;
cursor:pointer;
font-size:13px;
display:flex;
align-items:center;
gap:7px;
transition:.2s;
}
.logout:hover{
background:#e2e8f0;
border-color:#cbd5e1;
color:#1e2d4a;
}

.menu-toggle{
display:none;
background:#f1f5f9;
border:1px solid #e2e8f0;
border-radius:8px;
padding:8px 10px;
color:#1e2d4a;
font-size:16px;
cursor:pointer;
align-items:center;
justify-content:center;
transition:.2s;
}
.menu-toggle:hover{background:#e2e8f0}

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

.sidebar-close{
display:none;
background:none;
border:none;
color:#8899cc;
font-size:18px;
cursor:pointer;
padding:2px 6px;
}

*{box-sizing:border-box}
img{max-width:100%;height:auto}
table{max-width:100%;width:100%}

p,span,td,th,li,h1,h2,h3,h4,h5,h6,label,
.alerte-msg,.alerte-time,.alerte-item,
.stat-label,.stat-num,.gauge-label,.gauge-val,.gauge-unit,
.detail-row,.detail-card,.scard,.table-card,
.user-name,.user-date,.user-cell,
.badge,.btn,.pg-title,.dash-title,.toolbar-title,
.sensor-row,.sname,.sval,.sunit,
.chart-title,.chart-meta,
.filter-bar,.export-bar,.export-btns,
.td-name,.td-ip,.td-actions{
  max-width:100%;
  overflow-wrap:break-word;
  word-break:break-word;
}

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
.stats-grid,.charts-grid,.charts-row,.gauges,.form-grid,.detail-grid,.stats-row{
  grid-template-columns:1fr!important;
  flex-direction:column!important;
}
}

@media(max-width:640px){
.topbar{padding:10px 12px;border-radius:8px}
.datetime{font-size:13px}
.logout{padding:8px 12px;font-size:12px}
.logout span{display:none}
.table-wrap,.card-body,[class*="table"]{overflow-x:auto;-webkit-overflow-scrolling:touch}
.stat-card,.gauge-card,.chart-card,.scard,.detail-card,.table-card,.alertes-card{
  width:100%!important;
  min-width:0!important;
  max-width:100%!important;
}
.toolbar,.filter-bar,.export-bar,.export-btns,.table-toolbar{
  flex-wrap:wrap!important;
  gap:8px!important;
}
.btn,.btn-neon,.btn-blue,.btn-red,.btn-green,.btn-gray,.btn-warn{
  font-size:12px!important;
  padding:7px 10px!important;
}
.badge{max-width:120px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.form-group,.form-grid{width:100%!important;grid-template-columns:1fr!important}
.form-actions{flex-wrap:wrap!important;gap:8px!important}
}

@media(max-width:400px){
.main{padding:8px}
.logo{font-size:16px}
.pg-title,.dash-title{font-size:15px!important}
.stat-num,.gauge-val{font-size:20px!important}
.datetime{font-size:11px}
}

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

<div id="_pbar"></div>
<div id="_ploader"></div>

<div class="wrapper">

<div class="sidebar" id="sidebar">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding:0 4px">
    <div class="logo"><span>Système de<br>Surveillance</span></div>
    <button class="sidebar-close" id="sidebarClose" title="Fermer"><i class="fa-solid fa-xmark"></i></button>
  </div>

  @php $userRole = session('user') ? (session('user')->role ?? '') : ''; @endphp

  <a href="/dashboard"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
  <a href="/accueil"><i class="fa-solid fa-house"></i> Accueil</a>
  <a href="/alertes"><i class="fa-solid fa-bell"></i> Alertes</a>
  <a href="/historique"><i class="fa-solid fa-clock-rotate-left"></i> Historique</a>
  <a href="/statistiques"><i class="fa-solid fa-chart-line"></i> Statistiques</a>
  <a href="/mails"><i class="fa-solid fa-envelope"></i> Mails</a>
  <a href="/anomalies"><i class="fa-solid fa-triangle-exclamation"></i> Anomalies</a>
  <a href="/salles"><i class="fa-solid fa-warehouse"></i> Salles Serveurs</a>
  @if($userRole === 'admin' || $userRole === 'administrateur')
  <a href="/parametres"><i class="fa-solid fa-gear"></i> Paramètres</a>
  @endif
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

<div id="pjax-content">@yield('content')</div>

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


<div id="_toasts"></div>

<div id="_cdlg">
  <div class="_cbox" id="_cboxEl">
    <div class="_cbox-top"></div>
    <div class="_cbox-head">
      <div class="_cico" id="_cico"><i class="fa-solid fa-triangle-exclamation"></i></div>
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
function notify(msg, type, dur) {
    type = type||'s'; dur = dur||3600;
    var icons={s:'<i class="fa-solid fa-check"></i>',e:'<i class="fa-solid fa-xmark"></i>',w:'<i class="fa-solid fa-triangle-exclamation"></i>',i:'<i class="fa-solid fa-circle-info"></i>'};
    var t=document.createElement('div');
    t.className='_t '+type;
    t.innerHTML='<span>'+icons[type]+'</span><span style="flex:1">'+msg+'</span><span onclick="_dismiss(this.parentNode)" style="opacity:.45;font-size:17px;margin-left:4px;line-height:1">×</span>';
    t.onclick=function(e){if(e.target!==t.lastElementChild)_dismiss(t)};
    document.getElementById('_toasts').prepend(t);
    setTimeout(function(){_dismiss(t);},dur);
    return t;
}
function _dismiss(el){if(!el||el.classList.contains('out'))return;el.classList.add('out');setTimeout(function(){if(el.parentNode)el.remove();},230);}

function btnLoad(btn,on){
    if(!btn)return;
    if(on===false){if(btn._orig!==undefined){btn.innerHTML=btn._orig;btn.disabled=false;btn.style.opacity='';}return;}
    btn._orig=btn.innerHTML;
    btn.innerHTML='<span class="_spin-ico"></span> Chargement...';
    btn.disabled=true; btn.style.opacity='.72';
}

function csrfFetch(url,opts){
    opts=opts||{};
    var tok=(document.querySelector('meta[name="csrf-token"]')||{}).content||'';
    var hdrs=Object.assign({'X-CSRF-TOKEN':tok,'Content-Type':'application/json','Accept':'application/json'},opts.headers||{});
    return fetch(url,Object.assign({},opts,{headers:hdrs}));
}

var _cdlgBusy = false;
function confirmDlg(title, msg, opts) {
    return new Promise(function(res) {
        if (_cdlgBusy) { res(false); return; }
        _cdlgBusy = true;
        opts = opts || {};
        var type = opts.type || 'danger';
        var icoMap = {danger:'<i class="fa-solid fa-trash"></i>',warning:'<i class="fa-solid fa-triangle-exclamation"></i>',success:'<i class="fa-solid fa-circle-check" style="color:#33ff88"></i>',info:'<i class="fa-solid fa-circle-info"></i>',logout:'<i class="fa-solid fa-lock"></i>',user:'<i class="fa-solid fa-user"></i>',reset:'<i class="fa-solid fa-rotate"></i>',stop:'<i class="fa-solid fa-stop"></i>',valide:'<i class="fa-solid fa-check"></i>',refuse:'<i class="fa-solid fa-xmark"></i>',bloque:'<i class="fa-solid fa-ban"></i>'};
        var clrMap = {danger:'#ff5733',warning:'#ffd633',success:'#33ff88',info:'#33b5ff',logout:'#ff9933',user:'#ff5733',reset:'#ffd633',stop:'#ff5733',valide:'#33ff88',refuse:'#ffd633',bloque:'#ff5733'};
        var rgbMap = {danger:'255,87,51',warning:'255,214,51',success:'51,255,136',info:'51,181,255',logout:'255,153,51',user:'255,87,51',reset:'255,214,51',stop:'255,87,51',valide:'51,255,136',refuse:'255,214,51',bloque:'255,87,51'};
        var icon = opts.icon  || icoMap[type] || '<i class="fa-solid fa-triangle-exclamation"></i>';
        var clr  = clrMap[type] || clrMap.danger;
        var rgb  = rgbMap[type] || rgbMap.danger;
        var box  = document.getElementById('_cboxEl');
        box.style.setProperty('--dc', clr);
        box.style.setProperty('--dr', rgb);
        document.getElementById('_cico').innerHTML   = icon;
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
  var cur=window.location.pathname;
  document.querySelectorAll('.sidebar a').forEach(function(a){
    if(a.getAttribute('href')===cur) a.classList.add('active');
  });
})();

function doLogout() {
    confirmDlg(
        'Se déconnecter ?',
        'Vous allez quitter la plateforme de surveillance des salles serveurs. Votre session active sera fermée.',
        {type:'logout', icon:'<i class="fa-solid fa-lock"></i>', confirmText:'Se déconnecter', cancelText:'Rester connecté'}
    ).then(function(ok) {
        if (ok) {
            notify('Déconnexion en cours…', 'w', 2000);
            setTimeout(function(){
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/logout';
                var tok = document.createElement('input');
                tok.type = 'hidden';
                tok.name = '_token';
                tok.value = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
                form.appendChild(tok);
                document.body.appendChild(form);
                form.submit();
            }, 700);
        }
    });
}
</script>

<script>
(function () {
  'use strict';

  var CACHE_TTL = 60000;
  var _cache    = {};
  var _inflight = {};

  var _pi = [], _origSI = window.setInterval;
  window.setInterval = function () {
    var id = _origSI.apply(window, arguments);
    _pi.push(id);
    return id;
  };
  window.__pjaxAborts = window.__pjaxAborts || [];
  var _origFetch = window.fetch;
  window.fetch = function (url, opts) {
    if (typeof url === 'string' && (url.indexOf('mesures-live') !== -1 || url.indexOf('live-data') !== -1)) {
      var ctrl = new AbortController();
      window.__pjaxAborts.push(ctrl);
      opts = Object.assign({}, opts || {}, { signal: ctrl.signal });
    }
    return _origFetch.apply(window, [url, opts]);
  };
  function _clearPI() {
    _pi.forEach(clearInterval);
    _pi = [];
    (window.__pjaxAborts || []).forEach(function (c) { try { c.abort(); } catch(e){} });
    window.__pjaxAborts = [];
  }

  var _bar    = document.getElementById('_pbar');
  var _loader = document.getElementById('_ploader');
  function _showBar() {
    if (_bar) {
      _bar.style.transition = 'none';
      _bar.style.width      = '0';
      _bar.style.opacity    = '1';
      void _bar.offsetWidth;
      _bar.style.transition = 'width .25s ease';
      _bar.style.width      = '60%';
    }
    if (_loader) _loader.classList.add('on');
  }
  function _hideBar() {
    if (_bar) {
      _bar.style.width = '100%';
      setTimeout(function () { _bar.style.opacity = '0'; _bar.style.width = '0'; }, 250);
    }
    if (_loader) _loader.classList.remove('on');
  }

  function _active(path) {
    document.querySelectorAll('.sidebar a').forEach(function (a) {
      a.classList.toggle('active', a.getAttribute('href') === path);
    });
  }

  function _applyStyles(box) {
    document.querySelectorAll('style[data-pjax]').forEach(function (s) { s.remove(); });
    box.querySelectorAll('style').forEach(function (old) {
      var nw = document.createElement('style');
      nw.setAttribute('data-pjax', '1');
      nw.textContent = old.textContent;
      document.head.appendChild(nw);
      old.remove();
    });
  }

  function _exec(box) {
    box.querySelectorAll('script').forEach(function (old) {
      var nw = document.createElement('script');
      Array.prototype.forEach.call(old.attributes, function (a) { nw.setAttribute(a.name, a.value); });
      nw.textContent = old.textContent;
      old.parentNode.replaceChild(nw, old);
    });
  }

  function _isInternal(href) {
    if (!href || href === '#' || href.charAt(0) === '#') return false;
    if (/^(https?:)?\/\//.test(href))                   return false;
    if (/^(mailto:|tel:)/.test(href))                   return false;
    return true;
  }

  function _fetch(url) {
    var abs = new URL(url, location.origin).href;
    var now = Date.now();

    if (_cache[abs] && (now - _cache[abs].ts) < CACHE_TTL)
      return Promise.resolve(_cache[abs]);

    if (_inflight[abs]) return _inflight[abs];

    var p = fetch(abs, {
      headers: { 'X-PJAX': '1', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
    .then(function (r) {
      var finalUrl = r.url || abs;
      return r.text().then(function (html) {
        var entry = { html: html, finalUrl: finalUrl, ts: Date.now() };
        _cache[abs] = entry;
        delete _inflight[abs];
        return entry;
      });
    })
    .catch(function (err) {
      delete _inflight[abs];
      return Promise.reject(err);
    });

    _inflight[abs] = p;
    return p;
  }

  function _swap(entry, push) {
    var finalUrl = entry.finalUrl;

    if (finalUrl.indexOf('/login') !== -1) { location.href = '/login'; return; }

    var doc    = new DOMParser().parseFromString(entry.html, 'text/html');
    var newBox = doc.getElementById('pjax-content');
    var curBox = document.getElementById('pjax-content');
    if (!newBox || !curBox) { location.href = finalUrl; return; }

    _applyStyles(newBox);
    curBox.innerHTML = newBox.innerHTML;
    _exec(curBox);

    var path = new URL(finalUrl, location.origin).pathname;
    if (push !== false) history.pushState({ pjax: finalUrl }, '', finalUrl);
    _active(path);
    window.scrollTo(0, 0);
    var t = doc.querySelector('title');
    if (t) document.title = t.textContent;
  }

  function navigate(url, push) {
    var abs = new URL(url, location.origin).href;
    var now = Date.now();

    _clearPI();

    if (_cache[abs] && (now - _cache[abs].ts) < CACHE_TTL) {
      _swap(_cache[abs], push);
      _hideBar();
      return;
    }

    if (_inflight[abs]) {
      _showBar();
      _inflight[abs].then(function (entry) { _swap(entry, push); _hideBar(); })
                    .catch(function () { _hideBar(); location.href = url; });
      return;
    }

    _showBar();
    _fetch(abs)
      .then(function (entry) { _swap(entry, push); _hideBar(); })
      .catch(function () { _hideBar(); location.href = url; });
  }

  document.addEventListener('mouseover', function (e) {
    var a = e.target.closest('a');
    if (!a) return;
    var href = a.getAttribute('href');
    if (!_isInternal(href) || a.target === '_blank' || a.download || a.hasAttribute('data-no-pjax')) return;
    _fetch(href);
  }, true);

  document.addEventListener('click', function (e) {
    var a = e.target.closest('a');
    if (!a) return;
    var href = a.getAttribute('href');
    if (!_isInternal(href)) return;
    if (a.target === '_blank' || a.download || a.hasAttribute('data-no-pjax')) return;
    var dest = new URL(href, location.origin);
    if (dest.pathname === location.pathname && dest.search === location.search) return;
    e.preventDefault();
    navigate(href);
  }, true);

  window.addEventListener('popstate', function () {
    navigate(location.href, false);
  });

  document.addEventListener('submit', function () {
    _cache = {};
  }, true);

  history.replaceState({ pjax: location.href }, '', location.href);
  _active(location.pathname);

  setTimeout(function () {
    document.querySelectorAll('.sidebar a').forEach(function (a) {
      var href = a.getAttribute('href');
      if (_isInternal(href)) _fetch(href);
    });
  }, 800);

})();
</script>
</body>
</html>
