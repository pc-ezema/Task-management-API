<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/login', function () {
    return response([
        'code' => 401,
        'message' => 'Token Required!'
    ], 401);
})->name('login');

Route::group(['middleware' => ['cors', 'json.response']], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forget-password', [AuthController::class, 'forget_password']);
    Route::post('/reset-password', [AuthController::class, 'reset_password']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::apiResource('tasks', TaskController::class);
        Route::post('/complete-task/{id}', [TaskController::class, 'complete']);
    });
});
