<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', function () {
    return response()->json([
        'message' => 'Unauthorized'
    ],401);
})->name('login');



Route::group(['middleware' => 'auth:admin'], function ($routers) {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/get_response_user', [AuthController::class, 'getResponseUser']);
});
