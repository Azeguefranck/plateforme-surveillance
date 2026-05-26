<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServeursController extends Controller
{
    public function index()
    {
        DB::statement("CREATE TABLE IF NOT EXISTS `serveurs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nom` varchar(255) NOT NULL,
            `type` varchar(100) NOT NULL,
            `adresse_ip` varchar(50) DEFAULT NULL,
            `nom_domaine` varchar(255) DEFAULT NULL,
            `salle_id` int(11) DEFAULT NULL,
            `responsable` varchar(255) DEFAULT NULL,
            `systeme_exploitation` varchar(100) DEFAULT NULL,
            `version_os` varchar(100) DEFAULT NULL,
            `ram` varchar(50) DEFAULT NULL,
            `cpu` varchar(100) DEFAULT NULL,
            `stockage` varchar(100) DEFAULT NULL,
            `temperature` float DEFAULT NULL,
            `statut` varchar(50) NOT NULL DEFAULT 'actif',
            `date_installation` date DEFAULT NULL,
            `description` text DEFAULT NULL,
            `localisation_physique` varchar(255) DEFAULT NULL,
            `fournisseur` varchar(255) DEFAULT NULL,
            `numero_rack` varchar(100) DEFAULT NULL,
            `adresse_mac` varchar(50) DEFAULT NULL,
            `numero_serie` varchar(100) DEFAULT NULL,
            `type_alimentation` varchar(100) DEFAULT NULL,
            `port_reseau` varchar(100) DEFAULT NULL,
            `consommation_energetique` float DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        try {
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
        } catch (\Exception $e) {}

        $serveurs = DB::table('serveurs')
            ->leftJoin('salles', 'serveurs.salle_id', '=', 'salles.id')
            ->select('serveurs.*', 'salles.nom as salle_nom', 'salles.code as salle_code')
            ->orderBy('serveurs.id', 'desc')
            ->get();

        $salles = DB::table('salles')->orderBy('nom')->get();

        return view('serveurs', compact('serveurs', 'salles'));
    }

    public function store(Request $r)
    {
        DB::table('serveurs')->insert([
            'nom'                      => $r->input('nom'),
            'type'                     => $r->input('type'),
            'adresse_ip'               => $r->input('adresse_ip') ?: null,
            'nom_domaine'              => $r->input('nom_domaine') ?: null,
            'salle_id'                 => $r->input('salle_id') ?: null,
            'responsable'              => $r->input('responsable') ?: null,
            'systeme_exploitation'     => $r->input('systeme_exploitation') ?: null,
            'version_os'               => $r->input('version_os') ?: null,
            'ram'                      => $r->input('ram') ?: null,
            'cpu'                      => $r->input('cpu') ?: null,
            'stockage'                 => $r->input('stockage') ?: null,
            'temperature'              => $r->input('temperature') !== '' ? $r->input('temperature') : null,
            'statut'                   => $r->input('statut', 'actif'),
            'date_installation'        => $r->input('date_installation') ?: null,
            'description'              => $r->input('description') ?: null,
            'localisation_physique'    => $r->input('localisation_physique') ?: null,
            'fournisseur'              => $r->input('fournisseur') ?: null,
            'numero_rack'              => $r->input('numero_rack') ?: null,
            'adresse_mac'              => $r->input('adresse_mac') ?: null,
            'numero_serie'             => $r->input('numero_serie') ?: null,
            'type_alimentation'        => $r->input('type_alimentation') ?: null,
            'port_reseau'              => $r->input('port_reseau') ?: null,
            'consommation_energetique' => $r->input('consommation_energetique') !== '' ? $r->input('consommation_energetique') : null,
            'created_at'               => now(),
            'updated_at'               => now(),
        ]);

        return redirect('/serveurs')->with('success', 'Serveur ajouté avec succès.');
    }

    public function update(Request $r, $id)
    {
        DB::table('serveurs')->where('id', $id)->update([
            'nom'                      => $r->input('nom'),
            'type'                     => $r->input('type'),
            'adresse_ip'               => $r->input('adresse_ip') ?: null,
            'nom_domaine'              => $r->input('nom_domaine') ?: null,
            'salle_id'                 => $r->input('salle_id') ?: null,
            'responsable'              => $r->input('responsable') ?: null,
            'systeme_exploitation'     => $r->input('systeme_exploitation') ?: null,
            'version_os'               => $r->input('version_os') ?: null,
            'ram'                      => $r->input('ram') ?: null,
            'cpu'                      => $r->input('cpu') ?: null,
            'stockage'                 => $r->input('stockage') ?: null,
            'temperature'              => $r->input('temperature') !== '' ? $r->input('temperature') : null,
            'statut'                   => $r->input('statut', 'actif'),
            'date_installation'        => $r->input('date_installation') ?: null,
            'description'              => $r->input('description') ?: null,
            'localisation_physique'    => $r->input('localisation_physique') ?: null,
            'fournisseur'              => $r->input('fournisseur') ?: null,
            'numero_rack'              => $r->input('numero_rack') ?: null,
            'adresse_mac'              => $r->input('adresse_mac') ?: null,
            'numero_serie'             => $r->input('numero_serie') ?: null,
            'type_alimentation'        => $r->input('type_alimentation') ?: null,
            'port_reseau'              => $r->input('port_reseau') ?: null,
            'consommation_energetique' => $r->input('consommation_energetique') !== '' ? $r->input('consommation_energetique') : null,
            'updated_at'               => now(),
        ]);

        return redirect('/serveurs')->with('success', 'Serveur mis à jour avec succès.');
    }

    public function destroy($id)
    {
        DB::table('serveurs')->where('id', $id)->delete();
        return redirect('/serveurs')->with('success', 'Serveur supprimé avec succès.');
    }
}
