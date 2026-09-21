<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Job;
use Illuminate\Database\Eloquent\Collection;

class JobRepository
{
    public function all(): Collection
    {
	return Job::all();
    }

    public function find(Job $job): Job
    {
	return $job;
    }

    public function create(array $data, User $user): Job
    {
	return $user->jobs()->create($data);
    }

    public function delete(Job $job): void
    {
	$job->delete();
    }

    public function update(Job $job, array $data): Job
    {
	$job->update($data);

	return $job;
    }
}
