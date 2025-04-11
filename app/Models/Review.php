<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id', 
        'teacher_id', 
        'rating', 
        'review',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relationship with Teacher model
    public function teacher() {
        return $this->belongsTo(Teacher::class);
    }
}
