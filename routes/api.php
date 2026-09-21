<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('jobs', JobController::class)
    ->only(['index', 'show']);

Route::apiResource('jobs', JobController::class)
    ->except(['index', 'show'])
    ->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
