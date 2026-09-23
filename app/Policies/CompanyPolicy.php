<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{

    public function viewAny(?User $user): bool
    {
        return true;
    }


    public function view(?User $user, Company $company): bool
    {
        return true;
    }


    public function create(User $user): bool
    {
        return true;
    }


    public function update(User $user, Company $company): bool
    {
        return $company->users()
	    ->where('users.id', $user->id)
	    ->exists();
    }


    public function delete(User $user, Company $company): bool
    {
        return $company->owner_id === $user->id;
    }


    public function restore(User $user, Company $company): bool
    {
        return false;
    }


    public function forceDelete(User $user, Company $company): bool
    {
        return false;
    }
}
