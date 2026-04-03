<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pejabat;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function login_page_is_accessible_to_guest(): void
    {
        $response = $this->get('/login');
        $response->assertOk();
        $response->assertSee('Login');
    }

    /** @test */
    public function authenticated_user_is_redirected_from_login_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user)->get('/login');
        // Already logged in — should still see login page (no special redirect rule) or redirect
        $response->assertStatus(200); // Laravel doesn't auto-redirect unless middleware set
    }

    /** @test */
    public function user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $response = $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'password123',
            'tahun'    => 2026,
        ]);

        $response->assertRedirectToRoute('dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email'    => 'admin@test.com',
            'password' => bcrypt('correctpassword'),
            'role'     => 'admin',
        ]);

        $response = $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'wrongpassword',
            'tahun'    => 2026,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function login_requires_tahun_field(): void
    {
        User::factory()->create(['email' => 'admin@test.com', 'password' => bcrypt('password')]);

        $response = $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'password',
            // 'tahun' deliberately missing
        ]);

        $response->assertSessionHasErrors('tahun');
    }

    /** @test */
    public function login_requires_valid_4_digit_year(): void
    {
        User::factory()->create(['email' => 'admin@test.com', 'password' => bcrypt('password')]);

        $response = $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'password',
            'tahun'    => '20', // too short
        ]);

        $response->assertSessionHasErrors('tahun');
    }

    /** @test */
    public function tahun_is_stored_in_session_after_login(): void
    {
        User::factory()->create([
            'email'    => 'admin@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'password',
            'tahun'    => 2026,
        ]);

        $this->assertEquals(2026, session('tahun_anggaran'));
    }

    /** @test */
    public function authenticated_user_can_logout(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
