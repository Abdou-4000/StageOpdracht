<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use Inertia\Inertia;

class AgendaController extends Controller
{
    public function index(Teacher $teacher) {
        $user = auth()->user();
        $roles = $user->getRoleNames();

        if ($user->hasRole('admin')) {
            // Admins can access any teacher's agenda
            return Inertia::render('Agenda', [
                'teacherId' => $teacher->id,
                'userRoles' => $roles,
            ]);
        }
        
        if ($user->hasRole('teacher') && $user->id === $teacher->user_id) {
            // Teachers can only access their own agenda
            return Inertia::render('Agenda', [
                'teacherId' => $teacher->id,
                'userRoles' => $roles,
            ]);
        }

        // Everyone else gets denied
        abort(403, 'Unauthorized');
    }
}
