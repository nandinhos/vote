<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    // NOTA: Testes de registro foram desabilitados pois o registro agora é gerenciado por administrador
    // As rotas de registro estão comentadas em routes/auth.php

    /*
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->withoutMiddleware()
            ->post('/register', [
                'name' => 'Test User',
                'saram' => '1234567',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
    */

    public function test_registration_routes_are_disabled(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(404);

        $response = $this->post('/register', []);
        $response->assertStatus(404);
    }
}
