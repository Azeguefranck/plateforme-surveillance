<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

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

.sidebar{
position:relative;
width:100%;
}

.main{
margin-left:0;
width:100%;
}

.wrapper{
flex-direction:column;
}

}

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

</body>
</html>
