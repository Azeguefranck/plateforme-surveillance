@extends('layouts.app')

@section('content')

<style>

.alert-container{
background:#111c3d;
padding:25px;
border-radius:20px;
color:white;
}

.alert-card{
background:#18264d;
padding:20px;
border-radius:15px;
margin-bottom:20px;
border-left:6px solid red;
}

.alert-title{
font-size:22px;
font-weight:bold;
margin-bottom:10px;
}

.alert-time{
color:#ccc;
font-size:14px;
}

</style>

<div class="alert-container">

<h1>Alertes Temps Réel</h1>

@php

$alertes = DB::table('alertes')
->latest()
->get();

@endphp

@foreach($alertes as $alerte)

<div class="alert-card">

<div class="alert-title">

{{ $alerte->type_alerte }}

</div>

<div>

{{ $alerte->message }}

</div>

<div class="alert-time">

{{ $alerte->created_at }}

</div>

</div>

@endforeach

</div>

@endsection