<?php

namespace App\Policies;

use App\Models\Admin;

class AdminPolicy
{
    public function view(Admin $admin, Admin $target): bool
    {
        return $admin->id === $target->id;
    }

    public function update(Admin $admin, Admin $target): bool
    {
        return $admin->id === $target->id;
    }

    public function manageUsers(mixed $admin): bool
    {
        return $admin instanceof Admin;
    }

    public function manageJobs(mixed $admin): bool
    {
        return $admin instanceof Admin;
    }

    public function manageCompanies(mixed $admin): bool
    {
        return $admin instanceof Admin;
    }

}