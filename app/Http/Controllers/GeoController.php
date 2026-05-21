<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

// [NOUVEAU] Proxy GeoNames — protège le username côté serveur
class GeoController extends Controller
{
    private function u(): string
    {
        return config('services.geonames.username', 'demo');
    }

    // [NOUVEAU] Liste des pays avec geonameId (cache 30 jours)
    public function getPays()
    {
        $data = Cache::remember('geo_pays_v1', 60 * 24 * 30, function () {
            $u    = $this->u();
            $resp = Http::timeout(15)->get(
                "http://api.geonames.org/countryInfoJSON?username={$u}&lang=fr"
            );
            $rows = $resp->json()['geonames'] ?? [];
            $out  = [];
            foreach ($rows as $c) {
                $out[] = [
                    'id'  => (int) $c['geonameId'],
                    'nom' => $c['countryName'],
                    'iso' => strtoupper($c['countryCode']),
                ];
            }
            usort($out, fn($a, $b) => strcmp($a['nom'], $b['nom']));
            return $out;
        });
        return response()->json($data);
    }

    // [NOUVEAU] Régions (ADM1) d'un pays — cache 7 jours
    public function getRegions(int $geonameId)
    {
        $data = Cache::remember("geo_reg_{$geonameId}", 60 * 24 * 7, function () use ($geonameId) {
            return $this->children($geonameId);
        });
        return response()->json($data);
    }

    // [NOUVEAU] Départements (ADM2) d'une région — cache 7 jours
    public function getDepartements(int $geonameId)
    {
        $data = Cache::remember("geo_dept_{$geonameId}", 60 * 24 * 7, function () use ($geonameId) {
            return $this->children($geonameId);
        });
        return response()->json($data);
    }

    // [NOUVEAU] Arrondissements (ADM3) — GeoNames puis fallback local (ex: Cameroun)
    public function getArrondissements(int $geonameId)
    {
        $data = Cache::remember("geo_arr_{$geonameId}", 60 * 24 * 7, function () use ($geonameId) {
            // Essai GeoNames
            $rows = $this->children($geonameId);
            if (!empty($rows)) return $rows;

            // Fallback : base locale Cameroun
            $local = config('geo_cameroun');
            if (isset($local[$geonameId])) {
                return array_map(
                    fn($n) => ['id' => 0, 'nom' => $n],
                    $local[$geonameId]
                );
            }
            return [];
        });
        return response()->json($data);
    }

    // [NOUVEAU] Villes (P) d'un arrondissement — fallback searchJSON si vide
    public function getVilles(int $geonameId)
    {
        $data = Cache::remember("geo_villes_{$geonameId}", 60 * 24 * 7, function () use ($geonameId) {
            // Essaie d'abord les enfants directs
            $kids = $this->children($geonameId);
            if (!empty($kids)) return $kids;
            // Fallback : lieux peuplés dans la zone
            $u    = $this->u();
            $resp = Http::timeout(15)->get(
                "http://api.geonames.org/searchJSON",
                ['geonameId' => $geonameId, 'featureClass' => 'P', 'maxRows' => 200, 'username' => $u, 'lang' => 'fr']
            );
            $rows = $resp->json()['geonames'] ?? [];
            return array_map(fn($r) => ['id' => (int) $r['geonameId'], 'nom' => $r['name']], $rows);
        });
        return response()->json($data);
    }

    // [NOUVEAU] Appel générique childrenJSON — retourne [] si erreur/rate-limit
    private function children(int $geonameId): array
    {
        try {
            $u    = $this->u();
            $resp = Http::timeout(15)->get(
                "http://api.geonames.org/childrenJSON",
                ['geonameId' => $geonameId, 'username' => $u, 'lang' => 'fr']
            );
            $json = $resp->json();
            // Détecter les erreurs GeoNames (rate-limit, username invalide…)
            if (isset($json['status'])) return [];
            $rows = $json['geonames'] ?? [];
            return array_map(fn($r) => ['id' => (int) $r['geonameId'], 'nom' => $r['name']], $rows);
        } catch (\Throwable $e) {
            return [];
        }
    }
}
