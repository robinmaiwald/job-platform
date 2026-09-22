<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Job API

Route::apiResource('jobs', JobController::class)
    ->only(['index', 'show']);

Route::apiResource('jobs', JobController::class)
    ->except(['index', 'show'])
    ->middleware('auth:sanctum');

// Company API

Route::apiResource('companies', CompanyController::class)
    ->only(['index', 'show']);

Route::apiResource('companies', CompanyController::class)
    ->except(['index', 'show'])
    ->middleware('auth:sanctum');

// User API

Route::post('/users', [UserController::class, 'store']);

Route::apiResource('users', UserController::class)
    ->only(['show', 'update', 'destroy'])
    ->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

