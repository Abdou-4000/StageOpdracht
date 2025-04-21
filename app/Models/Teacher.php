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

    public function user() {
        return $this->belongsTo(User::class);
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
}
