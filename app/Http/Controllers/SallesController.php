<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SallesController extends Controller
{
    private function generateCode(): string
    {
        $last = DB::table('salles')
            ->orderBy('id', 'desc')
            ->value('code');

        if (!$last) {
            return 'SALLE-001';
        }

        // Extraire le numéro depuis le dernier code (ex: SALLE-042 → 42)
        $parts = explode('-', $last);
        $num   = isset($parts[1]) ? (int)$parts[1] : 0;
        return 'SALLE-' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        DB::statement("CREATE TABLE IF NOT EXISTS `salles` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `code` varchar(20) NOT NULL,
            `nom` varchar(255) NOT NULL,
            `description` text DEFAULT NULL,
            `localisation` varchar(255) DEFAULT NULL,
            `capacite` int(11) DEFAULT NULL,
            `responsable` varchar(255) DEFAULT NULL,
            `statut` varchar(50) NOT NULL DEFAULT 'actif',
            `niveau_securite` varchar(50) NOT NULL DEFAULT 'standard',
            `statut_reseau` varchar(50) NOT NULL DEFAULT 'connecte',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `code` (`code`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $salles = DB::table('salles')->orderBy('id', 'desc')->get();

        $total      = $salles->count();
        $actives    = $salles->where('statut', 'actif')->count();
        $maintenance = $salles->where('statut', 'maintenance')->count();

        $totalServeurs = 0;
        try {
            $totalServeurs = DB::table('serveurs')->count();
        } catch (\Exception $e) {
            $totalServeurs = 0;
        }

        $stats = [
            'total'          => $total,
            'actives'        => $actives,
            'maintenance'    => $maintenance,
            'total_serveurs' => $totalServeurs,
        ];

        return view('salles', compact('salles', 'stats'));
    }

    public function store(Request $r)
    {
        $code = $this->generateCode();

        DB::table('salles')->insert([
            'code'           => $code,
            'nom'            => $r->input('nom'),
            'description'    => $r->input('description') ?: null,
            'localisation'   => $r->input('localisation') ?: null,
            'capacite'       => $r->input('capacite') !== '' ? (int)$r->input('capacite') : null,
            'responsable'    => $r->input('responsable') ?: null,
            'statut'         => $r->input('statut', 'actif'),
            'niveau_securite' => $r->input('niveau_securite', 'standard'),
            'statut_reseau'  => $r->input('statut_reseau', 'connecte'),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect('/salles')->with('success', "Salle {$code} créée avec succès.");
    }

    public function update(Request $r, $id)
    {
        DB::table('salles')->where('id', $id)->update([
            'nom'            => $r->input('nom'),
            'description'    => $r->input('description') ?: null,
            'localisation'   => $r->input('localisation') ?: null,
            'capacite'       => $r->input('capacite') !== '' ? (int)$r->input('capacite') : null,
            'responsable'    => $r->input('responsable') ?: null,
            'statut'         => $r->input('statut', 'actif'),
            'niveau_securite' => $r->input('niveau_securite', 'standard'),
            'statut_reseau'  => $r->input('statut_reseau', 'connecte'),
            'updated_at'     => now(),
        ]);

        return redirect('/salles')->with('success', 'Salle mise à jour avec succès.');
    }

    public function destroy($id)
    {
        DB::table('salles')->where('id', $id)->delete();
        return redirect('/salles')->with('success', 'Salle supprimée avec succès.');
    }
}
