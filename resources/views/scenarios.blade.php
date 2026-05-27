<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>scenarios</title>

<style>

body{
margin:0;
background:#050b16;
font-family:Arial;
color:white;
}

.sidebar{
position:fixed;
left:0;
top:0;
width:250px;
height:100%;
background:#0f172a;
padding-top:20px;
}

.logo{
font-size:38px;
font-weight:bold;
color:#22c55e;
padding:20px;
}

.menu{
display:block;
padding:18px 30px;
color:white;
text-decoration:none;
font-size:22px;
}

.menu:hover{
background:#1e293b;
}

.content{
margin-left:260px;
padding:30px;
}

.box{
background:#111827;
padding:30px;
border-radius:20px;
margin-bottom:20px;
}

/* ── Responsive anti-overflow ── */
*{overflow-wrap:break-word;word-break:break-word}
img,video,iframe{max-width:100%;height:auto}
@media(max-width:640px){
body{overflow-x:hidden;padding:12px!important}
.box{padding:16px!important;border-radius:12px!important}
table{display:block;overflow-x:auto;-webkit-overflow-scrolling:touch}
input,select,textarea{max-width:100%!important;width:100%!important}
}
@media(max-width:400px){
h1,h2,h3{font-size:clamp(15px,5vw,22px)!important}
}

</style>
</head>

<body>

<div class="sidebar">

<div class="logo">SURVEILLANCE</div>

<a href="/dashboard" class="menu">Dashboard</a>
<a href="/alertes" class="menu">Alertes</a>
<a href="/historique" class="menu">Historique</a>
<a href="/scenarios" class="menu">Scénarios</a>
<a href="/users" class="menu">Utilisateurs</a>
<a href="/rapports" class="menu">Rapports</a>
<a href="/parametres" class="menu">Paramètres</a>

</div>

<div class="content">

<h1>PAGE SCENARIOS</h1>

<div class="box">

<h2>Module opérationnel</h2>

<p>Plateforme active et connectée</p>

</div>

</div>

</body>
</html>
