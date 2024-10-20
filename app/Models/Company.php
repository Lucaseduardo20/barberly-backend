<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public function employee ()
    {
        return $this->belongsToMany(User::class);
    }
}
