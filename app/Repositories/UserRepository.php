<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function all(): Collection
    {
        return User::with('companies')->get();
    }

    public function find(User $user): User
    {
        return $user->load('companies');
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->refresh();
    }

    public function delete(User $user): void
    {
        $user->load('jobs.company');

        foreach ($user->jobs as $job) {
            $job->user_id = $job->company?->owner_id;
            $job->save();
        }

        $user->delete();
    }
}