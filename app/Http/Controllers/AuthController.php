<?php

namespace App\Http\Controllers;
use App\Data\UserData;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Support\TenantResolver;

class AuthController extends Controller
{
    public function __construct(private readonly TenantResolver $tenantResolver)
    {
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['token' => $token, 'user' => UserData::fromUser(Auth::guard('api')->user())]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'tel' => ['nullable', 'string', 'max:30'],
            'role' => ['nullable', Rule::in(['admin', 'barber', 'manager'])],
            'percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'company_tel' => ['nullable', 'string', 'max:30'],
        ]);

        $user = DB::transaction(function () use ($request, $validated) {
            if ($request->filled('company_name')) {
                $company = Company::query()->create([
                    'name' => $validated['company_name'],
                    'address' => $validated['company_address'] ?? '',
                    'tel' => $validated['company_tel'] ?? $validated['tel'] ?? null,
                ]);
            } else {
                $company = $this->tenantResolver->resolveCompany($request);
            }

            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->company_id = $company->id;
            $user->role = $validated['role'] ?? ($request->filled('company_name') ? 'admin' : 'barber');
            $user->tel = $validated['tel'] ?? null;
            $user->password = Hash::make($validated['password']);
            $user->commission = 0;
            $user->percentage = $validated['percentage'] ?? 0;
            $user->save();

            return $user;
        });

        $token = JWTAuth::fromUser($user);

        return response()->json(['token' => $token, 'user' => UserData::fromUser($user)], 201);
    }
}
