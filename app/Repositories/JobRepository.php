<?php

namespace App\Repositories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Collection;

class JobRepository
{
    public function all(): Collection
    {
	return Job::all();
    }

    public function find(Job$job): Job
    {
	return $job;
    }
}
