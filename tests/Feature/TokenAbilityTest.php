<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokenAbilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_login_user(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/user');

        $response->assertStatus(200);
    }

    public function test_user_login_admin(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken(
            'test-user-token',
            ['user']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/admin/users');

        $response->assertStatus(403);
    }

    public function test_admin_login_admin(): void
    {
        $admin = Admin::create([
            'name' => 'test-admin',
            'password' => 'password',
        ]);

        $token = $admin->createToken(
            'test-admin-token',
            ['admin']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/admin/users');

        $response->assertStatus(200);
    }

    public function test_admin_login_user(): void
    {
        $admin = Admin::create([
            'name' => 'test-admin',
            'password' => 'password',
        ]);

        $token = $admin->createToken(
            'test-admin-token',
            ['admin']
        )->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/user');

        $response->assertStatus(403);
    }
}
