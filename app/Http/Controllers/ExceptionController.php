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
}
