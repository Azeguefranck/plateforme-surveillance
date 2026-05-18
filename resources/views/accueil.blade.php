<!DOCTYPE html>
<html lang="fr">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Accueil</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial;
}

body{
background:#050816;
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
padding:20px;
}

.container{
width:100%;
max-width:1000px;
background:#101935;
padding:60px;
border-radius:30px;
text-align:center;
}

h1{
color:#39ff14;
font-size:55px;
margin-bottom:25px;
}

p{
color:white;
font-size:22px;
margin-bottom:50px;
}

.grid{
display:grid;
grid-template-columns:1fr 1fr 1fr;
gap:25px;
}

a{
padding:22px;
background:#1d2a52;
color:white;
text-decoration:none;
border-radius:15px;
font-size:22px;
font-weight:bold;
transition:0.3s;
}

a:hover{
background:#39ff14;
color:black;
}

@media(max-width:768px){

.grid{
grid-template-columns:1fr;
}

h1{
font-size:35px;
}

}

</style>

</head>

<body>

<div class="container">

<h1>PLATEFORME DE SURVEILLANCE</h1>

<p>

Gestion intelligente des salles serveurs

</p>

<div class="grid">

<a href="/register">

Inscription Utilisateur

</a>

<a href="/register">

Inscription Administrateur

</a>

<a href="/login">

Connexion

</a>

</div>

</div>

</body>
</html>
