<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::inertia('/entry', 'Entry')->name('entry');

Route::inertia('/login', 'Login')->name('login');

Route::inertia('/dashboard', 'Dashboard')->name('dashboard');

Route::inertia('/guest', 'Guest')->name('guest');

//Guest Path

Route::inertia('/jobs', 'Jobs')->name('jobs');

Route::inertia('/jobs/{job}', 'JobDetails')->name('jobs.show');

Route::inertia('/companies', 'Companies')->name('companies');

Route::inertia('/companies/{company}/jobs', 'CompanyJobs')->name('companies.jobs');

Route::inertia('/companies/{company}', 'CompanyDetails')->name('companies.show');

//Login Path


