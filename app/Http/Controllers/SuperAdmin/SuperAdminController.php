<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{


    public function updateField(Request $request, User $user)
    {
        $validated = $request->validate([
            'field' => 'required|in:name,email',
            'value' => 'required|string'
        ]);
        
        // Additional validation for email
        if ($validated['field'] === 'email') {
            $request->validate([
                'value' => 'email|unique:users,email,' . $user->id
            ]);
        }
        
        $user->{$validated['field']} = $validated['value'];
        $user->save();
        
        return response()->json(['success' => true]);
    }
    public function updateAccess(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);
        
        // Sync the role first
        $user->syncRoles([$validated['role']]);
        
        // Check if super_admin
        if ($validated['role'] === 'super_admin') {
            // Super admins don't need direct permissions
            $user->syncPermissions([]);
        } else {
            // Sync any additional direct permissions
            $user->syncPermissions($validated['permissions'] ?? []);
        }
        
        return back()->with('success', 'User access updated successfully');
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8',
        ]);
        
        $user->password = Hash::make($validated['password']);
        $user->save();
        
        return back()->with('success', 'Password has been reset successfully');
    }

    public function syncRoles(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|exists:roles,name' // Changed from 'roles' array to single 'role'
        ]);
    
        $user->syncRoles([$validated['role']]); // Assign only one role
        return back()->with('success', 'User role updated successfully');
    }
    
    public function index()
    {
        // Get role permissions mapping to show in the UI
        $rolePermissions = [];
        $roles = Role::all();
        
        foreach ($roles as $role) {
            $rolePermissions[$role->name] = $role->permissions->pluck('name');
        }
        
        return view('super-admin.dashboard', [
            'users' => User::with('roles', 'permissions')->get(),
            'roles' => $roles,
            'permissions' => Permission::all(),
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function syncPermissions(Request $request, User $user)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $user->syncPermissions($validated['permissions'] ?? []);
        return back()->with('success', 'User permissions updated successfully');
    }
    
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name'  // Single role instead of array
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        
        $user->assignRole($validated['role']);
        
        return back()->with('success', 'User created successfully');
    }
    
    public function deleteUser(User $user)
    {
        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account');
        }
        
        $user->delete();
        return back()->with('success', 'User deleted successfully');
    }
}