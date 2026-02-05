<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the login page is displayed.
     */
    public function test_login_page_is_displayed(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('login'); // Assuming 'login' text or name is present
    }

    /**
     * Test successful login.
     */
    public function test_user_can_login_with_correct_credentials(): void
    {
        // Create a user
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('password123'),
        ]);

        // Attempt login
        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/'); // Assuming redirect to dashboard/home
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test failed login.
     */
    public function test_user_cannot_login_with_incorrect_credentials(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}
