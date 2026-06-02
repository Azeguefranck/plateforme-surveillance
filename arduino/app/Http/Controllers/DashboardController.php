<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{

    public function dashboard(){ return view('dashboard'); }

    public function accueil(){ return view('accueil'); }

    public function surveillance(){ return view('surveillance'); }

    public function alertes(){ return view('alertes'); }

    public function historique(){ return view('historique'); }

    public function statistiques(){ return view('statistiques'); }

    public function sms(){ return view('sms'); }

    public function anomalies(){ return view('anomalies'); }

    public function profil(){ return view('profil'); }

    public function users(){ return view('users'); }

    public function cameras(){ return view('cameras'); }

    public function salles(){ return view('salles'); }

    public function serveursweb(){ return view('serveurs_web'); }

    public function serveursbd(){ return view('serveurs_bd'); }

}