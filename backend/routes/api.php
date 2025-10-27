<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('users', UserController::class)->except(['index', 'update']);

    ROute::get('get-all-projects', [ProjectController::class, 'getAllProjects']);

    Route::post('users/update-user/{user}', [UserController::class, 'update']);
    Route::post('update-profile', [UserController::class, 'updateProfile']);
    Route::get('get-all-users', [UserController::class, 'getAllUsers']);
    Route::get('get-users', [UserController::class, 'getUsers']);

    Route::get('get-totals',[DashboardController::class,'getTotals']);
});
