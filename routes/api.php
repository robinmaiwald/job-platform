<?php

use App\Http\Controllers\AdminController;
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

// Admin API

Route::post('/admin/login', [AdminController::class, 'login']);

// Admin -> Users

Route::get('/admin/users', [AdminController::class, 'users'])
    ->middleware('auth:sanctum');

Route::get('/admin/users/{user}', [AdminController::class, 'user'])
    ->middleware('auth:sanctum');

Route::post('/admin/users', [AdminController::class, 'createUser'])
    ->middleware('auth:sanctum');

Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])
    ->middleware('auth:sanctum');

Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])
    ->middleware('auth:sanctum');

// Admin -> Jobs

Route::get('/admin/jobs', [AdminController::class, 'jobs'])
    ->middleware('auth:sanctum');

Route::get('/admin/jobs/{job}', [AdminController::class, 'job'])
    ->middleware('auth:sanctum');

Route::post('/admin/jobs', [AdminController::class, 'createJob'])
    ->middleware('auth:sanctum');

Route::put('/admin/jobs/{job}', [AdminController::class, 'updateJob'])
    ->middleware('auth:sanctum');

Route::delete('/admin/jobs/{job}', [AdminController::class, 'deleteJob'])
    ->middleware('auth:sanctum');

// Admin -> Companies

Route::get('/admin/companies', [AdminController::class, 'companies'])
    ->middleware('auth:sanctum');

Route::get('/admin/companies/{company}', [AdminController::class, 'company'])
    ->middleware('auth:sanctum');

Route::post('/admin/companies', [AdminController::class, 'createCompany'])
    ->middleware('auth:sanctum');

Route::put('/admin/companies/{company}', [AdminController::class, 'updateCompany'])
    ->middleware('auth:sanctum');

Route::delete('/admin/companies/{company}', [AdminController::class, 'deleteCompany'])
    ->middleware('auth:sanctum');

// Admin profile — keep this last

Route::apiResource('admin', AdminController::class)
    ->only(['show', 'update'])
    ->middleware('auth:sanctum');
