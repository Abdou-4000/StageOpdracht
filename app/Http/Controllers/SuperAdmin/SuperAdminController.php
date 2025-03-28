<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
    }

    public function index()
    {
        return Inertia::render('SuperAdmin/Dashboard', [
            'teachers' => User::role('teacher')->with('roles')->get(),
            'roles' => Role::where('name', '!=', 'super_admin')->get()
        ]);
    }

    public function syncRoles(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name'
        ]);

        $user->syncRoles($validated['roles']);
        return back()->with('success', 'Teacher roles updated successfully');
    }
}