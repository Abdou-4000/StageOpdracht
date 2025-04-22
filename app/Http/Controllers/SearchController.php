<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchTeachers(Request $request)
    {
        $query = $request->input('query', '');
        $category = $request->input('category', 'all');
        
        // Start with Algolia search
        $searchQuery = Teacher::search($query);
        
        // Apply category filter if needed
        if ($category !== 'all') {
            $searchQuery->whereIn('categories', [$category]);
        }
        
        // Get the results
        $teachers = $searchQuery->get();
        
        // Handle radius filtering in PHP
        if ($request->has('userLat') && $request->has('userLng')) {
            $userLat = $request->input('userLat');
            $userLng = $request->input('userLng');
            $radius = $request->input('radius', 15);
            
            $teachers = $teachers->filter(function ($teacher) use ($userLat, $userLng, $radius) {
                if (!isset($teacher->lat) || !isset($teacher->lng)) {
                    return false;
                }
                
                $distance = $this->calculateDistance(
                    $userLat, $userLng, $teacher->lat, $teacher->lng
                );
                
                return $distance <= $radius;
            });
        }
        
        return response()->json(['teachers' => $teachers]);
    }
    
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Same calculation as in your Vue component
        $R = 6371;
        $dLat = ($lat2 - $lat1) * M_PI / 180;
        $dLon = ($lon2 - $lon1) * M_PI / 180;
        $a = sin($dLat/2) * sin($dLat/2) +
             cos($lat1 * M_PI / 180) * cos($lat2 * M_PI / 180) * 
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
}