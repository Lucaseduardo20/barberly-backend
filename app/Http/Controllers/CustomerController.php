<?php

namespace App\Http\Controllers;

use App\Data\CustomerRequestData;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function store (CustomerRequestData $request): JsonResponse
    {
       $customer = Customer::register($request);
       return response()->json(['customer_id' => $customer->id], 201);
    }
}
