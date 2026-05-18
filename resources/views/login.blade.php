<!DOCTYPE html>
<html lang="fr">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Connexion</title>

<style>

body{
background:#050816;
display:flex;
justify-content:center;
align-items:center;
height:100vh;
font-family:Arial;
}

.box{
width:450px;
background:#101935;
padding:40px;
border-radius:25px;
}

h1{
color:#39ff14;
text-align:center;
margin-bottom:30px;
font-size:45px;
}

input{
width:100%;
padding:18px;
margin-top:20px;
border:none;
border-radius:12px;
font-size:18px;
}

button{
width:100%;
padding:18px;
margin-top:25px;
background:#63f5c8;
border:none;
border-radius:12px;
font-size:22px;
font-weight:bold;
color:white;
}

.error{
background:red;
padding:10px;
margin-top:15px;
border-radius:10px;
text-align:center;
}

.links{
margin-top:20px;
text-align:center;
}

.links a{
display:block;
margin-top:10px;
color:white;
text-decoration:none;
}

</style>

</head>

<body>

<div class="box">

<h1>Connexion</h1>

@if(session('error'))

<div class="error">

{{ session('error') }}

</div>

@endif

<form method="POST" action="/login-user">

@csrf

<input type="email"
name="email"
placeholder="Adresse email"
required>

<input type="password"
name="mot_de_passe"
placeholder="Mot de passe"
required>

<button type="submit">

SE CONNECTER

</button>

</form>

<div class="links">

<a href="/forgot-password">

Mot de passe oublié ?

</a>

<a href="/register">

Créer un compte

</a>

<a href="/accueil">

Retour accueil

</a>

</div>

</div>

</body>
</html>
