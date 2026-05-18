<!DOCTYPE html>
<html lang="fr">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Mot de passe oublié</title>

<style>

body{
background:#050816;
display:flex;
justify-content:center;
align-items:center;
height:100vh;
font-family:Arial;
padding:20px;
}

.box{
width:100%;
max-width:450px;
background:#101935;
padding:40px;
border-radius:20px;
}

h1{
color:#39ff14;
text-align:center;
margin-bottom:25px;
}

input{
width:100%;
padding:16px;
margin-top:20px;
border:none;
border-radius:10px;
}

button{
width:100%;
padding:16px;
margin-top:20px;
background:#00bfff;
color:white;
border:none;
border-radius:10px;
font-size:18px;
font-weight:bold;
}

a{
display:block;
text-align:center;
margin-top:20px;
color:white;
text-decoration:none;
}

</style>

</head>

<body>

<div class="box">

<h1>MOT DE PASSE OUBLIÉ</h1>

<form method="POST" action="/forgot-password">

@csrf

<input type="email" name="email" placeholder="Adresse email" required>

<button type="submit">

ENVOYER LE CODE

</button>

</form>

<a href="/login">

Retour connexion

</a>

</div>

</body>
</html>
