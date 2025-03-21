<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $teachers = Teacher::all(); 
        return view('teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        return view('teachers.create'); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        teacher::create([
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'companynumber' => $request->input('companynumber'),
            'companyname' => $request->input('companyname'),
            'street' => $request->input('street'),
            'streetnumber' => $request->input('streetnumber'),
            'city_id' => $request->input('city_id'),
        ]);
 
        return redirect()->route('teachers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher) {
        return view('teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher) {
        $teacher->update([
            'firstname' => $request->input('firstname'),
        ]);
        return redirect()->route('teachers.index'); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher) {
        $teacher->delete();
        return redirect()->route('teachers.index');
    }
}
