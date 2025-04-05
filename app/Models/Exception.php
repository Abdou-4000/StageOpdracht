<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exception extends Model
{
    public function teacher() {
        return $this->belongsTo(Teacher::class);
    }

    public function sort() {
        return $this->belongsTo(Sort::class);
    }
}
