<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesure;
use Illuminate\Support\Facades\DB;

class ApiCapteurController extends Controller
{
    public function store(Request $request)
    {

        $mesure = Mesure::create([

            'temperature' => $request->temperature ?? 0,
            'humidite' => $request->humidite ?? 0,
            'gaz' => $request->gaz ?? 0,
            'courant' => $request->courant ?? 0,
            'puissance' => $request->puissance ?? 0

        ]);

        DB::table('historiques')->insert([
            'action' => 'Nouvelle mesure reçue depuis Arduino',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if(($request->temperature ?? 0) >= 40){

            DB::table('alertes')->insert([
                'niveau' => 'CRITIQUE',
                'message' => 'Température critique détectée',
                'valeur' => $request->temperature.' °C',
                'created_at' => now(),
                'updated_at' => now()
            ]);

        }

        return response()->json([
            'success' => true,
            'message' => 'Mesure enregistrée',
            'data' => $mesure
        ]);
    }

    public function latest()
    {
        return Mesure::latest()->first();
    }

    public function historique()
    {
        return DB::table('historiques')
        ->latest()
        ->get();
    }

    public function alertes()
    {
        return DB::table('alertes')
        ->latest()
        ->get();
    }
}