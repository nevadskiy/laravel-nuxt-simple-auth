<?php

namespace Module\Auth\Tests\Feature;

use Module\Auth\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Response;
use Nevadskiy\Tokens\TokenManager;
use Module\Auth\Tests\DatabaseTestCase;
use Module\Auth\Tests\Factory\UserFactory;

class ForgottenPasswordUpdateTest extends DatabaseTestCase
{
    use AuthRequests;

    /** @test */
    public function guests_can_reset_password_with_reset_token(): void
    {
        $user = factory(User::class)->create([
            'email' => 'user@mail.com',
            'password' => 'FORGOTTEN_PASSWORD',
            'api_token' => 'OLD_API_TOKEN',
        ]);

        $hasher = $this->mock(Hasher::class);
        $hasher->shouldReceive('make')->with('NEW_PASSWORD')->andReturn('NEW_PASSWORD_HASH');
        $this->app->instance('hash', $hasher);

        $token = app(TokenManager::class)->generateFor($user, 'password.reset');

        $response = $this->resetPasswordRequest([
            'email' => 'user@mail.com',
            'password' => 'NEW_PASSWORD',
            'token' => $token->toString(),
        ]);

        $response->assertOk();
        $response->assertExactJson(['message' => __('auth::passwords.success')]);
        $this->assertEquals('NEW_PASSWORD_HASH', $user->fresh()->password);
        $this->assertTrue($token->fresh()->isUsed());
        $this->assertNull($user->fresh()->api_token);
    }

    /** @test */
    public function guests_cannot_reset_password_with_unknown_email(): void
    {
        $response = $this->resetPasswordRequest([
            'email' => 'another@mail.com',
            'password' => 'NEW_PASSWORD',
            'token' => 'SECRET_TOKEN',
        ]);

        $response->assertJsonValidationErrors(['email' => __('auth::passwords.not_found')]);
    }

    /** @test */
    public function guest_cannot_reset_password_for_another_user(): void
    {
        $user = app(UserFactory::class)->withCredentials('user@mail.com')->create();
        app(UserFactory::class)->withCredentials('another@mail.com')->create();

        $token = app(TokenManager::class)->generateFor($user, 'password.reset');

        $response = $this->resetPasswordRequest([
            'email' => 'another@mail.com',
            'password' => 'NEW_PASSWORD',
            'token' => $token->toString(),
        ]);

        $response->assertJsonValidationErrors('token');
        $this->assertFalse($token->fresh()->isUsed());
    }

    /** @test */
    public function guests_cannot_try_to_reset_password_with_too_many_attempts(): void
    {
        app(UserFactory::class)->withCredentials('user@mail.com')->create();

        $responses = [];

        for ($i = 0; $i < 5; $i++) {
            $responses[] = $this->resetPasswordRequest([
                'email' => 'user@mail.com',
                'password' => 'NEW_PASSWORD',
                'token' => 'INVALID_PASSWORD_TOKEN',
            ]);
        }

        foreach ($responses as $response) {
            $response->assertJsonValidationErrors(['token' => __('auth::passwords.invalid_token')]);
        }

        $response = $this->resetPasswordRequest([
            'email' => 'user@mail.com',
            'password' => 'NEW_PASSWORD',
            'token' => 'INVALID_PASSWORD_TOKEN',
        ]);

        $response->assertJsonValidationErrors(['email' => __('auth::passwords.throttle')]);
    }

    /** @test */
    public function guests_cannot_reset_password_with_invalid_token(): void
    {
        $user = app(UserFactory::class)->withCredentials('user@mail.com')->create();

        $token = app(TokenManager::class)->generateFor($user, 'password.reset');

        $response = $this->resetPasswordRequest([
            'email' => 'user@mail.com',
            'password' => 'NEW_PASSWORD',
            'token' => 'INVALID_PASSWORD_TOKEN',
        ]);

        $response->assertJsonValidationErrors(['token' => __('auth::passwords.invalid_token')]);
        $this->assertFalse($token->fresh()->isUsed());
    }

    /** @test */
    public function authenticated_users_cannot_reset_password_with_reset_token(): void
    {
        $user = app(UserFactory::class)->withCredentials('user@mail.com')->create();
        $this->be($user);

        $token = app(TokenManager::class)->generateFor($user, 'password.reset');

        $response = $this->resetPasswordRequest([
            'email' => 'user@mail.com',
            'password' => 'NEW_PASSWORD',
            'token' => 'RESET_PASSWORD_TOKEN',
        ]);

        $response->assertUnauthorized();
        $this->assertFalse($token->fresh()->isUsed());
    }

    /** @test */
    public function invalid_values_do_not_pass_the_reset_password_validation_process(): void
    {
        foreach ($this->invalidFields() as $field => $values) {
            foreach ($values as $rule => $value) {
                $response = $this->resetPasswordRequest([$field => $value]);
                $response->assertJsonValidationErrors($field);
                $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
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

            'token' => [
                'required' => '',
                'string' => ['INVALID_STRING'],
                'max:255' => str_repeat('A', 256),
            ],

            'password' => [
                'required' => '',
                'string' => ['INVALID_STRING'],
                'min:8' => str_repeat('A', 7),
                'max:255' => str_repeat('A', 256),
            ],
        ];
    }
}
