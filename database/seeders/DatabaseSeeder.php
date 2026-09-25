<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
		$this->call(AdminSeeder::class);

		// Test data
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $testCompany = Company::create([
            'name' => 'Test Company',
            'description' => 'Test Company',
            'website' => 'https://example.com',
            'owner_id' => $testUser->id,
        ]);

        $testCompany->users()->syncWithoutDetaching([$testUser->id]);

        Job::create([
            'company_id' => $testCompany->id,
            'user_id' => $testUser->id,
            'title' => 'Test Job',
            'description' => 'Test Job Description',
            'location' => 'Welt',
        ]);

        // Random data
        $users = User::factory(5)->create();
        $companies = Company::factory(5)->create();

        foreach ($users as $index => $user) {
            $company = $companies[$index];

            $company->owner_id = $user->id;
            $company->save();

            $company->users()->syncWithoutDetaching([$user->id]);

            Job::factory()->create([
                'company_id' => $company->id,
                'user_id' => $user->id,
            ]);
        }
    }
}