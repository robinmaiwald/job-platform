<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_email_and_password(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);
    }

    public function test_user_cannot_login_with_correct_email_and_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function test_user_cannot_login_with_wrong_email_and_correct_password(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'password',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function test_user_cannot_login_with_wrong_email_and_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function test_admin_can_login_with_correct_name_and_password(): void
    {
        $admin = Admin::create([
            'name' => 'test-admin',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/admin/login', [
            'name' => $admin->name,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'admin',
                'token',
            ]);
    }

    public function test_admin_cannot_login_with_correct_name_and_wrong_password(): void
    {
        $admin = Admin::create([
            'name' => 'test-admin',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/admin/login', [
            'name' => $admin->name,
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function test_admin_cannot_login_with_wrong_name_and_correct_password(): void
    {
        Admin::create([
            'name' => 'test-admin',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/admin/login', [
            'name' => 'wrong-admin',
            'password' => 'password',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function test_admin_cannot_login_with_wrong_name_and_wrong_password(): void
    {
        Admin::create([
            'name' => 'test-admin',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/admin/login', [
            'name' => 'wrong-admin',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }
}
