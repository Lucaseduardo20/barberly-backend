<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CustomerController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('appointments', [AppointmentController::class, 'store']);


Route::middleware('auth:api')->group(function () {
    Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/{id}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::put('appointments/{id}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::post('appointments/cancel', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::post('appointments/done', [AppointmentController::class, 'done'])->name('appointments.done');
});

Route::group(['prefix' => 'customer'], function () {
    Route::post('register', [CustomerController::class, 'store']);
});
