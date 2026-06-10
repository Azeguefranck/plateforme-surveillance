<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeoController extends Controller
{
    private function u(): string
    {
        return config('services.geonames.username', 'demo');
    }

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

    public function getRegions(int $geonameId)
    {
        $data = Cache::remember("geo_reg_{$geonameId}", 60 * 24 * 7, function () use ($geonameId) {
            return $this->children($geonameId);
        });
        return response()->json($data);
    }

    public function getDepartements(int $geonameId)
    {
        $data = Cache::remember("geo_dept_{$geonameId}", 60 * 24 * 7, function () use ($geonameId) {
            return $this->children($geonameId);
        });
        return response()->json($data);
    }

    public function getArrondissements(int $geonameId)
    {
        $data = Cache::remember("geo_arr_{$geonameId}", 60 * 24 * 7, function () use ($geonameId) {
            $rows = $this->children($geonameId);
            if (!empty($rows)) return $rows;

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

    public function getVilles(int $geonameId)
    {
        $data = Cache::remember("geo_villes_{$geonameId}", 60 * 24 * 7, function () use ($geonameId) {
            $kids = $this->children($geonameId);
            if (!empty($kids)) return $kids;
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

    private function children(int $geonameId): array
    {
        try {
            $u    = $this->u();
            $resp = Http::timeout(15)->get(
                "http://api.geonames.org/childrenJSON",
                ['geonameId' => $geonameId, 'username' => $u, 'lang' => 'fr']
            );
            $json = $resp->json();
            if (isset($json['status'])) return [];
            $rows = $json['geonames'] ?? [];
            return array_map(fn($r) => ['id' => (int) $r['geonameId'], 'nom' => $r['name']], $rows);
        } catch (\Throwable $e) {
            return [];
        }
    }
}
