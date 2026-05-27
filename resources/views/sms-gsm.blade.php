<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SMS GSM</title>

<style>

body{
background:#050b16;
color:white;
font-family:Arial;
padding:30px;
}

.container{
background:#111827;
padding:30px;
border-radius:20px;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

th,td{
padding:15px;
border-bottom:1px solid #333;
}

th{
background:#1e293b;
}

.green{
color:#22c55e;
font-weight:bold;
}

/* ── Responsive anti-overflow ── */
*{overflow-wrap:break-word;word-break:break-word}
img,video,iframe{max-width:100%;height:auto}
table{max-width:100%;width:100%}
@media(max-width:640px){
body{overflow-x:hidden;padding:12px}
.container{padding:16px!important;border-radius:12px!important}
table{display:block;overflow-x:auto;-webkit-overflow-scrolling:touch}
th,td{padding:10px 8px!important;font-size:12px}
}
@media(max-width:400px){
h1{font-size:18px!important}
}

</style>

</head>

<body>

<div class="container">

<h1>SMS GSM</h1>

<table>

<tr>
<th>NUMERO</th>
<th>MESSAGE</th>
<th>ETAT</th>
<th>DATE</th>
</tr>

<tr>
<td>690000000</td>
<td>Alerte température critique</td>
<td class="green">ENVOYÉ</td>
<td>17/05/2026 22:40</td>
</tr>

<tr>
<td>680000000</td>
<td>Gaz détecté salle serveur</td>
<td class="green">ENVOYÉ</td>
<td>17/05/2026 22:50</td>
</tr>

</table>

</div>

</body>
</html>
