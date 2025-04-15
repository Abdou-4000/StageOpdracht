<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Category;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index () {
        // Get the logged-in user's ID
        $userId = auth()->id();

        // Find the teacher profile associated with the user or fail
        $teacher = Teacher::with('category')->where('user_id', $userId)->firstOrFail();

        // Pass the teacher profile data to the Inertia page
        return Inertia::render('ProfileTeacher', [
            'teacher' => $teacher
        ]);
    }
}
