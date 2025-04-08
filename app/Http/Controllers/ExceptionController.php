<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Exception;
use App\Models\Sort;

class ExceptionController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index () {
        // add $teacherId 
        
        $exceptions = Exception::with('sort')->where('teacher_id', 2)->get();

        return response()->json([
            'exceptions' => $exceptions->map(function ($event) {
                return [
                    'title' => $event->sort->name,
                    'start' => $event->start,
                    'end' => $event->end,
                ];
            })
        ]);
    }

    /**
     * 
     */
    public function storeExceptions (Request $request) {
        // add $teacherId
    
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date_format:Y-m-d H:i:s',
            'end' => 'required|date_format:Y-m-d H:i:s',
        ]);
         
    
        // Find the Sort that matches the event title
        $sort = Sort::where('name', $validatedData['title'])->first();
             
        // If the Sort is found, use its id
        if ($sort) {
            $sortId = $sort->id;
                 
            Exception::create([
                'teacher_id' => 2,
                'sort_id' => $sortId,
                'start' => $validatedData['start'],
                'end' => $validatedData['end'],
            ]);
        }
         
        return response()->json(['message' => 'Events saved successfully']);
    }
}
