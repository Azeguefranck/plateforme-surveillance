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

        $salleId     = request()->query('salle_id');
        $salleActive = null;
        $equipements = $salles = collect();
        $stats = ['total'=>0,'actif'=>0,'maintenance'=>0,'panne'=>0,'hors_ligne'=>0];

        try {
            $equipements = DB::table('serveurs')
                ->when($salleId, fn($q) => $q->where('salle_id', (int)$salleId))
                ->orderByDesc('created_at')->get();
            $salles = DB::table('salles')->get();

            if ($salleId) {
                $salleActive = $salles->firstWhere('id', (int)$salleId);
            }

            $q = fn() => DB::table('serveurs')->when($salleId, fn($q) => $q->where('salle_id', (int)$salleId));
            $stats = [
                'total'       => $q()->count(),
                'actif'       => $q()->where('statut','actif')->count(),
                'maintenance' => $q()->where('statut','maintenance')->count(),
                'panne'       => $q()->whereIn('statut',['en_panne','critique'])->count(),
                'hors_ligne'  => $q()->whereIn('statut',['hors_ligne','deconnecte','inactif'])->count(),
            ];
        } catch (\Exception $e) {}

        return view('serveurs', compact('user','equipements','salles','stats','salleActive'));
    }

    public function store(Request $request)
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        $request->validate(['nom'=>'required|string|max:150','type'=>'required|string']);

        DB::table('serveurs')->insert($this->fields($request) + [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success_srv', 'Équipement ajouté avec succès.');
    }

    public function destroy($id)
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        DB::table('serveurs')->where('id', $id)->delete();
        return back()->with('success_srv', 'Équipement supprimé.');
    }

    public function update(Request $request, $id)
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        DB::table('serveurs')->where('id', $id)->update($this->fields($request) + [
            'updated_at' => now(),
        ]);

        return back()->with('success_srv', 'Équipement mis à jour.');
    }

    private function fields(Request $r): array
    {
        return [
            'nom'                    => $r->nom,
            'categorie'              => $r->categorie,
            'type'                   => $r->type,
            'marque'                 => $r->marque,
            'modele'                 => $r->modele,
            'numero_serie'           => $r->numero_serie,
            'code_inventaire'        => $r->code_inventaire,
            'adresse_ip'             => $r->adresse_ip,
            'adresse_mac'            => $r->adresse_mac,
            'masque_reseau'          => $r->masque_reseau,
            'passerelle'             => $r->passerelle,
            'vlan'                   => $r->vlan,
            'port_reseau'            => $r->port_reseau,
            'salle_id'               => $r->salle_id ?: null,
            'numero_rack'            => $r->numero_rack,
            'rack'                   => $r->rack,
            'position_rack'          => $r->position_rack,
            'localisation_physique'  => $r->localisation_physique,
            'statut'                 => $r->statut ?? 'actif',
            'criticite'              => $r->criticite ?? 'normale',
            'systeme_exploitation'   => $r->systeme_exploitation,
            'version_os'             => $r->version_os,
            'version_firmware'       => $r->version_firmware,
            'consommation_energetique'=> $r->consommation_energetique,
            'tension_alimentation'   => $r->tension_alimentation,
            'stockage'               => $r->stockage,
            'ram'                    => $r->ram,
            'cpu'                    => $r->cpu,
            'nombre_ports'           => $r->nombre_ports ?: null,
            'debit_reseau'           => $r->debit_reseau,
            'autonomie_batterie'     => $r->autonomie_batterie,
            'date_installation'      => $r->date_installation ?: null,
            'derniere_maintenance'   => $r->derniere_maintenance ?: null,
            'prochaine_maintenance'  => $r->prochaine_maintenance ?: null,
            'garantie'               => $r->garantie,
            'fournisseur'            => $r->fournisseur,
            'responsable'            => $r->responsable,
            'technicien_responsable' => $r->technicien_responsable,
            'description'            => $r->description,
        ];
    }
}
