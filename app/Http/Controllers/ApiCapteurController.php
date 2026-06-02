<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesure;
use Illuminate\Support\Facades\DB;

// Contrôleur API secondaire (non utilisé — voir routes/api.php pour la logique principale)
class ApiCapteurController extends Controller
{
    // Enregistre une mesure et déclenche une alerte si la température est critique
    public function store(Request $request)
    {
        $mesure = Mesure::create([
            'temperature' => $request->temperature ?? 0,
            'humidite'    => $request->humidite    ?? 0,
            'gaz'         => $request->gaz         ?? 0,
        ]);

        DB::table('historiques')->insert([
            'action'     => 'Nouvelle mesure reçue depuis Arduino',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (($request->temperature ?? 0) >= 40) {
            DB::table('alertes')->insert([
                'niveau'     => 'CRITIQUE',
                'message'    => 'Température critique détectée',
                'valeur'     => $request->temperature . ' °C',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Mesure enregistrée',
            'data'    => $mesure,
        ]);
    }

    // Retourne la dernière mesure
    public function latest()
    {
        return Mesure::latest()->first();
    }

    // Retourne tout l'historique
    public function historique()
    {
        return DB::table('historiques')->latest()->get();
    }

    // Retourne toutes les alertes
    public function alertes()
    {
        return DB::table('alertes')->latest()->get();
    }
}
