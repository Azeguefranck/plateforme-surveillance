@extends('layouts.app')

@section('content')

<style>

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
}

.card h2{
margin-bottom:15px;
font-size:28px;
}

.gauge{
width:140px;
height:140px;
border-radius:50%;
border:15px solid #2f437a;
margin:auto;
display:flex;
justify-content:center;
align-items:center;
font-size:28px;
font-weight:bold;
color:white;
}

.chart-container{
background:#111c3d;
padding:20px;
border-radius:20px;
margin-top:30px;
}

canvas{
width:100% !important;
height:350px !important;
}

</style>

<div class="gauges">

<div class="card">
<h2>TEMPÉRATURE</h2>
<div class="gauge">32°C</div>
</div>

<div class="card">
<h2>HUMIDITÉ</h2>
<div class="gauge">70%</div>
</div>

<div class="card">
<h2>GAZ</h2>
<div class="gauge">180</div>
</div>

<div class="card">
<h2>COURANT</h2>
<div class="gauge">12A</div>
</div>

<div class="card">
<h2>PUISSANCE</h2>
<div class="gauge">220W</div>
</div>

</div>

<div class="chart-container">

<h2 style="margin-bottom:20px;">
Température • Humidité • Gaz
</h2>

<canvas id="chart1"></canvas>

</div>

<div class="chart-container">

<h2 style="margin-bottom:20px;">
Courant • Puissance
</h2>

<canvas id="chart2"></canvas>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const heures = [
"08h",
"09h",
"10h",
"11h",
"12h",
"13h",
"14h",
"15h"
];

new Chart(document.getElementById('chart1'),{

type:'line',

data:{

labels:heures,

datasets:[

{
label:'Température',
data:[28,29,30,31,32,33,32,31],
borderColor:'#ff5733',
backgroundColor:'transparent',
borderWidth:4,
tension:0.4
},

{
label:'Humidité',
data:[60,62,64,66,68,70,69,67],
borderColor:'#33b5ff',
backgroundColor:'transparent',
borderWidth:4,
tension:0.4
},

{
label:'Gaz',
data:[100,120,130,140,150,180,170,160],
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

new Chart(document.getElementById('chart2'),{

type:'line',

data:{

labels:heures,

datasets:[

{
label:'Courant',
data:[5,6,7,8,9,10,11,12],
borderColor:'#33ff88',
backgroundColor:'transparent',
borderWidth:4,
tension:0.4
},

{
label:'Puissance',
data:[100,120,140,160,180,200,220,240],
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

</script>

@endsection
