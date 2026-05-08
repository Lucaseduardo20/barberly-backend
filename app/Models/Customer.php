<?php

namespace App\Models;

use App\Data\CustomerRequestData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{

    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'tel',
    ];

    public static function register(CustomerRequestData $customerData, Company $company): Customer
    {
        $customer = new Customer();
        $customer->company_id = $company->id;
        $customer->name = $customerData->name;
        $customer->tel = $customerData->phone;
        $customer->email = $customerData->email;
        $customer->save();

        return $customer;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
