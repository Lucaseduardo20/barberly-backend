<?php

namespace App\Http\Controllers;

use App\Data\CustomerRequestData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store (CustomerRequestData $request)
    {
        dd($request);
    }
}
