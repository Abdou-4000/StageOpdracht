<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use Inertia\Inertia;

class AgendaController extends Controller
{
    public function index(Teacher $teacher) {
        return Inertia::render('Agenda', [
            'teacherId' => $teacher->id,
        ]);
    }
}
