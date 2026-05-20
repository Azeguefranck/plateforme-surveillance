<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeoController extends Controller
{
    const TTL = 86400; // 24h

    public function states(string $country)
    {
        $data = Cache::remember('geo_states_'.md5($country), self::TTL, function () use ($country) {
            try {
                $r = Http::timeout(8)->post('https://countriesnow.space/api/v0.1/countries/states', [
                    'country' => $country,
                ]);
                if ($r->successful() && !($r->json()['error'] ?? true)) {
                    return collect($r->json()['data']['states'] ?? [])
                        ->pluck('name')->filter()->sort()->values()->toArray();
                }
            } catch (\Exception $e) {}
            return [];
        });
        return response()->json($data);
    }

    public function cities(string $country)
    {
        $data = Cache::remember('geo_cities_'.md5($country), self::TTL, function () use ($country) {
            try {
                $r = Http::timeout(12)->post('https://countriesnow.space/api/v0.1/countries/cities', [
                    'country' => $country,
                ]);
                if ($r->successful() && !($r->json()['error'] ?? true)) {
                    return collect($r->json()['data'] ?? [])
                        ->filter()->sort()->values()->toArray();
                }
            } catch (\Exception $e) {}
            return [];
        });
        return response()->json($data);
    }

    public function stateCities(string $country, string $state)
    {
        $data = Cache::remember('geo_sc_'.md5($country.$state), self::TTL, function () use ($country, $state) {
            try {
                $r = Http::timeout(10)->post('https://countriesnow.space/api/v0.1/countries/state/cities', [
                    'country' => $country,
                    'state'   => $state,
                ]);
                if ($r->successful() && !($r->json()['error'] ?? true)) {
                    return collect($r->json()['data'] ?? [])
                        ->filter()->sort()->values()->toArray();
                }
            } catch (\Exception $e) {}
            return [];
        });
        return response()->json($data);
    }

    // Arrondissements : tries the city name as a "state" sub-query
    public function subcities(string $country, string $city)
    {
        $data = Cache::remember('geo_sub_'.md5($country.$city), self::TTL, function () use ($country, $city) {
            try {
                // First attempt: treat city as a state subdivision
                $r = Http::timeout(10)->post('https://countriesnow.space/api/v0.1/countries/state/cities', [
                    'country' => $country,
                    'state'   => $city,
                ]);
                if ($r->successful() && !($r->json()['error'] ?? true)) {
                    $items = collect($r->json()['data'] ?? [])->filter()->sort()->values()->toArray();
                    if (count($items) > 0) return $items;
                }
                // Second attempt: search cities of the country containing the city name
                $r2 = Http::timeout(10)->post('https://countriesnow.space/api/v0.1/countries/cities', [
                    'country' => $country,
                ]);
                if ($r2->successful() && !($r2->json()['error'] ?? true)) {
                    $all = collect($r2->json()['data'] ?? [])->filter()->values()->toArray();
                    $prefix = strtolower($city);
                    $filtered = collect($all)->filter(function($c) use ($prefix) {
                        return stripos($c, $prefix) !== false && strtolower($c) !== $prefix;
                    })->sort()->values()->toArray();
                    return $filtered;
                }
            } catch (\Exception $e) {}
            return [];
        });
        return response()->json($data);
    }
}
