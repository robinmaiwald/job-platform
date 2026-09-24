<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_view_all(): void
    {
        $user = User::factory()->create();
    
        $this->assertFalse(
            $user->can('viewAny', User::class)
        );
    }

    public function test_user_view_user(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->getJson("/api/users/{$user->id}");

        $response->assertStatus(200);
    }

    public function test_user_view_other(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->getJson("/api/users/{$otherUser->id}");

        $response->assertStatus(403);
    }

    public function test_user_create_user(): void
    {
        $user = User::factory()->create();
    
        $this->assertTrue(
            $user->can('create', User::class)
        );
    }

    public function test_user_update_user(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->putJson("/api/users/{$user->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(200);
    }

    public function test_user_update_other(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->putJson("/api/users/{$otherUser->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(403);
    }

    public function test_user_delete_user(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(204);
    }

    public function test_user_delete_other(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->deleteJson("/api/users/{$otherUser->id}");

        $response->assertStatus(403);
    }
}