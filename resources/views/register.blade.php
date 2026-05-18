<!DOCTYPE html>
<html lang="fr">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Inscription</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css"/>

<style>

body{
background:#071426;
font-family:Arial,sans-serif;
display:flex;
justify-content:center;
align-items:center;
min-height:100vh;
padding:20px;
margin:0;
}

.form{
background:white;
padding:40px;
border-radius:20px;
width:100%;
max-width:1100px;
box-sizing:border-box;
}

h1{
text-align:center;
color:#1565ff;
margin-bottom:30px;
font-size:40px;
}

.grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:20px;
}

input,select{
width:100%;
padding:16px;
border-radius:12px;
border:2px solid #b7d7ff;
font-size:16px;
box-sizing:border-box;
background:white;
}

button{
width:100%;
padding:18px;
border:none;
border-radius:12px;
background:#18c964;
color:white;
font-size:20px;
font-weight:bold;
margin-top:25px;
cursor:pointer;
}

.back{
text-align:center;
margin-top:20px;
}

@media(max-width:768px){

.grid{
grid-template-columns:1fr;
}

h1{
font-size:28px;
}

}

</style>
</head>

<body>

<form class="form" method="POST" action="/register-user">

@csrf

<h1>NOUVEL UTILISATEUR</h1>

<div class="grid">

<input type="text"
name="nom"
placeholder="Nom complet"
required>

<input type="text"
name="prenom"
placeholder="Prénom"
required>

<input id="phone"
type="tel"
name="telephone"
required>

<input type="email"
name="email"
placeholder="Adresse email"
required>

<select id="country" name="pays" required>
<option value="">Choisir le pays</option>
</select>

<select id="region" name="region" required>
<option value="">Choisir la région</option>
</select>

<select id="departement" name="departement" required>
<option value="">Choisir le département</option>
</select>

<select id="arrondissement" name="arrondissement" required>
<option value="">Choisir l'arrondissement</option>
</select>

<input type="text"
name="ville_residence"
placeholder="Ville de résidence"
required>

<select name="statut_matrimonial" required>

<option value="">Statut matrimonial</option>

<option>Célibataire</option>
<option>Marié(e)</option>
<option>Divorcé(e)</option>
<option>Veuf(ve)</option>

</select>

<input type="text"
name="profession"
placeholder="Profession"
required>

<select name="role" required>

<option value="utilisateur">
Utilisateur
</option>

<option value="admin">
Administrateur
</option>

</select>

<input type="password"
name="password"
placeholder="Mot de passe"
required>

<input type="password"
name="password_confirmation"
placeholder="Confirmer mot de passe"
required>

</div>

<button type="submit">
Créer le compte
</button>

<div class="back">
<a href="/">Retour Accueil</a>
</div>

</form>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>

<script>

window.intlTelInput(document.querySelector("#phone"),{

initialCountry:"cm",
separateDialCode:true,

utilsScript:
"https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"

});

const countries = [

"Afghanistan","Afrique du Sud","Albanie","Algérie","Allemagne",
"Andorre","Angola","Arabie Saoudite","Argentine","Arménie",
"Australie","Autriche","Belgique","Bénin","Botswana",
"Brésil","Burkina Faso","Burundi","Cameroun","Canada",
"Cap-Vert","Chili","Chine","Colombie","Comores",
"Congo","Corée du Sud","Côte d'Ivoire","Danemark","Djibouti",
"Égypte","Émirats arabes unis","Espagne","États-Unis",
"Éthiopie","Finlande","France","Gabon","Gambie",
"Ghana","Guinée","Guinée équatoriale","Haïti","Inde",
"Italie","Japon","Kenya","Liban","Madagascar",
"Mali","Maroc","Maurice","Mauritanie","Mexique",
"Niger","Nigéria","Norvège","Ouganda","Pays-Bas",
"Pologne","Portugal","Qatar","RDC","Royaume-Uni",
"Russie","Rwanda","Sénégal","Suisse","Tchad",
"Togo","Tunisie","Turquie"
];

const countrySelect = document.getElementById("country");

countries.forEach(country => {

let option = document.createElement("option");

option.value = country;
option.textContent = country;

countrySelect.appendChild(option);

});

let camerounData = {};

fetch('/data/cameroun.json')
.then(res => res.json())
.then(data => {
    camerounData = data;
});
countrySelect.addEventListener("change", function(){

regionSelect.innerHTML =
'<option value="">Choisir la région</option>';

departementSelect.innerHTML =
'<option value="">Choisir le département</option>';

arrondissementSelect.innerHTML =
'<option value="">Choisir l\'arrondissement</option>';

if(this.value === "Cameroun"){

Object.keys(camerounData).forEach(region => {

let option = document.createElement("option");

option.value = region;
option.textContent = region;

regionSelect.appendChild(option);

});

}

});

