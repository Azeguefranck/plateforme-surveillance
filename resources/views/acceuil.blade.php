<!DOCTYPE html>
<html lang="fr">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Acceuil</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial;
}

body{
background:#050816;
color:white;
padding:40px;
}

.container{
max-width:1200px;
margin:auto;
}

h1{
font-size:50px;
color:#39ff14;
margin-bottom:20px;
}

.subtitle{
font-size:22px;
margin-bottom:40px;
color:#ccc;
}

.grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
gap:20px;
}

.card{
background:#111827;
padding:25px;
border-radius:20px;
transition:0.3s;
}

.card:hover{
transform:translateY(-5px);
}

.card h2{
margin-bottom:15px;
color:#39ff14;
}

.card p{
color:#ccc;
line-height:1.6;
}

.btn{
display:inline-block;
margin-top:40px;
padding:15px 30px;
background:#39ff14;
color:black;
text-decoration:none;
border-radius:10px;
font-weight:bold;
}

@media(max-width:900px){

h1{
font-size:35px;
}

.subtitle{
font-size:18px;
}

}

</style>

</head>

<body>

<div class="container">

<h1>PLATEFORME DE SURVEILLANCE</h1>

<p class="subtitle">
Système intelligent de supervision des salles serveurs en temps réel.
</p>

<div class="grid">

<div class="card">
<h2>Surveillance Temps Réel</h2>
<p>
Contrôle des températures, humidité, gaz, courant et puissance.
</p>
</div>

<div class="card">
<h2>Alertes Intelligentes</h2>
<p>
Détection automatique des anomalies et génération des alertes.
</p>
</div>

<div class="card">
<h2>Caméras IP</h2>
<p>
Visualisation et contrôle des équipements de sécurité connectés.
</p>
</div>

<div class="card">
<h2>Rapports</h2>
<p>
Exportation des données en PDF, Word et Excel.
</p>
</div>

</div>

<a href="/dashboard" class="btn">
Accéder au Dashboard
</a>

</div>

</body>
</html>
