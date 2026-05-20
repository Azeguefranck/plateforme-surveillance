<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Surveillance</title>

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
padding:20px;
position:fixed;
top:0;
left:0;
bottom:0;
overflow-y:auto;
}

.logo{
font-size:26px;
font-weight:bold;
color:#39ff14;
margin-bottom:25px;
white-space:nowrap;
}

.sidebar a{
display:block;
padding:14px;
margin-bottom:10px;
background:#111c3d;
border-radius:12px;
text-decoration:none;
color:white;
font-weight:bold;
transition:0.3s;
}

.sidebar a:hover{
background:#1f2d5e;
}

.main{
margin-left:240px;
width:calc(100% - 240px);
padding:20px;
}

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
background:#111c3d;
padding:15px;
border-radius:12px;
margin-bottom:20px;
flex-wrap:wrap;
}

.datetime{
font-size:18px;
font-weight:bold;
color:#00ffcc;
}

.logout{
background:red;
padding:12px 18px;
border:none;
border-radius:10px;
color:white;
font-weight:bold;
cursor:pointer;
}

.logout:hover{
background:#d10000;
}

@media(max-width:900px){
.sidebar{position:relative;width:100%;}
.main{margin-left:0;width:100%;}
.wrapper{flex-direction:column;}
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

<div class="wrapper">

<div class="sidebar">

<div class="logo">
SURVEILLANCE
</div>

<a href="/dashboard">Dashboard</a>
<a href="/accueil">Accueil</a>
<a href="/surveillance">Surveillance</a>
<a href="/alertes">Alertes</a>
<a href="/historique">Historique</a>
<a href="/statistiques">Statistiques</a>
<a href="/sms">SMS GSM</a>
<a href="/anomalies">Anomalies</a>
<a href="/profil">Mon Profil</a>
<a href="/users">Utilisateurs</a>
<a href="/cameras-ip">Caméras IP</a>
<a href="/salles">Salles Serveurs</a>
<a href="/serveurs">Serveurs</a>
<a href="/parametres">Paramètres</a>
<a href="/rapports">Rapports</a>

</div>

<div class="main">

<div class="topbar">

<div class="datetime">
<span id="date"></span> |
<span id="heure"></span>
</div>

<button class="logout" onclick="doLogout()">
&#128274; Se Déconnecter
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
