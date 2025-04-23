<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserOwnsDataOrIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Admin can do anything
        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            return $next($request);
        }

        // Get the teacher's record associated with the authenticated user
        $teacher = $user->teacher; // Assuming the teacher model has the 'user_id' column as a foreign key

        if ($teacher) {
            // Teacher can only modify their own data
            $routeId = $request->route('id') ?? $request->route('teacher_id');
            
            // Check if the teacher's user_id matches the one in the route
            if ((int) $routeId === $teacher->id) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized');
    }
}
