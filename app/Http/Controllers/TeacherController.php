<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\City;
use App\Models\Category;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $teachers = Teacher::with('city', 'category')->get(); 
        return view('teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        $categories = Category::get();
        return view('teachers.create', compact('categories')); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        // Look for the city id matching the city name
        $city = City::where('name', $request->city_name)->first();

        // Return error if city name is incorrect
        if (!$city) {
            return back()->withErrors(['city_name' => 'City not found.'])->withInput();
        }

        $teacher = Teacher::create([
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'companynumber' => $request->input('companynumber'),
            'companyname' => $request->input('companyname'),
            'street' => $request->input('street'),
            'streetnumber' => $request->input('streetnumber'),
            'city_id' => $city->id,
        ]);
 
        if ($request->has('categories')) {
            $teacher->category()->attach($request->categories);
        }

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
    public function edit($id) {
        $teacher = Teacher::with('category')->findOrFail($id);
        $categories = Category::get();
        return view('teachers.edit', compact('teacher', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher) {
        // Look for the city id matching the city name
        $city = City::where('name', $request->city_name)->first();

        // Return error if city name is incorrect
        if (!$city) {
            return back()->withErrors(['city_name' => 'City not found.'])->withInput();
        }

        $teacher->update([
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'companynumber' => $request->input('companynumber'),
            'companyname' => $request->input('companyname'),
            'street' => $request->input('street'),
            'streetnumber' => $request->input('streetnumber'),
            'city_id' => $city->id,
        ]);

        if ($request->has('categories')) {
            $teacher->category()->sync($request->categories);
        }

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
