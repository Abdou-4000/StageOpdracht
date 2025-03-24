<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    public function teachers() {
        return $this->belongsToMany(Teacher::class);
    }
}
