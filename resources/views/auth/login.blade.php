<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion</title>

<style>

body{
margin:0;
font-family:Arial;
background:#071739;
height:100vh;
display:flex;
justify-content:center;
align-items:center;
color:white;
}

.box{
width:400px;
background:#0b1d45;
padding:40px;
border-radius:20px;
box-shadow:0 0 30px rgba(0,0,0,0.4);
}

h1{
color:#39ff14;
margin-bottom:30px;
}

input{
width:100%;
padding:15px;
margin-bottom:20px;
border:none;
border-radius:10px;
background:#132c63;
color:white;
}

button{
width:100%;
padding:15px;
border:none;
border-radius:10px;
background:#39ff14;
font-weight:bold;
cursor:pointer;
}

a{
color:#39ff14;
text-decoration:none;
}

.error{
background:red;
padding:10px;
border-radius:10px;
margin-bottom:20px;
}

</style>
</head>

<body>

<div class="box">

<h1>CONNEXION</h1>

@if(session('error'))
<div class="error">
{{ session('error') }}
</div>
@endif

<form method="POST" action="/login">
@csrf

<input type="email" name="email" placeholder="Adresse email" required>

<input type="password" name="mot_de_passe" placeholder="Mot de passe" required>

<button type="submit">
SE CONNECTER
</button>

</form>

<br>

<a href="/register">
Créer un compte
</a>

</div>

</body>
</html>
