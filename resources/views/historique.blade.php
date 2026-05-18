@extends('layouts.app')

@section('content')

<style>

.history-box{
background:#111c3d;
padding:25px;
border-radius:20px;
color:white;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

table th{
background:#1c2b52;
padding:15px;
}

table td{
padding:15px;
border-bottom:1px solid #2f437a;
text-align:center;
}

</style>

<div class="history-box">

<h1>Historique des mesures</h1>

<table>

<tr>

<th>ID</th>
<th>Température</th>
<th>Humidité</th>
<th>Gaz</th>
<th>Courant</th>
<th>Puissance</th>
<th>Date</th>

</tr>

@php

$mesures = DB::table('mesures')
->latest()
->limit(100)
->get();

@endphp

@foreach($mesures as $m)

<tr>

<td>{{ $m->id }}</td>

<td>{{ $m->temperature }} °C</td>

<td>{{ $m->humidite }} %</td>

<td>{{ $m->gaz }}</td>

<td>{{ $m->courant }} A</td>

<td>{{ $m->puissance }} W</td>

<td>{{ $m->created_at }}</td>

</tr>

@endforeach

</table>

</div>

@endsection