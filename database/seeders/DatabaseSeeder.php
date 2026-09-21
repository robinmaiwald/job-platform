<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
	    'name'  => 'Test User',
	    'email'=> 'test@example.com',
	]);

	$company = Company::create([
	    'name' => 'Test Company',
	    'description' => 'Test Company',
	    'website' => 'https://example.com',
	]);

	$job = new Job([
	   'company_id' => $company->id,
	   'title' => 'Test Job',
	   'description' => 'Test Job Description',
	   'location' => 'Welt',
	]);

	$job->user_id = $user->id;
	$job->save();
    }
}
