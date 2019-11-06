<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;
use Tests\DatabaseTestCase;

class SignUpStoreTest extends DatabaseTestCase
{
    // TODO: authenticated_user_cannot_register_a_new_account
    // TODO: email verification
    // TODO: test that api token is null

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
        $this->signUp(['email' => 'user@mail.com'])
            ->assertJsonStructure([
                'data' => ['id', 'email']
            ])
            ->assertJsonFragment([
                'email' => 'user@mail.com'
            ]);
    }

    /** @test */
    public function invalid_values_do_not_pass_the_sign_up_validation_process(): void
    {
        foreach ($this->invalidFields() as $field => $values) {
            foreach ($values as $rule => $value) {
                $response = $this->signUp([$field => $value]);
                $this->assertEmpty(User::all(), "Request was processed with the invalid {$field} for the rule {$rule}");
                $response->assertJsonValidationErrors($field);
                $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }

    /** @test */
    public function a_username_must_be_unique_for_signing_up(): void
    {
        factory(User::class)->create(['email' => 'example@mail.com']);

        $response = $this->signUp(['email' => 'example@mail.com']);

        $this->assertCount(1, User::all());
        $response->assertJsonValidationErrors('email');
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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
                'max:255' => str_repeat('A', 256 - 9) . '@mail.com'
            ],

            'password' => [
                'required' => '',
                'string' => ['INVALID_STRING'],
                'min:8' => 'SHORT..',
                'max:255' => str_repeat('A', 256),
            ],
        ];
    }
}
