<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    // Define the fillable properties
    protected $fillable = [
        'teacher_id',
        'sort_id',
        'start',
        'end',
        'rrule',
    ];

    public function teacher() {
        return $this->belongsTo(Teacher::class);
    }

    public function sort() {
        return $this->belongsTo(Sort::class);
    }
}
