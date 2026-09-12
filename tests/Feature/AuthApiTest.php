<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Sanctum's statefulApi() middleware only attaches the session
     * middleware group when the request "looks like" it came from the SPA
     * (EnsureFrontendRequestsAreStateful::fromFrontend() checks the
     * Referer/Origin header against sanctum.stateful) — endpoints that
     * touch request()->session() 500 without it. phpunit.xml pins
     * SANCTUM_STATEFUL_DOMAINS to "localhost" for tests so this doesn't
     * depend on whatever port the local .env points the real dev server at.
     */
    private function postAsFrontend(string $uri, array $data = []): TestResponse
    {
        return $this->withHeader('Referer', 'http://localhost')->postJson($uri, $data);
    }

    public function test_a_new_user_can_register_and_is_logged_in(): void
    {
        $response = $this->postAsFrontend('/api/register', [
            'name' => 'Admin User',
            'email' => 'admin@shilporekha.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.email', 'admin@shilporekha.test')
            // Regression: role has a DB-level default that User::create()'s
            // in-memory model doesn't pick up without a refresh() — this
            // used to report role: null instead of "admin".
            ->assertJsonPath('data.role', 'admin');

        $this->assertDatabaseHas('users', ['email' => 'admin@shilporekha.test']);
        $this->assertAuthenticated();
    }

    public function test_registering_requires_matching_password_confirmation(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Admin User',
            'email' => 'admin@shilporekha.test',
            'password' => 'password123',
            'password_confirmation' => 'something-else',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', false)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registering_with_an_email_already_in_use_fails_validation(): void
    {
        User::factory()->create(['email' => 'admin@shilporekha.test']);

        $response = $this->postJson('/api/register', [
            'name' => 'Another Admin',
            'email' => 'admin@shilporekha.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_a_user_can_log_in_with_correct_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@shilporekha.test',
            'password' => 'password123',
        ]);

        $response = $this->postAsFrontend('/api/login', [
            'email' => 'admin@shilporekha.test',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.email', 'admin@shilporekha.test');

        $this->assertAuthenticated();
    }

    public function test_logging_in_with_incorrect_credentials_fails(): void
    {
        User::factory()->create(['email' => 'admin@shilporekha.test', 'password' => 'password123']);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@shilporekha.test',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
        $this->assertGuest();
    }

    public function test_an_authenticated_user_can_log_out(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->postAsFrontend('/api/logout');

        $response->assertOk()->assertJsonPath('status', true);

        // Guard explicitly: the sanctum guard is a RequestGuard, which
        // caches its resolved user for the app container's lifetime —
        // across separate real HTTP requests that's a non-issue (each is a
        // fresh process), but it means a *second* simulated request in this
        // same test would read that stale cache rather than re-check the
        // (correctly cleared) session. Verified for real against a live
        // `php artisan serve` instance: login → logout → GET /api/user
        // correctly returns 401 afterward.
        $this->assertGuest('web');
    }
}
