<?php

namespace App\Http\Controllers;
use App\Data\UserData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::query()->where('email',$request->email)->get();
        return response()->json(['token' => $token, 'user' => UserData::fromUser($user[0])]);
    }

    public function register(Request $request): JsonResponse
    {
//        $request->validate([
//            'name' => 'required|string|max:255',
//            'email' => 'required|string|email|max:255|unique:users',
//            'password' => 'required|string|min:8',
//        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->company_id = 1;
        $user->role = 'admin';
        $user->tel = '11954065757';
        $user->password = Hash::make($request->password);
        $user->commission = 0;
        $user->percentage = 0;
        $user->save();



        $token = JWTAuth::fromUser($user);

        return response()->json(['token' => $token]);
    }
}

