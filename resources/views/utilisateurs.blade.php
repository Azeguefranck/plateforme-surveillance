@extends('layouts.app')

@section('content')

<style>

.table-container{
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

.btn{
padding:8px 15px;
border-radius:10px;
text-decoration:none;
color:white;
font-weight:bold;
}

.valider{
background:green;
}

.bloquer{
background:red;
}

.attente{
background:orange;
}

</style>

<div class="table-container">

<h1>Gestion des utilisateurs</h1>

<table>

<tr>

<th>ID</th>
<th>Nom</th>
<th>Email</th>
<th>Téléphone</th>
<th>Rôle</th>
<th>Statut</th>
<th>Actions</th>

</tr>

@php

$users = DB::table('users')->get();

@endphp

@foreach($users as $user)

<tr>

<td>{{ $user->id }}</td>

<td>{{ $user->nom }}</td>

<td>{{ $user->email }}</td>

<td>{{ $user->telephone }}</td>

<td>{{ $user->role }}</td>

<td>{{ $user->validation_status }}</td>

<td>

<a
href="/valider/{{ $user->id }}"
class="btn valider">

VALIDER

</a>

<a
href="/bloquer/{{ $user->id }}"
class="btn bloquer">

BLOQUER

</a>

<a
href="/attente/{{ $user->id }}"
class="btn attente">

ATTENTE

</a>

</td>

</tr>

@endforeach

</table>

</div>

@endsection