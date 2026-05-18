<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

body{
margin:0;
padding:0;
background:#f4f4f4;
font-family:Arial,sans-serif;
}

.container{
max-width:650px;
margin:auto;
background:white;
padding:25px;
border-radius:18px;
box-sizing:border-box;
}

h2{
font-size:34px;
margin-bottom:25px;
color:#111;
}

p{
font-size:20px;
line-height:32px;
word-break:break-word;
}

.btn-container{
width:100%;
margin-top:35px;
text-align:center;
}

.btn{
display:inline-block;
width:100%;
max-width:180px;
padding:16px 20px;
margin:10px;
border-radius:12px;
font-size:18px;
font-weight:bold;
color:white !important;
text-decoration:none;
box-sizing:border-box;
text-align:center;
}

.valider{
background:#0a9b1f;
}

.refuser{
background:#e00000;
}

.attente{
background:#f0a000;
}

@media screen and (max-width:700px){

.container{
padding:18px;
border-radius:0;
}

h2{
font-size:28px;
text-align:center;
}

p{
font-size:18px;
line-height:28px;
}

.btn{
display:block;
width:100%;
max-width:100%;
margin:12px 0;
font-size:20px;
padding:18px;
}

}

</style>
</head>

<body>

<div class="container">

<h2>Nouvelle demande inscription</h2>

<p><b>Nom :</b> {{ $data['name'] }}</p>

<p><b>Prénom :</b> {{ $data['prenom'] }}</p>

<p><b>Email :</b> {{ $data['email'] }}</p>

<p><b>Téléphone :</b> {{ $data['telephone'] }}</p>

<p><b>Profession :</b> {{ $data['profession'] }}</p>

<p><b>Rôle :</b> {{ $data['role'] }}</p>

<div class="btn-container">

<a href="{{ url('/admin/validate/'.$data['id']) }}"
class="btn valider">

VALIDER

</a>

<a href="{{ url('/admin/reject/'.$data['id']) }}"
class="btn refuser">

REFUSER

</a>

<a href="{{ url('/admin/pending/'.$data['id']) }}"
class="btn attente">

EN ATTENTE

</a>

</div>

</div>

</body>
</html>
