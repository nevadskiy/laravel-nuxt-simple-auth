<?php

namespace Module\Auth\Tests\Feature;

use Module\Auth\Models\User;
use Illuminate\Http\Response;
use Module\Auth\Tests\DatabaseTestCase;

class SignUpStoreTest extends DatabaseTestCase
{
    use AuthRequests;

    /** @test */
    public function guests_can_sign_up_with_email_and_password(): void
    {
        $response = $this->signUpRequest([
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
        $this->signUpRequest(['email' => 'user@mail.com'])
            ->assertJsonStructure([
                'data' => ['id', 'email']
            ])
            ->assertJsonFragment([
                'email' => 'user@mail.com'
            ]);
    }

    /** @test */
    public function authenticated_users_cannot_register_a_new_account(): void
    {
        $this->signIn();

        $response = $this->signUpRequest(['email' => 'second@mail.com']);

        $this->assertCount(1, User::all());
        $response->assertUnauthorized();
    }

    /** @test */
    public function invalid_values_do_not_pass_the_sign_up_validation_process(): void
    {
        foreach ($this->invalidFields() as $field => $values) {
            foreach ($values as $rule => $value) {
                $response = $this->signUpRequest([$field => $value]);
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

        $response = $this->signUpRequest(['email' => 'example@mail.com']);

        $this->assertCount(1, User::all());
        $response->assertJsonValidationErrors('email');
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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
