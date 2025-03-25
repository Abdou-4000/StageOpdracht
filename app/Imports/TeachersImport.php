<?php

namespace App\Imports;

use App\Models\Teacher;
use App\Models\City;
use App\Models\Category;
use App\Http\Controllers\TeacherController;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeachersImport implements ToModel, WithHeadingRow
{
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

        // Make services
        // $address = $row['street'].' '.$row['street'].', '.$row['city'];

        // // Fetch coordinates
        // $coordinates = $this->getCoordinates($address);

        // // Save coordinates if found
        // if (!$coordinates) {
        //     $flagged = true;
        // } else {
        //     $teacher->lat = round($coordinates['lat'], 3);
        //     $teacher->lng = round($coordinates['lon'], 3);
        //     $teacher->save();
        // }

        // Update flag if necessary
        if ($flagged) {
            $teacher->flagged = true;
            $teacher->save();
        }

        return $teacher;
    }
}
