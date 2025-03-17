<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::group(['prefix' => 'customer'], function () {
    Route::post('register', [CustomerController::class, 'store']);
});
Route::post('appointments', [AppointmentController::class, 'store'])->name('appointments.store');

Route::middleware('auth:api')->group(function () {
    Route::group(['prefix' => 'appointments'], function () {
        Route::get('/', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/{id}', [AppointmentController::class, 'show'])->name('appointments.show');
        Route::put('/{id}', [AppointmentController::class, 'update'])->name('appointments.update');
        Route::post('/cancel', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
        Route::post('/done', [AppointmentController::class, 'done'])->name('appointments.done');
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('/preview', [UserController::class, 'get_preview'])->name('user.get_preview');
    });
});
