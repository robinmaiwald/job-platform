<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPolicyTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private function createAdminToken(): string
    {
        $this->admin = Admin::create([
            'name' => 'test-admin',
            'password' => 'password',
        ]);

        return $this->admin->createToken(
            'test-admin-token',
            ['admin']
        )->plainTextToken;
    }

    public function test_admin_view_self(): void
    {
        $token = $this->createAdminToken();

        $response = $this->withToken($token)
            ->getJson("/api/admin/{$this->admin->id}");

        $response->assertStatus(200);
    }

    public function test_admin_update_self(): void
    {
        $token = $this->createAdminToken();

        $response = $this->withToken($token)
            ->putJson("/api/admin/{$this->admin->id}", [
                'name' => 'updated-admin',
            ]);

        $response->assertStatus(200);
    }

    public function test_admin_manage_users(): void
    {
        $token = $this->createAdminToken();

        $response = $this->withToken($token)
            ->getJson('/api/admin/users');

        $response->assertStatus(200);
    }

    public function test_admin_manage_jobs(): void
    {
        $token = $this->createAdminToken();

        $response = $this->withToken($token)
            ->getJson('/api/admin/jobs');

        $response->assertStatus(200);
    }

    public function test_admin_manage_companies(): void
    {
        $token = $this->createAdminToken();

        $response = $this->withToken($token)
            ->getJson('/api/admin/companies');

        $response->assertStatus(200);
    }
}