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
#_cdlg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.72);z-index:10000;align-items:center;justify-content:center}
#_cdlg.open{display:flex}
._cbox{background:#090f22;border:1px solid #1e2f5a;border-radius:14px;padding:28px 24px;text-align:center;max-width:350px;width:92%}
._cbox h4{color:#fff;font-size:16px;margin:0 0 10px}
._cbox p{color:#aaa;font-size:13px;margin:0 0 22px}
._cbtns{display:flex;gap:10px;justify-content:center}
._cok{background:#ff5733;border:none;color:#fff;padding:9px 22px;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px}
._cok:hover{background:#e83d1e}
._cno{background:transparent;border:1px solid #1e2f5a;color:#aaa;padding:9px 22px;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px}
._cno:hover{border-color:#33b5ff;color:#33b5ff}

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

<button class="logout" onclick="window.location.href='/login'">
Se Déconnecter
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
  <div class="_cbox">
    <h4 id="_ctitle">Confirmer</h4>
    <p  id="_cmsg">Êtes-vous sûr de vouloir effectuer cette action ?</p>
    <div class="_cbtns">
      <button class="_cno"  id="_cno">Annuler</button>
      <button class="_cok"  id="_cok">Confirmer</button>
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
function confirmDlg(title,msg){
    return new Promise(function(res){
        var dlg=document.getElementById('_cdlg');
        document.getElementById('_ctitle').textContent=title||'Confirmer';
        document.getElementById('_cmsg').textContent=msg||'Êtes-vous sûr ?';
        dlg.classList.add('open');
        var ok=document.getElementById('_cok'),no=document.getElementById('_cno');
        function done(v){dlg.classList.remove('open');ok.onclick=no.onclick=null;res(v);}
        ok.onclick=function(){done(true);};
        no.onclick=function(){done(false);};
        dlg.onclick=function(e){if(e.target===dlg)done(false);};
    });
}
</script>
</body>
</html>
