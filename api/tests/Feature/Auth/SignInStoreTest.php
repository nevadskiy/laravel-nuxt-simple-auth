<?php

namespace Tests\Feature\Auth;

use App\Services\Auth\ApiTokenGenerator;
use App\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;
use Tests\DatabaseTestCase;

class SignInStoreTest extends DatabaseTestCase
{
    // TODO: authenticated_user_cannot_sign_into_an_account

    /** @test */
    public function guests_can_sign_into_account_with_email_and_password(): void
    {
        $this->createUserWithCredentials('user@mail.com', 'secret123');

        $response = $this->signIn([
            'email' => 'user@mail.com',
            'password' => 'secret123',
        ]);

        $token = $response->json('api_token');

        $response->assertOk();
        $this->assertNotEmpty($token);
        $this->assertDatabaseHas(User::TABLE, [
            'email' => 'user@mail.com',
            'api_token' => $token
        ]);
    }

    /** @test */
    public function api_returns_correct_response_after_success_sign_up(): void
    {
        $this->createUserWithCredentials('user@mail.com', 'secret123');

        $this->mock(ApiTokenGenerator::class)
            ->shouldReceive('generate')
            ->andReturn('secret-api-token');

        $response = $this->signIn([
            'email' => 'user@mail.com',
            'password' => 'secret123',
        ]);

        $response->assertExactJson([
            'api_token' => 'secret-api-token'
        ]);
    }

    /** @test */
    public function invalid_values_do_not_pass_the_sign_in_validation_process(): void
    {
        foreach ($this->invalidFields() as $field => $values) {
            foreach ($values as $rule => $value) {
                $response = $this->signIn([$field => $value]);
                $this->assertEmpty(User::all(), "Request was processed with the invalid {$field} for the rule {$rule}");
                $response->assertJsonValidationErrors($field);
                $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }

    /**
     * Send a sign in request.
     *
     * @param array $overrides
     * @return TestResponse
     */
    private function signIn(array $overrides = []): TestResponse
    {
        return $this->postJson(route('api.auth.signin.store'), array_merge([
            'email' => 'user@mail.com',
            'password' => 'secret123',
        ], $overrides));
    }

    /**
     * Create user with provided credentials.
     *
     * @param string $email
     * @param string $password
     * @return User
     */
    private function createUserWithCredentials(string $email = 'user@mail.com', string $password = 'secret123'): User
    {
        return factory(User::class)->create([
            'email' => 'user@mail.com',
            'password' => resolve(Hasher::class)->make($password),
        ]);
    }

    /**
     * Invalid fields for testing validation errors.
     *
     * @return array
     */
    private function invalidFields(): array
    {
        return [
            'email' => [
                'required' => '',
                'string' => ['INVALID_STRING'],
                'email' => 'INVALID_EMAIL',
                'max:255' => str_repeat('A', 256 - 9) . '@mail.com',
            ],

            'password' => [
                'required' => '',
                'string' => ['INVALID_STRING'],
                'max:255' => str_repeat('A', 256 - 9) . '@mail.com',
            ],
        ];
    }
}
