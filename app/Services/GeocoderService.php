<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeocoderService
{
    /**
     * Get coordinates from address using OpenStreetMap Nominatim.
     */
    public function coordinates(string $address): ?array
    {
        if (blank($address)) {
            return null;
        }

        $response = Http::withoutVerifying()
    ->withHeaders([
        'User-Agent' => 'Acrovoy/1.0',
    ])
    ->get('https://nominatim.openstreetmap.org/search', [
        'q' => $address,
        'format' => 'jsonv2',
        'limit' => 1,
    ]);

        if (!$response->successful()) {
            return null;
        }

        $results = $response->json();

        if (empty($results)) {
            return null;
        }

        return [
            'lat' => (float) $results[0]['lat'],
            'lon' => (float) $results[0]['lon'],
            'display_name' => $results[0]['display_name'] ?? null,
        ];
    }
}