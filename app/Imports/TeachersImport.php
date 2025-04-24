<?php

namespace App\Imports;

use App\Models\Teacher;
use App\Models\City;
use App\Models\Category;
use App\Models\User;
use App\Http\Controllers\TeacherController;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeachersImport implements ToModel, WithHeadingRow
{
    public static $added = 0;
    public static $exists = 0;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row) {
        // Fetches corresponding city entry for city_id
        $city = City::where('name', $row['city'])->first();
        $city_id = $city ? $city->id : 9999;

        // If city is not found flag the teacher
        $flagged = false;
        if (!$city) {
            $flagged = true;
        }

        // Validate firstname, lastname, email, etc.
        $firstname = (empty($row['firstname']) || strlen($row['firstname']) > 255) ? null : $row['firstname'];
        $lastname = (empty($row['lastname']) || strlen($row['lastname']) > 255) ? null : $row['lastname'];
        $email = (empty($row['email']) || !filter_var($row['email'], FILTER_VALIDATE_EMAIL) || strlen($row['email']) > 255) ? null : $row['email'];
        $phone = (empty($row['phone']) || strlen($row['phone']) < 10 || strlen($row['phone']) > 15) ? null : $row['phone'];
        $companynumber = (empty($row['companynumber']) || !preg_match('/^(BE)?\d{10}$/', $row['companynumber'])) ? null : $row['companynumber'];
        $companyname = (empty($row['companyname']) || strlen($row['companyname']) > 255) ? null : $row['companyname'];
        $street = (empty($row['street']) || strlen($row['street']) > 255) ? null : $row['street'];
        $streetnumber = (empty($row['streetnumber']) || strlen($row['streetnumber']) > 10) ? null : $row['streetnumber'];
        $userEmail = $this->generateEmail($firstname, $lastname);

        // Checks if the user already exists
        if (User::where('email', $userEmail)->exists()) {
            self::$exists++;
            return;
        } else {
            $user = User::create([
                'name' => $firstname . ' ' . $lastname,
                'email' => $userEmail,
                'password' => Hash::make(Str::random(12)),
            ]);
        
            self::$added++;
            $user->assignRole('teacher');
        }

        // If any required fields are null, flag the record
        if (is_null($firstname) || is_null($lastname) || is_null($email) || is_null($phone) || is_null($companynumber) || is_null($companyname) || is_null($street) || is_null($streetnumber)) {
            $flagged = true;
        }

        // Create the teacher record
        $teacher = Teacher::create([
            'firstname'     => $row['firstname'],
            'lastname'      => $row['lastname'],
            'email'         => $row['email'],
            'phone'         => $row['phone'],
            'companynumber' => $row['companynumber'],
            'companyname'   => $row['companyname'],
            'street'        => $row['street'],
            'streetnumber'  => $row['streetnumber'],
            'city_id'       => $city_id,
            'lat'           => null, 
            'lng'           => null,
        ]);

        $teacher->user_id = $user->id;
        $teacher->save();

        // Attach categories to teacher
        $categories = explode(',', $row['categories']);
        foreach ($categories as $categoryName) {
            $category = Category::where('name', trim($categoryName))->first();
            // Add a flag when category is not found
            if (!$category) {
                $flagged = true;
            } else {
                $teacher->category()->attach($category);
            }
        }

        $address = $row['street'].' '.$row['street'].', '.$row['city'];

        // Fetch coordinates
        $coordinates = $this->getCoordinates($address);

        // Save coordinates if found
        if (!$coordinates) {
            $flagged = true;
        } else {
            $teacher->lat = round($coordinates['lat'], 3);
            $teacher->lng = round($coordinates['lon'], 3);
            $teacher->save();
        }

        // Update flag if necessary
        if ($flagged) {
            $teacher->flagged = true;
            $teacher->save();
        }

        return $teacher;
    }

    /**
     * Fetches the coordinates for an address.
     */
    public function getCoordinates($address) {
        $url = "https://nominatim.openstreetmap.org/search?format=json&limit=3&q=".urlencode($address);

        // Send the request
        $response = Http::withHeaders([
            'User-Agent' => 'SyntraPXL map (Zoe.Dreessen@cursist.syntrapxl.be)'
        ])
        ->withoutVerifying()
        ->get($url);

        // Save the results
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

    /**
     * Sets the email to the general format
     */
    public function generateEmail($firstname, $lastname) {
        // Ensure both names are trimmed and in lowercase
        $first = strtolower(trim($firstname));
        $last = strtolower(trim($lastname));
    
        // Replace spaces with dots in the last name
        $last = preg_replace('/\s+/', '.', $last);
    
        // Generate the email preview
        $emailPreview = "{$first}-{$last}@docent.syntrapxl.be";
    
        return $emailPreview;
    }
    
}
