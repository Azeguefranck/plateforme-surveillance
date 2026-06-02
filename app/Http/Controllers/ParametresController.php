<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Gestion des seuils d'alerte stockés dans storage/app/seuils.json
class ParametresController extends Controller
{
    // Chemin vers le fichier de seuils
    private function seuilsPath(): string
    {
        return storage_path('app/seuils.json');
    }

    // Seuils par défaut si le fichier n'existe pas
    public function defaults(): array
    {
        return [
            'temperature' => ['warning' => 35.0,  'critique' => 40.0],
            'humidite'    => ['warning' => 75.0,  'critique' => 85.0],
            'gaz'         => ['warning' => 300.0, 'critique' => 500.0],
            'pir'         => ['actif' => 1],
        ];
    }

    // Charge les seuils depuis le fichier JSON
    public function load(): array
    {
        if (!file_exists($this->seuilsPath())) {
            return $this->defaults();
        }
        $data = json_decode(file_get_contents($this->seuilsPath()), true);
        return is_array($data) ? $data : $this->defaults();
    }

    // Affiche la page paramètres
    public function show()
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        $seuils = $this->load();
        return view('parametres', compact('user', 'seuils'));
    }

    // Sauvegarde les seuils envoyés par le formulaire
    public function saveSeuils(Request $request)
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        $seuils = [
            'temperature' => [
                'warning'  => (float) $request->temp_warning,
                'critique' => (float) $request->temp_critique,
            ],
            'humidite' => [
                'warning'  => (float) $request->hum_warning,
                'critique' => (float) $request->hum_critique,
            ],
            'gaz' => [
                'warning'  => (float) $request->gaz_warning,
                'critique' => (float) $request->gaz_critique,
            ],
            'pir' => [
                'actif' => $request->pir_actif ? 1 : 0,
            ],
        ];

        $dir = dirname($this->seuilsPath());
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        file_put_contents($this->seuilsPath(), json_encode($seuils, JSON_PRETTY_PRINT));

        return back()->with('success_seuils', 'Seuils de capteurs mis à jour avec succès.');
    }
}
