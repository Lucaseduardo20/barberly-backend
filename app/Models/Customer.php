<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{

    public function register($customer)
    {
        
    }
    public function appointment (): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
