<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServeursController extends Controller
{
    public function index()
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        $serveurs = $salles = collect();
        $stats    = ['total' => 0, 'en_ligne' => 0, 'hors_ligne' => 0, 'maintenance' => 0];

        try {
            $serveurs = DB::table('serveurs')->orderByDesc('created_at')->get();
            $salles   = DB::table('salles')->get();
            $stats = [
                'total'       => DB::table('serveurs')->count(),
                'en_ligne'    => DB::table('serveurs')->where('statut', 'en_ligne')->count(),
                'hors_ligne'  => DB::table('serveurs')->where('statut', 'hors_ligne')->count(),
                'maintenance' => DB::table('serveurs')->where('statut', 'maintenance')->count(),
            ];
        } catch (\Exception $e) {}

        return view('serveurs', compact('user', 'serveurs', 'salles', 'stats'));
    }

    public function store(Request $request)
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        $request->validate([
            'nom'  => 'required|string|max:150',
            'type' => 'required|string',
        ]);

        DB::table('serveurs')->insert([
            'nom'               => $request->nom,
            'type'              => $request->type,
            'adresse_ip'        => $request->adresse_ip,
            'nom_domaine'       => $request->nom_domaine,
            'localisation'      => $request->localisation,
            'salle_id'          => $request->salle_id ?: null,
            'responsable'       => $request->responsable,
            'os'                => $request->os,
            'ram'               => $request->ram,
            'cpu'               => $request->cpu,
            'stockage'          => $request->stockage,
            'statut'            => $request->statut ?? 'en_ligne',
            'date_installation' => $request->date_installation ?: null,
            'notes'             => $request->notes,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return back()->with('success_srv', 'Serveur ajouté avec succès.');
    }

    public function destroy($id)
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        DB::table('serveurs')->where('id', $id)->delete();
        return back()->with('success_srv', 'Serveur supprimé.');
    }

    public function update(Request $request, $id)
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        DB::table('serveurs')->where('id', $id)->update([
            'nom'               => $request->nom,
            'type'              => $request->type,
            'adresse_ip'        => $request->adresse_ip,
            'nom_domaine'       => $request->nom_domaine,
            'localisation'      => $request->localisation,
            'salle_id'          => $request->salle_id ?: null,
            'responsable'       => $request->responsable,
            'os'                => $request->os,
            'ram'               => $request->ram,
            'cpu'               => $request->cpu,
            'stockage'          => $request->stockage,
            'statut'            => $request->statut ?? 'en_ligne',
            'date_installation' => $request->date_installation ?: null,
            'notes'             => $request->notes,
            'updated_at'        => now(),
        ]);

        return back()->with('success_srv', 'Serveur mis à jour.');
    }
}
