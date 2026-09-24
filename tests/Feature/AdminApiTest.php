<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminToken(): string
    {
        $admin = Admin::create([
            'name' => 'test-admin',
            'password' => 'password',
        ]);

        return $admin->createToken(
            'test-admin-token',
            ['admin']
        )->plainTextToken;
    }

    private function adminCanGet(string $endpoint): void
    {
        $token = $this->createAdminToken();

        $response = $this->withToken($token)
            ->getJson($endpoint);

        $response->assertStatus(200);
    }

    private function adminCanCreate(string $endpoint, array $data): void
    {
        $token = $this->createAdminToken();

        $response = $this->withToken($token)
            ->postJson($endpoint, $data);

        $response->assertStatus(201);
    }

    private function adminCanUpdate(string $endpoint, array $data): void
    {
        $token = $this->createAdminToken();

        $response = $this->withToken($token)
            ->putJson($endpoint, $data);

        $response->assertStatus(200);
    }

    private function adminCanDelete(string $endpoint): void
    {
        $token = $this->createAdminToken();

        $response = $this->withToken($token)
            ->deleteJson($endpoint);

        $response->assertStatus(204);
    }

    // Users

    public function test_admin_list_users(): void
    {
        $this->adminCanGet('/api/admin/users');
    }

    public function test_admin_view_user(): void
    {
        $user = User::factory()->create();

        $this->adminCanGet("/api/admin/users/{$user->id}");
    }

    public function test_admin_create_user(): void
    {
        $this->adminCanCreate('/api/admin/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
    }

    public function test_admin_update_user(): void
    {
        $user = User::factory()->create();

        $this->adminCanUpdate("/api/admin/users/{$user->id}", [
            'name' => 'Updated User',
        ]);
    }

    public function test_admin_delete_user(): void
    {
        $user = User::factory()->create();

        $this->adminCanDelete("/api/admin/users/{$user->id}");
    }

    // Jobs

    public function test_admin_list_jobs(): void
    {
        $this->adminCanGet('/api/admin/jobs');
    }

    public function test_admin_view_job(): void
    {
        $job = Job::factory()->create();

        $this->adminCanGet("/api/admin/jobs/{$job->id}");
    }

    public function test_admin_create_job(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();

        $this->adminCanCreate('/api/admin/jobs', [
            'company_id' => $company->id,
            'user_id' => $user->id,
            'title' => 'Test Job',
            'description' => 'Test description',
            'location' => 'Hannover',
        ]);
    }

    public function test_admin_update_job(): void
    {
        $job = Job::factory()->create();

        $this->adminCanUpdate("/api/admin/jobs/{$job->id}", [
            'company_id' => $job->company_id,
            'title' => 'Updated Job',
        ]);
    }

    public function test_admin_delete_job(): void
    {
        $job = Job::factory()->create();

        $this->adminCanDelete("/api/admin/jobs/{$job->id}");
    }

    // Companies

    public function test_admin_list_companies(): void
    {
        $this->adminCanGet('/api/admin/companies');
    }

    public function test_admin_view_company(): void
    {
        $company = Company::factory()->create();

        $this->adminCanGet("/api/admin/companies/{$company->id}");
    }

    public function test_admin_create_company(): void
    {
        $this->adminCanCreate('/api/admin/companies', [
            'name' => 'Test Company',
            'description' => 'Test description',
            'website' => 'https://example.com',
        ]);
    }

    public function test_admin_update_company(): void
    {
        $company = Company::factory()->create();

        $this->adminCanUpdate("/api/admin/companies/{$company->id}", [
            'name' => 'Updated Company',
        ]);
    }

    public function test_admin_delete_company(): void
    {
        $company = Company::factory()->create();

        $this->adminCanDelete("/api/admin/companies/{$company->id}");
    }
}