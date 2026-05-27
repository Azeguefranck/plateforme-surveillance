<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Accueil</title>

<style>

body{
margin:0;
padding:0;
font-family:Arial;
background:#050816;
color:white;
overflow-x:hidden;
}

.container{
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
padding:30px;
}

.box{
max-width:1000px;
width:100%;
background:#101935;
border-radius:25px;
padding:50px;
display:grid;
grid-template-columns:1fr 1fr;
gap:40px;
}

.left h1{
font-size:55px;
color:#39ff14;
margin-bottom:20px;
}

.left p{
font-size:20px;
line-height:35px;
color:#ddd;
}

.features{
display:grid;
grid-template-columns:1fr 1fr;
gap:15px;
margin-top:30px;
}

.feature{
background:#18264d;
padding:20px;
border-radius:15px;
font-weight:bold;
}

.right{
display:flex;
flex-direction:column;
justify-content:center;
gap:20px;
}

.btn{
padding:18px;
border:none;
border-radius:15px;
font-size:18px;
font-weight:bold;
cursor:pointer;
transition:0.3s;
text-decoration:none;
text-align:center;
}

.green{
background:#39ff14;
color:black;
}

.blue{
background:#00bfff;
color:white;
}

.orange{
background:#ff9900;
color:white;
}

.btn:hover{
transform:scale(1.03);
}

@media(max-width:900px){

.box{
grid-template-columns:1fr;
padding:25px;
}

.left h1{
font-size:35px;
}

}

/* ── Responsive anti-overflow ── */
*{overflow-wrap:break-word;word-break:break-word}
img,video,iframe{max-width:100%;height:auto}
@media(max-width:640px){
body{overflow-x:hidden}
[class*="container"],[class*="card"],[class*="box"],[class*="left"],[class*="right"]{max-width:100%!important}
}
@media(max-width:400px){
h1{font-size:clamp(18px,6vw,35px)!important}
h2,h3{font-size:clamp(14px,4vw,22px)!important}
}

</style>

</head>

<body>

<div class="container">

<div class="box">

<div class="left">

<h1>SURVEILLANCE INTELLIGENTE</h1>

<p>

Plateforme intelligente de surveillance des salles serveurs :

✔ Température  
✔ Humidité  
✔ Gaz  
✔ Puissance électrique  
✔ Alertes temps réel  
✔ Alertes Email
✔ Caméras IP  
✔ Rapports automatiques  

</p>

<div class="features">

<div class="feature">Surveillance Temps Réel</div>
<div class="feature">Alertes Email</div>
<div class="feature">Graphiques Modernes</div>
<div class="feature">Rapports PDF</div>

</div>

</div>

<div class="right">

<a href="/login" class="btn green">
SE CONNECTER
</a>


</div>

</div>

</div>

</body>
</html>
