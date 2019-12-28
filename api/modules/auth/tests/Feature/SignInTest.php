<?php

namespace Module\Auth\Tests\Feature;

use Module\Auth\Models\User;
use Module\Auth\Services\TokenGenerator\ApiTokenGenerator;
use Illuminate\Http\Response;
use Module\Auth\Tests\DatabaseTestCase;
use Module\Auth\Tests\Factory\UserFactory;

class SignInTest extends DatabaseTestCase
{
    use AuthRequests;

    /** @test */
    public function guests_can_sign_into_account_with_email_and_password(): void
    {
        app(UserFactory::class)->withCredentials('user@mail.com', 'secret123')->create();

        $response = $this->signInRequest([
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
    public function guests_cannot_sign_into_their_account_with_invalid_email(): void
    {
        app(UserFactory::class)->withCredentials('user@mail.com', 'secret123')->create();

        $response = $this->signInRequest([
            'email' => 'user@mail.com',
            'password' => 'INVALID_PASSWORD',
        ]);

        $response->assertJsonValidationErrors(['email' => __('auth::sign_in.failed')]);
    }

    /** @test */
    public function guests_cannot_sign_into_their_account_with_invalid_password(): void
    {
        app(UserFactory::class)->withCredentials('user@mail.com', 'secret123')->create();

        $response = $this->signInRequest([
            'email' => 'admin@mail.com',
            'password' => 'secret123',
        ]);

        $response->assertJsonValidationErrors('email');
    }

    /** @test */
    public function authenticated_users_cannot_sign_in_again(): void
    {
        $user = app(UserFactory::class)->withCredentials('user@mail.com', 'secret123')->create();

        $this->be($user);

        $response = $this->signInRequest([
            'email' => 'user@mail.com',
            'password' => 'secret123',
        ]);

        $response->assertUnauthorized();
    }

    /** @test */
    public function api_returns_correct_response_after_success_sign_in(): void
    {
        app(UserFactory::class)->withCredentials('user@mail.com', 'secret123')->create();

        $this->mock(ApiTokenGenerator::class)
            ->shouldReceive('generate')
            ->andReturn('secret-api-token');

        $response = $this->signInRequest([
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
                $response = $this->signInRequest([$field => $value]);
                $this->assertEmpty(User::all(), "Request was processed with the invalid {$field} for the rule {$rule}");
                $response->assertJsonValidationErrors($field);
                $response->assertJsonCount(1, "errors.{$field}");
                $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }

    /** @test */
    public function failed_attempts_in_a_row_lock_the_sign_in_action_to_prevent_brute_force(): void
    {
        $this->freezeTime();

        config(['auth.sign_in.rate_limiter.max_attempts' => 3]);
        config(['auth.sign_in.rate_limiter.seconds' => 55]);

        $failedResponse1 = $this->signInRequest(['email' => 'admin@mail.com']);
        $failedResponse2 = $this->signInRequest(['email' => 'admin@mail.com']);
        $failedResponse3 = $this->signInRequest(['email' => 'admin@mail.com']);
        $lockedResponse = $this->signInRequest(['email' => 'admin@mail.com']);

        $failedResponse1->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $failedResponse2->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $failedResponse3->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $lockedResponse->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);

        $lockedResponse->assertJsonValidationErrors(['email' => __('auth::sign_in.throttle', ['seconds' => 55])]);
    }

    /** @test */
    public function locked_sign_in_can_be_unlocked_after_timeout(): void
    {
        $this->freezeTime();

        config(['auth.sign_in.rate_limiter.max_attempts' => 1]);
        config(['auth.sign_in.rate_limiter.seconds' => 60]);

        $failedResponse = $this->signInRequest(['email' => 'admin@mail.com']);
        $failedResponse->assertJsonValidationErrors(['email' => __('auth::sign_in.failed')]);

        $lockedResponse = $this->signInRequest(['email' => 'admin@mail.com']);
        $lockedResponse->assertJsonValidationErrors(['email' => __('auth::sign_in.throttle', ['seconds' => 60])]);

        $this->freezeTime(now()->addSeconds(61));

        $failedResponse = $this->signInRequest(['email' => 'admin@mail.com']);
        $failedResponse->assertJsonValidationErrors(['email' => __('auth::sign_in.failed')]);
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
