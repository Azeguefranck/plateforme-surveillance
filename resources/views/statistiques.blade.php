@extends('layouts.app')

@section('content')

<style>

.stats-container{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
gap:20px;
}

.stat-card{
background:#111c3d;
padding:25px;
border-radius:20px;
text-align:center;
color:white;
}

.number{
font-size:40px;
font-weight:bold;
margin-top:15px;
color:#33ff88;
}

</style>

<div class="stats-container">

<div class="stat-card">

<h2>Total mesures</h2>

<div class="number">

{{ DB::table('mesures')->count() }}

</div>

</div>

<div class="stat-card">

<h2>Total alertes</h2>

<div class="number">

{{ DB::table('alertes')->count() }}

</div>

</div>

<div class="stat-card">

<h2>Utilisateurs</h2>

<div class="number">

{{ DB::table('users')->count() }}

</div>

</div>

<div class="stat-card">

<h2>Comptes validés</h2>

<div class="number">

{{ DB::table('users')
->where('validation_status','valide')
->count() }}

</div>

</div>

</div>

@endsection