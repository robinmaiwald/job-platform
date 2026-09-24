<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_view_all(): void
    {
        $response = $this->getJson('/api/jobs');

        $response->assertStatus(200);
    }

    public function test_user_view_all(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/jobs');

        $response->assertStatus(200);
    }

    public function test_guest_view(): void
    {
        $job = Job::factory()->create();

        $response = $this->getJson("/api/jobs/{$job->id}");

        $response->assertStatus(200);
    }

    public function test_user_view(): void
    {
        $user = User::factory()->create();

        $job = Job::factory()->create([
            'user_id' => $user->id,
        ]);

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->getJson("/api/jobs/{$job->id}");

        $response->assertStatus(200);
    }

    public function test_guest_create(): void
    {
        $response = $this->postJson('/api/jobs', [
            'company_id' => 1,
            'title' => 'Test Job',
            'description' => 'Test description',
            'location' => 'Hannover',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_create(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $job = Job::factory()->make([
            'user_id' => $user->id,
        ]);

        $response = $this->withToken($token)
            ->postJson('/api/jobs', [
                'company_id' => $job->company_id,
                'title' => $job->title,
                'description' => $job->description,
                'location' => $job->location,
            ]);

        $response->assertStatus(201);
    }

    public function test_guest_update(): void
    {
        $job = Job::factory()->create();

        $response = $this->putJson("/api/jobs/{$job->id}", [
            'title' => 'Updated Job',
        ]);

        $response->assertStatus(401);
    }

    public function test_owner_update(): void
    {
        $user = User::factory()->create();

        $job = Job::factory()->create([
            'user_id' => $user->id,
        ]);

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->putJson("/api/jobs/{$job->id}", [
                'title' => 'Updated Job',
                'company_id' => $job->company_id,
            ]);

        $response->assertStatus(200);
    }

    public function test_other_update(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $job = Job::factory()->create([
            'user_id' => $owner->id,
        ]);

        $token = $otherUser->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->putJson("/api/jobs/{$job->id}", [
                'title' => 'Updated Job',
                'company_id' => $job->company_id,
            ]);

        $response->assertStatus(403);
    }

    public function test_guest_delete(): void
    {
        $job = Job::factory()->create();

        $response = $this->deleteJson("/api/jobs/{$job->id}");

        $response->assertStatus(401);
    }

    public function test_owner_delete(): void
    {
        $user = User::factory()->create();

        $job = Job::factory()->create([
            'user_id' => $user->id,
        ]);

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->deleteJson("/api/jobs/{$job->id}");

        $response->assertStatus(204);
    }

    public function test_other_delete(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $job = Job::factory()->create([
            'user_id' => $owner->id,
        ]);

        $token = $otherUser->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->deleteJson("/api/jobs/{$job->id}");

        $response->assertStatus(403);
    }
}