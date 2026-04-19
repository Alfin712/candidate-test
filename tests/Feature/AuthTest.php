<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_visiting_dashboard_is_redirected_to_login(): void
    {
        // Arrange: no authenticated user

        // Act
        $response = $this->get('/dashboard');

        // Assert
        $response->assertRedirect('/login');
    }

    public function test_login_with_valid_credentials_redirects_to_dashboard(): void
    {
        // Arrange
        $user = User::factory()->create(['role' => User::ROLE_STAFF]);

        // Act
        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        // Assert
        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_login_with_invalid_credentials_stays_on_login_with_error(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        // Assert
        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    public function test_register_route_is_not_defined_returns_404(): void
    {
        // Arrange: no authenticated user

        // Act
        $response = $this->get('/register');

        // Assert
        $response->assertStatus(404);
    }
}
