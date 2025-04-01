<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'color',
    ];

    public $timestamps = false;

    public function teachers() {
        return $this->belongsToMany(Teacher::class);
    }
}
