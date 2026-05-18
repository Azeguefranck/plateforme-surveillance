@extends('layouts.app')

@section('content')

<style>

.report-box{
background:#111c3d;
padding:30px;
border-radius:20px;
color:white;
}

.report-card{
background:#18264d;
padding:20px;
border-radius:15px;
margin-bottom:20px;
}

.btn{
display:inline-block;
padding:12px 20px;
background:#33b5ff;
border-radius:10px;
text-decoration:none;
color:white;
font-weight:bold;
margin-top:10px;
}

</style>

<div class="report-box">

<h1>Rapports et Exportations</h1>

<div class="report-card">

<h2>Rapport PDF</h2>

<p>

Exporter toutes les mesures et alertes.

</p>

<a href="#" class="btn">

EXPORT PDF

</a>

</div>

<div class="report-card">

<h2>Rapport Excel</h2>

<p>

Exporter les données statistiques.

</p>

<a href="#" class="btn">

EXPORT EXCEL

</a>

</div>

</div>

@endsection