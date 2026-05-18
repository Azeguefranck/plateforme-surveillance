<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8">
<title>Inscription</title>
</head>

<body style="font-family:Arial;background:#f4f4f4;padding:20px;">

<div style="
max-width:600px;
margin:auto;
background:white;
padding:30px;
border-radius:10px;
">

<h1>Nouvelle demande inscription</h1>

<p><strong>Nom :</strong> {{ $user->name }}</p>

<p><strong>Email :</strong> {{ $user->email }}</p>

<p><strong>Téléphone :</strong> {{ $user->telephone }}</p>

<p><strong>Profession :</strong> {{ $user->profession }}</p>

<p><strong>Rôle :</strong> Utilisateur</p>

<br>

<table width="100%" cellpadding="10">

<tr>
<td>

<a href="{{ url('/valider/'.$user->id) }}"
style="
background:green;
color:white;
padding:15px;
display:block;
text-align:center;
text-decoration:none;
border-radius:8px;
font-weight:bold;
margin-bottom:10px;
font-size:22px;
">
VALIDER
</a>

<a href="{{ url('/refuser/'.$user->id) }}"
style="
background:red;
color:white;
padding:15px;
display:block;
text-align:center;
text-decoration:none;
border-radius:8px;
font-weight:bold;
margin-bottom:10px;
font-size:22px;
">
REFUSER
</a>

<a href="{{ url('/attente/'.$user->id) }}"
style="
background:orange;
color:white;
padding:15px;
display:block;
text-align:center;
text-decoration:none;
border-radius:8px;
font-weight:bold;
font-size:22px;
">
EN ATTENTE
</a>

</td>
</tr>

</table>

</div>

</body>
</html>