<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

        // Create the teacher
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
 
        // Attach categories if selected
        if ($request->has('categories')) {
            $teacher->category()->attach($request->categories);
        }

        // Construct the address string
        $address = $request->input('street').' '.$request->input('streetnumber').', '.$request->input('city_name');

        // Fetch coordinates
        $coordinates = $this->getCoordinates($address);

        // Round coordinates to 3 decimal places
        if ($coordinates) {
            $teacher->lat = round($coordinates['lat'], 3);
            $teacher->lng = round($coordinates['lon'], 3);
            $teacher->save();
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

        // Update teacher
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

        // Update teachers categories
        if ($request->has('categories')) {
            $teacher->category()->sync($request->categories);
        }

        // Construct the address string
        $address = $request->input('street').' '.$request->input('streetnumber').', '.$request->input('city_name');

        // Fetch coordinates
        $coordinates = $this->getCoordinates($address);

        // Round coordinates to 3 decimal places
        if ($coordinates) {
            $teacher->lat = round($coordinates['lat'], 3);
            $teacher->lng = round($coordinates['lon'], 3);
            $teacher->save();
        }

        return redirect()->route('teachers.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher) {
        $teacher->category()->detach();
        $teacher->delete();
        return redirect()->route('teachers.index');
    }

    public function getCoordinates($address) {
        $url = "https://nominatim.openstreetmap.org/search?format=json&limit=3&q=".urlencode($address);

        $response = Http::withHeaders([
            'User-Agent' => 'SyntraPXL map (Zoe.Dreessen@cursist.syntrapxl.be)'
        ])->get($url);

        $coordinates = $response->json();

        // Check if coordinates are found in the response
        if (isset($coordinates[0]['lat']) && isset($coordinates[0]['lon'])) {
            return [
                'lat' => $coordinates[0]['lat'],
                'lon' => $coordinates[0]['lon']
            ];
        }

        return null;
    }
}
