<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository
{
    // Repository Methods

    public function all(): Collection
    {
        return Company::with('users:id')->get();
    }

    public function find(Company $company): Company
    {
	    return $company;
    }

    public function create(array $data, User $user): Company
    {
	$company = Company::create($data);

	$company->owner()->associate($user);
	$company->save();

	$company->users()->syncWithoutDetaching([$user->id]);

	return $company;
    }

    public function update(Company $company, array $data): Company
    {
	    $company->update($data);

	    return $company;
    }

    public function delete(Company $company): void
    {
	    $company->delete();
    }
}
