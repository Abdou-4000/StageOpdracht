<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sort extends Model
{
    public function availabilities() {
        return $this->hasMany(Availability::class);
    }

    public function exceptions() {
        return $this->hasMany(Exception::class);
    }
}
