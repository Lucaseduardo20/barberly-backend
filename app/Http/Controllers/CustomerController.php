<?php

namespace App\Http\Controllers;

use App\Data\CustomerRequestData;
use App\Data\BarberData;
use App\Models\Customer;
use App\Models\User;
use App\Support\TenantResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(private readonly TenantResolver $tenantResolver)
    {
    }

    public function store(CustomerRequestData $customerData, Request $request): JsonResponse
    {
       $company = $this->tenantResolver->resolveCompany($request);
       $customer = Customer::register($customerData, $company);
       return response()->json(['customer_id' => $customer->id], 201);
    }

    public function get_barbers(Request $request): JsonResponse
    {
        $company = $this->tenantResolver->resolveCompany($request);

        return response()->json(BarberData::collect(
            User::query()->where('role', 'barber')
                ->where('company_id', $company->id)
                ->get()
                ->map(fn ($barber) => BarberData::fromUser($barber))
        ), 200);
    }
}
