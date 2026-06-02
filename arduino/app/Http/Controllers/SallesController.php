<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SallesController extends Controller
{
    public function index()
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        $salles = collect();
        $stats  = ['total' => 0, 'actives' => 0, 'inactives' => 0, 'maintenance' => 0];

        try {
            $salles = DB::table('salles')->orderBy('nom')->get();
            $stats  = [
                'total'       => DB::table('salles')->count(),
                'actives'     => DB::table('salles')->where('statut', 'active')->count(),
                'inactives'   => DB::table('salles')->where('statut', 'inactive')->count(),
                'maintenance' => DB::table('salles')->where('statut', 'maintenance')->count(),
            ];
        } catch (\Exception $e) {}

        // Dernière mesure pour afficher temp/hum globale
        $derniereMesure = null;
        try {
            $derniereMesure = DB::table('mesures')->latest()->first();
        } catch (\Exception $e) {}

        return view('salles', compact('user', 'salles', 'stats', 'derniereMesure'));
    }

    public function store(Request $request)
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        $request->validate(['nom' => 'required|string|max:150']);

        DB::table('salles')->insert([
            'nom'               => $request->nom,
            'code'              => $request->code,
            'localisation'      => $request->localisation,
            'responsable'       => $request->responsable,
            'capacite_serveurs' => (int) ($request->capacite_serveurs ?? 0),
            'statut'            => $request->statut ?? 'active',
            'description'       => $request->description,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return back()->with('success_salle', 'Salle ajoutée avec succès.');
    }

    public function destroy($id)
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        DB::table('salles')->where('id', $id)->delete();
        return back()->with('success_salle', 'Salle supprimée.');
    }

    public function update(Request $request, $id)
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        DB::table('salles')->where('id', $id)->update([
            'nom'               => $request->nom,
            'code'              => $request->code,
            'localisation'      => $request->localisation,
            'responsable'       => $request->responsable,
            'capacite_serveurs' => (int) ($request->capacite_serveurs ?? 0),
            'statut'            => $request->statut ?? 'active',
            'description'       => $request->description,
            'updated_at'        => now(),
        ]);

        return back()->with('success_salle', 'Salle mise à jour.');
    }
}
