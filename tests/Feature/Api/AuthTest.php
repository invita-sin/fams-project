<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::where('email', 'admin@fams.test')->first();
        $this->token = $this->admin->createToken('api-token')->plainTextToken;
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@fams.test',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'token',
                'user',
            ]);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@fams.test',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_logout(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    }

    public function test_user_can_forgot_password(): void
    {
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'admin@fams.test',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_forgot_password_fails_for_invalid_email(): void
    {
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'nonexistent@fams.test',
        ]);

        $response->assertStatus(400);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $token = Password::createToken($this->admin);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'admin@fams.test',
            'token' => $token,
            'password' => 'new-password-ok',
            'password_confirmation' => 'new-password-ok',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertTrue(Hash::check('new-password-ok', $this->admin->fresh()->password));
    }

    public function test_user_can_change_password(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/auth/change-password', [
            'current_password' => 'password',
            'new_password' => 'new-password-ok',
            'new_password_confirmation' => 'new-password-ok',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertTrue(Hash::check('new-password-ok', $this->admin->fresh()->password));
    }

    public function test_change_password_fails_with_wrong_current_password(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/auth/change-password', [
            'current_password' => 'wrong-current',
            'new_password' => 'new-password-ok',
            'new_password_confirmation' => 'new-password-ok',
        ]);

        $response->assertStatus(422);
    }
}
