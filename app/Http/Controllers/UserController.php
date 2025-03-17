<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct(private readonly UserService $service)
    {
    }

    public function get_preview(Request $request)
    {
        return response()->json($this->service->preview($request->user()));
    }
}
