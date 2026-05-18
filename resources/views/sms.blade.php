@extends('layouts.app')

@section('content')

<style>

.sms-box{
background:#111c3d;
padding:25px;
border-radius:20px;
color:white;
}

.sms-card{
background:#18264d;
padding:20px;
border-radius:15px;
margin-bottom:20px;
}

</style>

<div class="sms-box">

<h1>SMS Envoyés</h1>

<div class="sms-card">

SMS automatiques envoyés par le module GSM
en cas d'alerte critique.

</div>

<div class="sms-card">

• Température élevée

</div>

<div class="sms-card">

• Gaz dangereux détecté

</div>

<div class="sms-card">

• Salle serveur hors ligne

</div>

</div>

@endsection