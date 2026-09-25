<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;


class JobFactory extends Factory
{

    public function definition(): array
    {
        return [
	    'company_id' => Company::factory(),
	    'title' => fake()->jobTitle(),
	    'description' => fake()->paragraph(),
	    'location' => fake()->city(),
        ];
    }
}
