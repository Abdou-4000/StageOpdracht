<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;

class MapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $teacher = Teacher::with(['city', 'category']) 
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get();

            return response()->json([
                'teachers' => $teacher->map(function($teacher) {
                    return [
                        'id' => $teacher->id,
                        'name' => $teacher->firstname . ' ' . $teacher->lastname,
                        'lat' => (float)$teacher->lat,
                        'lng' => (float)$teacher->lng,
                        'compname' => $teacher->companyname ?? 'Uncategorized',
                        'category' => $teacher->category->pluck('name')->toArray(), 
                        'details' => [
                            'location' => $teacher->street . ' ' . $teacher->streetnumber,
                            'email' => $teacher->email,
                            'phone' => $teacher->phone,
                            'hours' => 'Contact for availability',
                        ]
                    ];
                })
            ]);
    }
}
