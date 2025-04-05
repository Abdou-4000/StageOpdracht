<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Availability;
use App\Models\Sort;

class AvailabilityController extends Controller
{
    public function index () {
        
        $availabilities = Availability::with('sort')->where('teacher_id', 2)->get();

        $sort = Sort::get();

        return response()->json([
            'sorts' => $sort,
            'availabilities' => $availabilities->map(function ($event) {
                return [
                    'title' => $event->sort->name,
                    'start' => $event->start,
                    'end' => $event->end,
                    'rrule' => $event->rrule,
                ];
            })
        ]);
    }

    public function storeEvents () {
        // Overwrite the events
    }
}
