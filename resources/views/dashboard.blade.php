@extends('layouts.app')

@section('content')

<style>

body{
background:#0b1120;
color:white;
font-family:Arial, Helvetica, sans-serif;
}

.dashboard-title{
font-size:32px;
font-weight:bold;
margin-bottom:25px;
color:#ffffff;
}

.gauges{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
margin-bottom:40px;
}

.card{
background:#111c3d;
padding:20px;
border-radius:20px;
text-align:center;
box-shadow:0 0 15px rgba(0,0,0,0.3);
transition:0.3s;
}

.card:hover{
transform:translateY(-5px);
}

.card h2{
margin-bottom:20px;
font-size:22px;
color:#c7d2ff;
}

.gauge{
width:150px;
height:150px;
border-radius:50%;
border:15px solid #2f437a;
margin:auto;
display:flex;
justify-content:center;
align-items:center;
font-size:28px;
font-weight:bold;
color:white;
background:#18264d;
}

.chart-container{
background:#111c3d;
padding:25px;
border-radius:20px;
margin-top:30px;
box-shadow:0 0 15px rgba(0,0,0,0.3);
}

.chart-title{
margin-bottom:20px;
font-size:24px;
color:#ffffff;
}

canvas{
width:100% !important;
height:350px !important;
}

.status-box{
margin-top:30px;
background:#111c3d;
padding:20px;
border-radius:20px;
display:flex;
justify-content:space-between;
align-items:center;
flex-wrap:wrap;
gap:15px;
}

.status-item{
font-size:18px;
}

.online{
color:#33ff88;
font-weight:bold;
}

.alert{
color:#ff5733;
font-weight:bold;
}

</style>

<div class="dashboard-title">
Dashboard de Surveillance Temps Réel
</div>

<div class="gauges">

<div class="card">
<h2>TEMPÉRATURE</h2>
<div class="gauge" id="temperature">
0°C
</div>
</div>

<div class="card">
<h2>HUMIDITÉ</h2>
<div class="gauge" id="humidite">
0%
</div>
</div>

<div class="card">
<h2>GAZ</h2>
<div class="gauge" id="gaz">
0
</div>
</div>

<div class="card">
<h2>COURANT</h2>
<div class="gauge" id="courant">
0A
</div>
</div>

<div class="card">
<h2>PUISSANCE</h2>
<div class="gauge" id="puissance">
0W
</div>
</div>

</div>

<div class="status-box">

<div class="status-item">
État système :
<span class="online">
EN LIGNE
</span>
</div>

<div class="status-item">
Alertes :
<span id="alerte-status" class="online">
AUCUNE
</span>
</div>

<div class="status-item">
Dernière mise à jour :
<span id="last-update">
--
</span>
</div>

</div>

<div class="chart-container">

<h2 class="chart-title">
Température • Humidité • Gaz
</h2>

<canvas id="chart1"></canvas>

</div>

<div class="chart-container">

<h2 class="chart-title">
Courant • Puissance
</h2>

<canvas id="chart2"></canvas>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const labels = [];

const temperatureData = [];
const humiditeData = [];
const gazData = [];
const courantData = [];
const puissanceData = [];

const chart1 = new Chart(document.getElementById('chart1'),{

type:'line',

data:{

labels:labels,

datasets:[

{
label:'Température',
data:temperatureData,
borderColor:'#ff5733',
backgroundColor:'transparent',
borderWidth:4,
tension:0.4
},

{
label:'Humidité',
data:humiditeData,
borderColor:'#33b5ff',
backgroundColor:'transparent',
borderWidth:4,
tension:0.4
},

{
label:'Gaz',
data:gazData,
borderColor:'#ffd633',
backgroundColor:'transparent',
borderWidth:4,
tension:0.4
}

]

},

options:{
responsive:true
}

});

const chart2 = new Chart(document.getElementById('chart2'),{

type:'line',

data:{

labels:labels,

datasets:[

{
label:'Courant',
data:courantData,
borderColor:'#33ff88',
backgroundColor:'transparent',
borderWidth:4,
tension:0.4
},

{
label:'Puissance',
data:puissanceData,
borderColor:'#bb66ff',
backgroundColor:'transparent',
borderWidth:4,
tension:0.4
}

]

},

options:{
responsive:true
}

});

function ajouterValeur(tableau,valeur){

tableau.push(valeur);

if(tableau.length > 15){
tableau.shift();
}

}

function ajouterLabel(){

const now = new Date();

const heure =
now.getHours() + ":" +
now.getMinutes() + ":" +
now.getSeconds();

labels.push(heure);

if(labels.length > 15){
labels.shift();
}

}

function verifierAlertes(data){

let alerte = "AUCUNE";

if(data.temperature >= 40){
alerte = "TEMPÉRATURE ÉLEVÉE";
}

if(data.gaz >= 300){
alerte = "GAZ DANGEREUX";
}

document.getElementById('alerte-status').innerHTML = alerte;

if(alerte !== "AUCUNE"){
document.getElementById('alerte-status')
.className = "alert";
}else{
document.getElementById('alerte-status')
.className = "online";
}

}

setInterval(() => {

fetch('/api/dashboard-data')

.then(response => response.json())

.then(data => {

document.getElementById('temperature')
.innerHTML = data.temperature + "°C";

document.getElementById('humidite')
.innerHTML = data.humidite + "%";

document.getElementById('gaz')
.innerHTML = data.gaz;

document.getElementById('courant')
.innerHTML = data.courant + "A";

document.getElementById('puissance')
.innerHTML = data.puissance + "W";

document.getElementById('last-update')
.innerHTML = new Date().toLocaleTimeString();

ajouterLabel();

ajouterValeur(temperatureData,data.temperature);
ajouterValeur(humiditeData,data.humidite);
ajouterValeur(gazData,data.gaz);
ajouterValeur(courantData,data.courant);
ajouterValeur(puissanceData,data.puissance);

chart1.update();
chart2.update();

verifierAlertes(data);

})

.catch(error => {

console.log(error);

});

},1000);

</script>

@endsection