<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    public function teachers() {
        return $this->hasMany(Teacher::class);
    }
}
