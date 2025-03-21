<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function teachers() {
        return $this->belongsToMany(Teacher::class);
    }
}
