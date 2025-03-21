<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
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

    public function categories() {
        return $this->belongsToMany(Category::class);
    }
}
