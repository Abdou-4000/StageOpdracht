<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Teacher;
use App\Models\City;
use App\Models\Category;
use App\Models\User;
use App\Services\TeacherDataService;
use App\Imports\TeachersImport;

class TeacherController extends Controller
{
    /**
     * Constructor.
     */
    protected $teacherDataService;

    public function __construct(TeacherDataService $teacherDataService) {
        $this->teacherDataService = $teacherDataService;
    }

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
            return back()->withErrors(['city_name' => 'Stad niet gevonden.'])->withInput();
        }

        // Validate inputs
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|min:10|max:15',
            'companynumber' => 'required|regex:/^(BE)?\d{10}$/',
            'companyname' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'streetnumber' => 'required|string|max:10',
            'login_email' => 'required|email|unique:users,email',
        ]);

        $user = User::create([
            'name' => $request['firstname'] . ' ' . $request['lastname'],
            'email' => $request['login_email'],
            'password' => Hash::make(Str::random(12)),
        ]);

        $user->assignRole('teacher');

        // Create the teacher
        $teacher = Teacher::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'companynumber' => $request->companynumber,
            'companyname' => $request->companyname,
            'street' => $request->street,
            'streetnumber' => $request->streetnumber,
            'city_id' => $city->id,
        ]);

        $teacher->user_id = $user->id;
        $teacher->save();
 
        // Attach categories if selected
        if ($request->has('categories')) {
            $teacher->category()->attach($request->categories);
        }

        // Construct the address string
        $address = $request->input('street').' '.$request->input('streetnumber').', '.$request->input('city_name');

        // Fetch coordinates
        $coordinates = $this->teacherDataService->getCoordinates($address);

        // Save coordinates if found
        if (!$coordinates) {
            $teacher->flagged = true;
            $teacher->save();
        } else {
            $teacher->lat = round($coordinates['lat'], 3);
            $teacher->lng = round($coordinates['lon'], 3);
            $teacher->flagged = false;
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
            return back()->withErrors(['city_name' => 'Stad niet gevonden.'])->withInput();
        }

        // Validate inputs
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|min:10|max:15',
            'companynumber' => 'required|regex:/^(BE)?\d{10}$/',
            'companyname' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'streetnumber' => 'required|string|max:10',
        ]);

        // Update teacher
        $teacher->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'companynumber' => $request->companynumber,
            'companyname' => $request->companyname,
            'street' => $request->street,
            'streetnumber' => $request->streetnumber,
            'city_id' => $city->id,
        ]);

        // Update teachers categories
        if ($request->has('categories')) {
            $teacher->category()->sync($request->categories);
        }

        // Construct the address string
        $address = $request->input('street').' '.$request->input('streetnumber').', '.$request->input('city_name');
        $flagged = false;

        // Fetch coordinates
        $coordinates = $this->teacherDataService->getCoordinates($address);

        // Save coordinates if found
        if (!$coordinates) {
            $teacher->lat = null;
            $teacher->lng = null;
            $teacher->flagged = true;
            $teacher->save();
        } else {
            $teacher->lat = round($coordinates['lat'], 3);
            $teacher->lng = round($coordinates['lon'], 3);
            $teacher->flagged = false;
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

    /**
     * csv import.
     */
    public function import(Request $request) {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        Excel::import(new TeachersImport, $request->file('file'));

        return back()->with('success', 'Teachers imported successfully.');
    }
}
