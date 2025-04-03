<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Availability;
use App\Models\Sort;

class AvailabilityController extends Controller
{
    public function index() {
        
        $availabilities = Availability::with('sort')->where('teacher_id', 2)->get();

        return response()->json($availabilities->map(function ($event) {
            return [
                'title' => $event->sort->name, // Example: "school"
                'start' => date('H:i:s', strtotime($event->start)),
                'end' => date('H:i:s', strtotime($event->end)),
                'rrule' => $event->rrule,
            ];
        }));
    }
}
