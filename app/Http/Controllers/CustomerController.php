<?php

namespace App\Http\Controllers;

use App\Data\CustomerRequestData;
use App\Data\BarberData;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function store (CustomerRequestData $request): JsonResponse
    {
       $customer = Customer::register($request);
       return response()->json(['customer_id' => $customer->id], 201);
    }

    public function get_barbers (): JsonResponse
    {
        return response()->json(BarberData::collect(
            User::query()->where('role', 'barber')
                ->get()
                ->map(fn ($barber) => BarberData::fromUser($barber))
        ), 200);
    }
}
