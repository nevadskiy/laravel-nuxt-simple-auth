<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\DatabaseTestCase;

class SignUpStoreTest extends DatabaseTestCase
{
    /** @test */
    public function guests_can_sign_up_with_email_and_password(): void
    {
        $response = $this->signUp([
            'email' => 'user@mail.com',
            'password' => 'secret123',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas(User::TABLE, [
            'email' => 'user@mail.com',
        ]);
    }

    /** @test */
    public function api_returns_correct_response_after_success_sign_up(): void
    {
        $response = $this->signUp([
            'email' => 'user@mail.com',
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'email',
            ]
        ]);

        $response->assertJsonFragment([
            'email' => 'user@mail.com',
        ]);
    }

    /**
     * Send a sign up request.
     *
     * @param array $overrides
     * @return TestResponse
     */
    private function signUp(array $overrides = []): TestResponse
    {
        return $this->postJson(route('api.auth.signup.store'), array_merge([
            'email' => 'guest@mail.com',
            'password' => 'secret123',
        ], $overrides));
    }
}
