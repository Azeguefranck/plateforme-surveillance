<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Rapports</title>

<style>

body{
margin:0;
font-family:Arial;
background:#050b16;
color:white;
}

.sidebar{
position:fixed;
width:250px;
height:100vh;
background:#0d1628;
padding:20px;
}

.logo{
font-size:38px;
font-weight:bold;
color:#39ff14;
margin-bottom:30px;
}

.menu a{
display:block;
padding:15px;
margin-bottom:10px;
background:#111827;
border-radius:10px;
text-decoration:none;
color:white;
}

.menu a:hover{
background:#2563eb;
}

.content{
margin-left:290px;
padding:30px;
}

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
}

.btn{
padding:12px 18px;
border:none;
border-radius:10px;
font-weight:bold;
cursor:pointer;
margin-right:10px;
}

.pdf{
background:#dc2626;
color:white;
}

.word{
background:#2563eb;
color:white;
}

.excel{
background:#16a34a;
color:white;
}

.update{
background:#f59e0b;
color:white;
}

table{
width:100%;
border-collapse:collapse;
background:#111827;
border-radius:15px;
overflow:hidden;
}

th{
background:#1f2937;
padding:15px;
}

td{
padding:15px;
border-bottom:1px solid #1f2937;
text-align:center;
}

.delete{
background:#ef4444;
padding:10px;
border:none;
border-radius:8px;
color:white;
cursor:pointer;
}

</style>
</head>

<body>

<div class="sidebar">

<div class="logo">SURVEILLANCE</div>

<div class="menu">

<a href="/dashboard">Dashboard</a>
<a href="/alertes">Alertes</a>
<a href="/historique">Historique</a>
<a href="/rapports">Rapports</a>
<a href="/parametres">Paramètres</a>

</div>

</div>

<div class="content">

<div class="topbar">

<h1>RAPPORTS SYSTÈME</h1>

<div>

<button class="btn pdf">PDF</button>

<button class="btn word">WORD</button>

<button class="btn excel">EXCEL</button>

<button class="btn update">METTRE À JOUR</button>

</div>

</div>

<table>

<tr>
<th>ID</th>
<th>Date</th>
<th>Heure</th>
<th>Type Alerte</th>
<th>Paramètre</th>
<th>Valeur</th>
<th>Action</th>
</tr>

<tr>
<td>1</td>
<td>17/05/2026</td>
<td>23:10</td>
<td>Critique</td>
<td>Température</td>
<td>45°C</td>
<td><button class="delete">Supprimer</button></td>
</tr>

<tr>
<td>2</td>
<td>17/05/2026</td>
<td>23:15</td>
<td>Moyenne</td>
<td>Gaz</td>
<td>300ppm</td>
<td><button class="delete">Supprimer</button></td>
</tr>

<tr>
<td>3</td>
<td>17/05/2026</td>
<td>23:20</td>
<td>Faible</td>
<td>Humidité</td>
<td>70%</td>
<td><button class="delete">Supprimer</button></td>
</tr>

</table>

</div>

</body>
</html>
