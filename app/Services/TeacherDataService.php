<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TeacherDataService
{
    /**
     * Fetches the coordinates for an address.
     */
    public function getCoordinates($address) {
        $url = "https://nominatim.openstreetmap.org/search?format=json&limit=3&q=".urlencode($address);

        // Send the request
        $response = Http::withHeaders([
            'User-Agent' => 'SyntraPXL map (Zoe.Dreessen@cursist.syntrapxl.be)'
        ])
        ->withoutVerifying()
        ->get($url);

        // Save the results
        $coordinates = $response->json();

        // Check if coordinates are found in the response
        if (isset($coordinates[0]['lat']) && isset($coordinates[0]['lon'])) {
            return [
                'lat' => $coordinates[0]['lat'],
                'lon' => $coordinates[0]['lon']
            ];
        }

        return null;
    }
}