regionSelect.addEventListener("change", function(){

departementSelect.innerHTML =
'<option value="">Choisir le département</option>';

arrondissementSelect.innerHTML =
'<option value="">Choisir l\'arrondissement</option>';

let deps = camerounData[this.value];

Object.keys(deps).forEach(dep => {

let option = document.createElement("option");

option.value = dep;
option.textContent = dep;

departementSelect.appendChild(option);

});

});

departementSelect.addEventListener("change", function(){

arrondissementSelect.innerHTML =
'<option value="">Choisir l\'arrondissement</option>';

let arrs =
camerounData[
regionSelect.value
][this.value];

arrs.forEach(arr => {

let option = document.createElement("option");

option.value = arr;
option.textContent = arr;

arrondissementSelect.appendChild(option);

});

});

</script>

</body>
</html>


<script>

const camerounData = {

"Centre":{
"Mfoundi":[
"Yaoundé I",
"Yaoundé II",
"Yaoundé III",
"Yaoundé IV",
"Yaoundé V",
"Yaoundé VI",
"Yaoundé VII"
],
"Lekié":[
"Monatélé",
"Obala",
"Sa'a",
"Okola"
]
},

"Littoral":{
"Wouri":[
"Douala I",
"Douala II",
"Douala III",
"Douala IV",
"Douala V",
"Douala VI"
]
},

"Ouest":{
"Mifi":[
"Bafoussam I",
"Bafoussam II",
"Bafoussam III"
]
}

};

const pays =
document.getElementById("country");

const region =
document.getElementById("region");

const departement =
document.getElementById("departement");

const arrondissement =
document.getElementById("arrondissement");

pays.addEventListener("change",()=>{

region.innerHTML =
'<option value="">Choisir région</option>';

departement.innerHTML =
'<option value="">Choisir département</option>';

arrondissement.innerHTML =
'<option value="">Choisir arrondissement</option>';

if(pays.value=="Cameroun"){

Object.keys(camerounData)
.forEach(r=>{

region.innerHTML +=
`<option value="${r}">
${r}
</option>`;

});

}else{

region.innerHTML +=
'<option>À remplir librement</option>';

departement.innerHTML +=
'<option>À remplir librement</option>';

arrondissement.innerHTML +=
'<option>À remplir librement</option>';

}

});

region.addEventListener("change",()=>{

departement.innerHTML =
'<option value="">Choisir département</option>';

Object.keys(
camerounData[region.value]
).forEach(d=>{

departement.innerHTML +=
`<option value="${d}">
${d}
</option>`;

});

});

departement.addEventListener("change",()=>{

arrondissement.innerHTML =
'<option value="">Choisir arrondissement</option>';

camerounData[
region.value
][departement.value]
.forEach(a=>{

arrondissement.innerHTML +=
`<option value="${a}">
${a}
</option>`;

});

});

</script>



<script>

const camerounData = {

"Centre":{
"Mfoundi":[
"Yaoundé I",
"Yaoundé II",
"Yaoundé III",
"Yaoundé IV",
"Yaoundé V",
"Yaoundé VI",
"Yaoundé VII"
],
"Lekié":[
"Monatélé",
"Obala",
"Sa'a",
"Okola"
]
},

"Littoral":{
"Wouri":[
"Douala I",
"Douala II",
"Douala III",
"Douala IV",
"Douala V",
"Douala VI"
]
},

"Ouest":{
"Mifi":[
"Bafoussam I",
"Bafoussam II",
"Bafoussam III"
]
}

};

const pays =
document.getElementById("country");

const region =
document.getElementById("region");

const departement =
document.getElementById("departement");

const arrondissement =
document.getElementById("arrondissement");

pays.addEventListener("change",()=>{

region.innerHTML =
'<option value="">Choisir région</option>';

departement.innerHTML =
'<option value="">Choisir département</option>';

arrondissement.innerHTML =
'<option value="">Choisir arrondissement</option>';

if(pays.value=="Cameroun"){

Object.keys(camerounData)
.forEach(r=>{

region.innerHTML +=
`<option value="${r}">
${r}
</option>`;

});

}else{

region.innerHTML +=
'<option>À remplir librement</option>';

departement.innerHTML +=
'<option>À remplir librement</option>';

arrondissement.innerHTML +=
'<option>À remplir librement</option>';

}

});

region.addEventListener("change",()=>{

departement.innerHTML =
'<option value="">Choisir département</option>';

Object.keys(
camerounData[region.value]
).forEach(d=>{

departement.innerHTML +=
`<option value="${d}">
${d}
</option>`;

});

});

departement.addEventListener("change",()=>{

arrondissement.innerHTML =
'<option value="">Choisir arrondissement</option>';

camerounData[
region.value
][departement.value]
.forEach(a=>{

arrondissement.innerHTML +=
`<option value="${a}">
${a}
</option>`;

});

});

</script>

