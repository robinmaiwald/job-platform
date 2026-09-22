<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
	    'company_id' => Company::factory(),
	    'title' => fake()->jobTitle(),
	    'description' => fake()->paragraph(),
	    'location' => fake()->city(),
        ];
    }

    public function configure(): static
    {
	return $this->afterMaking(function (Job $job) {
	    $job->user_id = User::factory()->create()->id;
	});
    }
}
