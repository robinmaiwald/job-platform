<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdminRepository
{
    // Admin profile

    public function update(Admin $admin, array $data): Admin
    {
        $admin->update($data);

        return $admin->refresh();
    }

    // Users

    public function users(): Collection
    {
        return User::with('companies')->get();
    }

    public function user(User $user): User
    {
        return $user->load('companies');
    }

    public function createUser(array $data): User
    {
        return User::create($data);
    }

    public function updateUser(User $user, array $data): User
    {
        $user->update($data);

        return $user->refresh();
    }

    public function deleteUser(User $user): void
    {
        $user->load('jobs.company');

        foreach ($user->jobs as $job) {
            $job->user_id = $job->company?->owner_id;
            $job->save();
        }

        $user->delete();
    }

    // Jobs

    public function jobs(): Collection
    {
        return Job::with('company', 'user')->get();
    }

    public function job(Job $job): Job
    {
        return $job->load('company', 'user');
    }

    public function createJob(array $data): Job
    {
        $job = Job::create($data);
    
        $job->company->users()->syncWithoutDetaching([
            $job->user_id,
        ]);
    
        return $job->load('company', 'user');
    }

    public function updateJob(Job $job, array $data): Job
    {
        $job->update($data);

        return $job->refresh()->load('company', 'user');
    }

    public function deleteJob(Job $job): void
    {
        $job->delete();
    }

    // Companies

    public function companies(): Collection
    {
        return Company::with('owner', 'users', 'jobs')->get();
    }

    public function company(Company $company): Company
    {
        return $company->load('owner', 'users', 'jobs');
    }

    public function createCompany(array $data): Company
    {
        $ownerId = $data['owner_id'] ?? null;

        unset($data['owner_id']);

        $company = Company::create($data);

        if ($ownerId) {
            $company->owner_id = $ownerId;
            $company->save();

            $company->users()->syncWithoutDetaching([$ownerId]);
        }

        return $company->refresh()->load('owner', 'users', 'jobs');
    }

    public function updateCompany(
        Company $company,
        array $data,
        ?int $ownerId = null
    ): Company {
        $company->update($data);

        $company->owner_id = $ownerId;
        $company->save();

        if ($ownerId) {
            $company->users()->syncWithoutDetaching([$ownerId]);
        }

        return $company->refresh()->load('owner', 'users', 'jobs');
    }

    public function deleteCompany(Company $company): void
    {
        $company->jobs()->delete();
        $company->delete();
    }
}