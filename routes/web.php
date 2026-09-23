<?php

use Illuminate\Support\Facades\Route;

// Main

Route::inertia('/', 'Welcome')->name('home');

Route::inertia('/entry', 'Entry')->name('entry');

Route::inertia('/login', 'Login')->name('login');

Route::inertia('/register', 'Register')->name('register');

Route::inertia('/guest', 'Guest')->name('guest');

// Public

Route::inertia('/jobs', 'public/Jobs')->name('jobs');

Route::inertia('/jobs/{job}', 'public/JobDetails')->name('jobs.show');

Route::inertia('/jobs/{job}/edit', 'auth/EditJob')->name('jobs.edit');

Route::inertia('/companies', 'public/Companies')->name('companies');

Route::inertia('/companies/{company}/edit', 'auth/EditCompany')->name('companies.edit');

Route::inertia('/companies/{company}/jobs', 'public/CompanyJobs')->name('companies.jobs');

Route::inertia('/companies/{company}', 'public/CompanyDetails')->name('companies.show');

// Authenticated

Route::inertia('/profile', 'auth/Profile')->name('profile');

Route::inertia('/network', 'auth/Network')->name('network');

Route::inertia('/settings', 'auth/Settings')->name('settings');

Route::inertia('/create/job', 'auth/CreateJob')->name('create.job');

Route::inertia('/create/company', 'auth/CreateCompany')->name('create.company');

// Admin

Route::inertia('/admin/login', 'admin/AdminLogin')->name('admin.login');

Route::inertia('/admin', 'admin/AdminDashboard')->name('admin.dashboard');

Route::inertia('/admin/users', 'admin/AdminUsers')->name('admin.users');

Route::inertia('/admin/jobs', 'admin/AdminJobs')->name('admin.jobs');

Route::inertia('/admin/companies', 'admin/AdminCompanies')->name('admin.companies');