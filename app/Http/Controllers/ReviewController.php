<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function saveReview(Request $request) {

        $request->validate([
            'rating' => 'required|numeric|min:0|max:5',
            'review' => 'nullable|string|max:255',
        ]);

        // Add user_id/teacher_id 
        Review::create([
            'user_id' => 2,
            'teacher_id' => 2,
            'rating' => $request['rating'],
            'review' => $request['review'],
        ]);
         
    }
}
