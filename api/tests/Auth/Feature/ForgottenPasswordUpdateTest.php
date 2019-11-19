<?php

namespace Tests\Auth\Feature;

use App\Auth\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\DatabaseTestCase;
use Tests\Auth\Factory\UserFactory;

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
        $hasher->shouldReceive('check')->with('RESET_PASSWORD_TOKEN', 'RESET_PASSWORD_HASH')->andReturn(true);
        $hasher->shouldReceive('make')->with('NEW_PASSWORD')->andReturn('NEW_PASSWORD_HASH');
        $this->app->instance('hash', $hasher);

        DB::table('password_resets')->insert([
            'email' => 'user@mail.com',
            'token' => 'RESET_PASSWORD_HASH',
            'created_at' => now(),
        ]);

        $response = $this->resetPasswordRequest([
            'email' => 'user@mail.com',
            'password' => 'NEW_PASSWORD',
            'token' => 'RESET_PASSWORD_TOKEN',
        ]);

        $response->assertOk();
        $response->assertExactJson(['message' => __('passwords.reset')]);
        $this->assertEmpty(DB::table('password_resets')->get());
        $this->assertEquals('NEW_PASSWORD_HASH', $user->fresh()->password);
        $this->assertNull($user->fresh()->api_token);
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

        $response = $this->resetPasswordRequest([
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

        $hasher = $this->mock(Hasher::class);
        $hasher->shouldReceive('check')->with('INVALID_PASSWORD_TOKEN', 'RESET_PASSWORD_HASH')->andReturn(false);
        $hasher->shouldNotReceive('make');
        $this->app->instance('hash', $hasher);

        DB::table('password_resets')->insert([
            'email' => 'user@mail.com',
            'token' => 'RESET_PASSWORD_HASH',
            'created_at' => now(),
        ]);

        $response = $this->resetPasswordRequest([
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

        $response = $this->resetPasswordRequest([
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
