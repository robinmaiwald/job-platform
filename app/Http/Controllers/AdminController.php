<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Requests\Admin\CreateCompanyRequest;
use App\Http\Requests\Admin\CreateJobRequest;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Http\Requests\Admin\UpdateCompanyRequest;
use App\Http\Requests\Admin\UpdateJobRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct(
        private AdminRepository $admins
    ) {
        //
    }

    public function login(AdminLoginRequest $request)
    {
        $validated = $request->validated();

        $admin = $this->admins->get();

        if (
            !$admin ||
            $validated['name'] !== $admin->name ||
            !Hash::check($validated['password'], $admin->password)
        ) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = $admin->createToken('admin')->plainTextToken;

        return response()->json([
            'admin' => $admin,
            'token' => $token,
        ]);
    }

    // ADMIN PROFILE

    public function show(Admin $admin)
    {
        $this->authorize('view', $admin);

        return response()->json($admin);
    }

    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $this->authorize('update', $admin);

        $validated = $request->validated();

        $admin = $this->admins->update($admin, $validated);

        return response()->json($admin);
    }

    // USERS

    public function users()
    {
        $this->authorize('manageUsers', Admin::class);

        return response()->json(
            $this->admins->users()
        );
    }

    public function user(User $user)
    {
        $this->authorize('manageUsers', Admin::class);

        return response()->json(
            $this->admins->user($user)
        );
    }

    public function createUser(CreateUserRequest $request)
    {
        $this->authorize('manageUsers', Admin::class);

        $validated = $request->validated();

        $user = $this->admins->createUser($validated);

        return response()->json($user, 201);
    }

    public function updateUser(UpdateUserRequest $request, User $user)
    {
        $this->authorize('manageUsers', Admin::class);

        $validated = $request->validated();

        $user = $this->admins->updateUser($user, $validated);

        return response()->json($user);
    }

    public function deleteUser(User $user)
    {
        $this->authorize('manageUsers', Admin::class);

        $this->admins->deleteUser($user);

        return response()->noContent();
    }

    // JOBS

    public function jobs()
    {
        $this->authorize('manageJobs', Admin::class);

        return response()->json(
            $this->admins->jobs()
        );
    }

    public function job(Job $job)
    {
        $this->authorize('manageJobs', Admin::class);

        return response()->json(
            $this->admins->job($job)
        );
    }

    public function createJob(CreateJobRequest $request)
    {
        $this->authorize('manageJobs', Admin::class);

        $validated = $request->validated();

        $job = $this->admins->createJob($validated);

        return response()->json($job, 201);
    }

    public function updateJob(UpdateJobRequest $request, Job $job)
    {
        $this->authorize('manageJobs', Admin::class);

        $validated = $request->validated();

        $job = $this->admins->updateJob($job, $validated);

        return response()->json($job);
    }

    public function deleteJob(Job $job)
    {
        $this->authorize('manageJobs', Admin::class);

        $this->admins->deleteJob($job);

        return response()->noContent();
    }

    // COMPANIES

    public function companies()
    {
        $this->authorize('manageCompanies', Admin::class);

        return response()->json(
            $this->admins->companies()
        );
    }

    public function company(Company $company)
    {
        $this->authorize('manageCompanies', Admin::class);

        return response()->json(
            $this->admins->company($company)
        );
    }

    public function createCompany(CreateCompanyRequest $request)
    {
        $this->authorize('manageCompanies', Admin::class);
    
        $validated = $request->validated();
    
        $company = $this->admins->createCompany($validated);
    
        return response()->json($company, 201);
    }

    public function updateCompany(UpdateCompanyRequest $request, Company $company)
    {
        $this->authorize('manageCompanies', Admin::class);

        $validated = $request->validated();

        $ownerId = $validated['owner_id'] ?? null;

        unset($validated['owner_id']);

        $company = $this->admins->updateCompany(
            $company,
            $validated,
            $ownerId
        );

        return response()->json($company);
    }

    public function deleteCompany(Company $company)
    {
        $this->authorize('manageCompanies', Admin::class);

        $this->admins->deleteCompany($company);

        return response()->noContent();
    }
}