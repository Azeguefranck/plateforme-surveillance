@extends('layouts.app')

@section('content')

<style>

.settings-box{
background:#111c3d;
padding:30px;
border-radius:20px;
color:white;
}

.input-group{
margin-bottom:20px;
}

.input-group label{
display:block;
margin-bottom:10px;
}

.input-group input{
width:100%;
padding:15px;
border:none;
border-radius:10px;
background:#18264d;
color:white;
}

button{
padding:15px 25px;
background:#33ff88;
border:none;
border-radius:10px;
font-weight:bold;
cursor:pointer;
}

</style>

<div class="settings-box">

<h1>Paramètres système</h1>

<form>

<div class="input-group">

<label>Seuil température</label>

<input type="number" value="40">

</div>

<div class="input-group">

<label>Seuil gaz</label>

<input type="number" value="500">

</div>

<div class="input-group">

<label>Email alertes</label>

<input type="email"
value="franckazegue0007@gmail.com">

</div>

<button>

ENREGISTRER

</button>

</form>

</div>

@endsection
