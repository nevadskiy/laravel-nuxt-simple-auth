<?php

namespace Tests\Feature\Auth;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\DatabaseTestCase;
use Tests\Factory\UserFactory;

class ForgottenPasswordUpdateTest extends DatabaseTestCase
{
    use AuthRequests;

    /** @test */
    public function guests_can_reset_password_with_reset_token(): void
    {
        app(UserFactory::class)->withCredentials('user@mail.com')->create();

        $hasher = $this->mockHashCheck('RESET_PASSWORD_TOKEN', 'RESET_PASSWORD_HASH', true);
        $hasher->shouldReceive('make')->with('NEW_PASSWORD')->andReturn('TEST');

        DB::table('password_resets')->insert([
            'email' => 'user@mail.com',
            'token' => 'RESET_PASSWORD_HASH',
            'created_at' => now(),
        ]);

        $response = $this->resetPassword([
            'email' => 'user@mail.com',
            'password' => 'NEW_PASSWORD',
            'token' => 'RESET_PASSWORD_TOKEN',
        ]);

        $response->assertOk();
        $response->assertExactJson(['message' => __('passwords.reset')]);
        $this->assertEmpty(DB::table('password_resets')->get());
    }

    /** @test */
    public function guests_cannot_reset_password_with_another_email(): void
    {
        app(UserFactory::class)->withCredentials('user@mail.com')->create();
        app(UserFactory::class)->withCredentials('another@mail.com')->create();

        DB::table('password_resets')->insert([
            'email' => 'user@mail.com',
            'token' => 'RESET_PASSWORD_HASH',
            'created_at' => now(),
        ]);

        $response = $this->resetPassword([
            'email' => 'another@mail.com',
            'password' => 'NEW_PASSWORD',
            'token' => 'RESET_PASSWORD_TOKEN',
        ]);

        $response->assertJsonValidationErrors('email');
        $this->assertCount(1, DB::table('password_resets')->get());
    }

    /** @test */
    public function guests_cannot_reset_password_with_invalid_token(): void
    {
        app(UserFactory::class)->withCredentials('user@mail.com')->create();

        $hasher = $this->mockHashCheck('INVALID_PASSWORD_TOKEN', 'RESET_PASSWORD_HASH', false);
        $hasher->shouldNotReceive('make');

        DB::table('password_resets')->insert([
            'email' => 'user@mail.com',
            'token' => 'RESET_PASSWORD_HASH',
            'created_at' => now(),
        ]);

        $response = $this->resetPassword([
            'email' => 'user@mail.com',
            'password' => 'NEW_PASSWORD',
            'token' => 'INVALID_PASSWORD_TOKEN',
        ]);

        $response->assertJsonValidationErrors('email');
        $this->assertCount(1, DB::table('password_resets')->get());
    }

    /** @test */
    public function authenticated_users_cannot_reset_password_with_reset_token(): void
    {
        $user = app(UserFactory::class)->withCredentials('user@mail.com')->create();
        $this->be($user);

        DB::table('password_resets')->insert([
            'email' => 'user@mail.com',
            'token' => 'RESET_PASSWORD_HASH',
            'created_at' => now(),
        ]);

        $response = $this->resetPassword([
            'email' => 'user@mail.com',
            'password' => 'NEW_PASSWORD',
            'token' => 'RESET_PASSWORD_TOKEN',
        ]);

        $response->assertUnauthorized();
        $this->assertCount(1, DB::table('password_resets')->get());
    }

    /** @test */
    public function invalid_values_do_not_pass_the_reset_password_validation_process(): void
    {
        foreach ($this->invalidFields() as $field => $values) {
            foreach ($values as $rule => $value) {
                $response = $this->resetPassword([$field => $value]);
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

    // TODO: add unit tests for handlers
}
