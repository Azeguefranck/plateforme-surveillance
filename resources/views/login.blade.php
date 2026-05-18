<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Connexion</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{
background:#050816;
display:flex;
justify-content:center;
align-items:center;
min-height:100vh;
font-family:Arial, Helvetica, sans-serif;
padding:20px;
}

.box{
width:100%;
max-width:450px;
background:#101935;
padding:40px;
border-radius:25px;
box-shadow:0 0 30px rgba(0,0,0,0.4);
}

h1{
color:#39ff14;
text-align:center;
margin-bottom:30px;
font-size:42px;
}

input{
width:100%;
padding:18px;
margin-top:20px;
border:none;
border-radius:12px;
font-size:18px;
outline:none;
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
cursor:pointer;
transition:0.3s;
}

button:hover{
opacity:0.9;
}

.error{
background:red;
color:white;
padding:14px;
margin-top:15px;
border-radius:10px;
text-align:center;
font-size:17px;
}

.success{
background:green;
color:white;
padding:14px;
margin-top:15px;
border-radius:10px;
text-align:center;
font-size:17px;
}

.links{
margin-top:25px;
text-align:center;
}

.links a{
display:block;
margin-top:12px;
color:white;
text-decoration:none;
font-size:17px;
}

.links a:hover{
color:#63f5c8;
}

@media(max-width:500px){

.box{
padding:25px;
}

h1{
font-size:32px;
}

button{
font-size:18px;
}

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

@if(session('success'))

<div class="success">

{{ session('success') }}

</div>

@endif

<form method="POST" action="/login-user">

@csrf

<input
type="email"
name="email"
placeholder="Adresse email"
required>

<input
type="password"
name="password"
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