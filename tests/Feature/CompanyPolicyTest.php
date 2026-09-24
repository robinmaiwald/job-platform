<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_view_all(): void
    {
        $response = $this->getJson('/api/companies');

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
            ->getJson('/api/companies');

        $response->assertStatus(200);
    }

    public function test_guest_view(): void
    {
        $company = Company::factory()->create();

        $response = $this->getJson("/api/companies/{$company->id}");

        $response->assertStatus(200);
    }

    public function test_user_view(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->getJson("/api/companies/{$company->id}");

        $response->assertStatus(200);
    }

    public function test_guest_create(): void
    {
        $response = $this->postJson('/api/companies', [
            'name' => 'Test Company',
            'description' => 'Test description',
            'website' => 'https://example.com',
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

        $response = $this->withToken($token)
            ->postJson('/api/companies', [
                'name' => 'Test Company',
                'description' => 'Test description',
                'website' => 'https://example.com',
            ]);

        $response->assertStatus(201);
    }

    public function test_guest_update(): void
    {
        $company = Company::factory()->create();

        $response = $this->putJson("/api/companies/{$company->id}", [
            'name' => 'Updated Company',
        ]);

        $response->assertStatus(401);
    }

    public function test_member_update(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $company->users()->attach($user->id);

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->putJson("/api/companies/{$company->id}", [
                'name' => 'Updated Company',
            ]);

        $response->assertStatus(200);
    }

    public function test_other_update(): void
    {
        $member = User::factory()->create();
        $otherUser = User::factory()->create();
        $company = Company::factory()->create();

        $company->users()->attach($member->id);

        $token = $otherUser->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->putJson("/api/companies/{$company->id}", [
                'name' => 'Updated Company',
            ]);

        $response->assertStatus(403);
    }

    public function test_guest_delete(): void
    {
        $company = Company::factory()->create();

        $response = $this->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(401);
    }

    public function test_owner_delete(): void
    {
        $user = User::factory()->create();

        $company = Company::factory()->create([
            'owner_id' => $user->id,
        ]);

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(204);
    }

    public function test_other_delete(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $company = Company::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $token = $otherUser->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(403);
    }
}