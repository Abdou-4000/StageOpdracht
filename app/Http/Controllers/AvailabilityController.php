<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Availability;
use App\Models\Sort;

class AvailabilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index ($id) {
        
        $availabilities = Availability::with('sort')->where('teacher_id', $id)->get();

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

    /**
     * Deletes the old saved week and saves the new week
     */
    public function storeEvents ($id, Request $request) {

        DB::table('availabilities')->where('teacher_id', $id)->delete();
    
        $validatedData = $request->validate([
            '*.title' => 'required|string|max:255',
            '*.start' => 'required|date_format:H:i:s',
            '*.end' => 'required|date_format:H:i:s',
            '*.rrule' => 'required|string',
        ]);
         
        // Loop through the validated data and store the events
        foreach ($validatedData as $event) {
            // Find the Sort that matches the event title
            $sort = Sort::where('name', $event['title'])->first();
             
            // If the Sort is found, use its id
            if ($sort) {
                $sortId = $sort->id;
                 
                Availability::create([
                    'teacher_id' => $id,
                    'sort_id' => $sortId,
                    'start' => $event['start'],
                    'end' => $event['end'],
                    'rrule' => $event['rrule'],
                ]);
            }
        }
         
        return response()->json(['message' => 'Events saved successfully']);
    }
}
