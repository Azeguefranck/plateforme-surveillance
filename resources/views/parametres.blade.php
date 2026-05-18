<!DOCTYPE html>
<html lang="fr">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Paramètres</title>

<style>

body{
background:#050816;
font-family:Arial;
padding:30px;
color:white;
}

table{
width:100%;
border-collapse:collapse;
background:#101935;
border-radius:15px;
overflow:hidden;
}

th,td{
padding:15px;
border-bottom:1px solid #333;
text-align:center;
}

th{
background:#1b2a52;
}

a{
padding:8px 12px;
border-radius:8px;
color:white;
text-decoration:none;
font-size:14px;
}

.valider{
background:green;
}

.refuser{
background:red;
}

.attente{
background:orange;
}

.supprimer{
background:#444;
}

</style>

</head>

<body>

<h1>

GESTION UTILISATEURS

</h1>

<table>

<tr>

<th>ID</th>
<th>Nom</th>
<th>Email</th>
<th>Téléphone</th>
<th>Profession</th>
<th>Rôle</th>
<th>Statut</th>
<th>Actions</th>

</tr>

@php

$users = DB::table('utilisateurs')->get();

@endphp

@foreach($users as $u)

<tr>

<td>{{ $u->id }}</td>
<td>{{ $u->nom }}</td>
<td>{{ $u->email }}</td>
<td>{{ $u->telephone }}</td>
<td>{{ $u->profession }}</td>
<td>{{ $u->role }}</td>
<td>{{ $u->statut }}</td>

<td>

<a class="valider"
href="/admin/validate/{{ $u->id }}">

VALIDER

</a>

<a class="refuser"
href="/admin/reject/{{ $u->id }}">

REFUSER

</a>

<a class="attente"
href="/admin/pending/{{ $u->id }}">

ATTENTE

</a>

<a class="supprimer"
href="/admin/delete/{{ $u->id }}">

SUPPRIMER

</a>

</td>

</tr>

@endforeach

</table>

</body>
</html>
