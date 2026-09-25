<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\CreateCompanyRequest;
use App\Http\Requests\Admin\CreateJobRequest;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Http\Requests\Admin\UpdateCompanyRequest;
use App\Http\Requests\Admin\UpdateJobRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\Admin\AdminResource;
use App\Http\Resources\Admin\UserResource as AdminUserResource;
use App\Http\Resources\Admin\JobResource as AdminJobResource;
use App\Http\Resources\Admin\CompanyResource as AdminCompanyResource;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use App\Repositories\AdminRepository;


class AdminController extends Controller
{
    public function __construct(
        private AdminRepository $admins
    ) {
        //
    }

    // ADMIN PROFILE

    public function show(Admin $admin)
    {
        $this->authorize('view', $admin);

        return new AdminResource($admin);
    }

    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $this->authorize('update', $admin);

        $validated = $request->validated();

        $admin = $this->admins->update($admin, $validated);

        return new AdminResource($admin);
    }

    // USERS

    public function users()
    {
        $this->authorize('manageUsers', Admin::class);

        return AdminUserResource::collection($this->admins->users());
    }

    public function user(User $user)
    {
        $this->authorize('manageUsers', Admin::class);

        return new AdminUserResource($this->admins->user($user));
    }

    public function createUser(CreateUserRequest $request)
    {
        $this->authorize('manageUsers', Admin::class);

        $validated = $request->validated();

        $user = $this->admins->createUser($validated);

        return (new AdminUserResource($user))->response()->setStatusCode(201);
    }

    public function updateUser(UpdateUserRequest $request, User $user)
    {
        $this->authorize('manageUsers', Admin::class);

        $validated = $request->validated();

        $user = $this->admins->updateUser($user, $validated);

        return new AdminUserResource($user);
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

        return AdminJobResource::collection($this->admins->jobs());
    }

    public function job(Job $job)
    {
        $this->authorize('manageJobs', Admin::class);

        return new AdminJobResource($this->admins->job($job));
    }

    public function createJob(CreateJobRequest $request)
    {
        $this->authorize('manageJobs', Admin::class);

        $validated = $request->validated();

        $job = $this->admins->createJob($validated);

        return (new AdminJobResource($job))->response()->setStatusCode(201);
    }

    public function updateJob(UpdateJobRequest $request, Job $job)
    {
        $this->authorize('manageJobs', Admin::class);

        $validated = $request->validated();

        $job = $this->admins->updateJob($job, $validated);

        return new AdminJobResource($job);
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

        return AdminCompanyResource::collection($this->admins->companies());
    }

    public function company(Company $company)
    {
        $this->authorize('manageCompanies', Admin::class);

        return new AdminCompanyResource($this->admins->company($company));
    }

    public function createCompany(CreateCompanyRequest $request)
    {
        $this->authorize('manageCompanies', Admin::class);

        $validated = $request->validated();

        $company = $this->admins->createCompany($validated);

        return (new AdminCompanyResource($company))->response()->setStatusCode(201);
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

        return new AdminCompanyResource($company);
    }

    public function deleteCompany(Company $company)
    {
        $this->authorize('manageCompanies', Admin::class);

        $this->admins->deleteCompany($company);

        return response()->noContent();
    }
}