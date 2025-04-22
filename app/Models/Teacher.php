<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Teacher extends Model
{
    use Searchable;

    // Define the fillable properties
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'companynumber',
        'companyname',
        'street',
        'streetnumber',
        'city_id',
    ];

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function category() {
        return $this->belongsToMany(Category::class);
    }

    public function availabilities() {
        return $this->hasMany(Availability::class);
    }

    public function exceptions() {
        return $this->hasMany(Exception::class);
    }

        
    public function toSearchableArray()
    {
        // Get the category names as an array
        $categories = $this->category->pluck('name')->toArray();
        
        // Get the city name
        $city = $this->city ? $this->city->name : null;
        
        // Correctly format the teacher name
        $fullName = $this->firstname . ' ' . $this->lastname;
        
        return [
            'id' => $this->id,
            'name' => $fullName,
            'compname' => $this->companyname, // Using the correct field name from fillable
            'categories' => $categories, // Use the relationship data properly
            'category' => $categories, // Duplicate for backward compatibility
            'lat' => $this->lat,
            'lng' => $this->lng,
            'email' => $this->email,
            'phone' => $this->phone,
            'street' => $this->street,
            'streetnumber' => $this->streetnumber,
            'city' => $city,
            'company_number' => $this->companynumber
        ];
    }
}
