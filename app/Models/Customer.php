<?php

namespace App\Models;

use App\Data\CustomerRequestData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{

    use HasFactory;
    public static function register(CustomerRequestData $customerData): Customer
    {
        $customer = new Customer();
        $customer->name = $customerData->name;
        $customer->tel = $customerData->phone;
        $customer->email = $customerData->email;
        $customer->save();

        return $customer;
    }
    public function appointment (): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
