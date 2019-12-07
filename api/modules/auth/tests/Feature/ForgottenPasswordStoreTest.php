<?php

namespace Module\Auth\Tests\Feature;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Module\Auth\Notifications\ResetPasswordNotification;
use Module\Auth\Tests\DatabaseTestCase;
use Module\Auth\Tests\Factory\UserFactory;
use Nevadskiy\Tokens\TokenEntity;

class ForgottenPasswordStoreTest extends DatabaseTestCase
{
    use AuthRequests;

    /** @test */
    public function guests_can_request_a_reset_password_link_to_their_account(): void
    {
        Notification::fake();

        $user = app(UserFactory::class)->withCredentials('user@mail.com')->create();

        $response = $this->forgotPasswordRequest(['email' => 'user@mail.com']);

        $token = TokenEntity::last();

        $token->tokenable->is($user);
        $response->assertCreated();
        $response->assertExactJson(['message' => __('auth::passwords.sent')]);

        Notification::assertSentTo(
            $user,
            ResetPasswordNotification::class,
            function (ResetPasswordNotification $notification) use ($token) {
                return $notification->token === $token->toString();
            }
        );
    }

    /** @test */
    public function validation_error_occurs_if_guest_tries_reset_password_for_email_that_is_not_registered(): void
    {
        Notification::fake();

        $response = $this->forgotPasswordRequest(['email' => 'another@mail.com']);

        $response->assertJsonValidationErrors(['email' => __('auth::passwords.not_found')]);
        $this->assertEmpty(TokenEntity::all());
        Notification::assertNothingSent();
    }

    /** @test */
    public function guest_cannot_send_too_many_reset_password_links(): void
    {
        app(UserFactory::class)->withCredentials('user@mail.com')->create();

        $response1 = $this->forgotPasswordRequest(['email' => 'user@mail.com']);
        $response2 = $this->forgotPasswordRequest(['email' => 'user@mail.com']);
        $response3 = $this->forgotPasswordRequest(['email' => 'user@mail.com']);

        Notification::fake();

        $response = $this->forgotPasswordRequest(['email' => 'user@mail.com']);

        $response1->assertCreated();
        $response2->assertCreated();
        $response3->assertCreated();

        $response->assertJsonValidationErrors(['email' => __('auth::passwords.throttle')]);
        Notification::assertNothingSent();
    }

    /** @test */
    public function guests_cannot_request_a_reset_password_link_with_unknown_email(): void
    {
        Notification::fake();

        $response = $this->forgotPasswordRequest(['email' => 'unknown@mail.com']);

        $response->assertJsonValidationErrors('email');
        $this->assertEmpty(TokenEntity::all());
        Notification::assertNothingSent();
    }

    /** @test */
    public function authenticated_users_cannot_request_a_reset_password_link(): void
    {
        Notification::fake();

        $user = app(UserFactory::class)->withCredentials('user@mail.com')->create();
        $this->be($user);

        $response = $this->forgotPasswordRequest(['email' => 'user@mail.com']);

        $response->assertUnauthorized();
        Notification::assertNothingSent();
    }

    /** @test */
    public function invalid_values_do_not_pass_the_forgot_password_process(): void
    {
        Notification::fake();

        foreach ($this->invalidEmails() as $rule => $value) {
            $response = $this->forgotPasswordRequest(['email' => $value]);
            $this->assertEmpty(TokenEntity::all(), "Token was generated for invalid email on {$rule}");
            $response->assertJsonValidationErrors('email');
            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
            Notification::assertNothingSent();
        }
    }

    /**
     * Invalid fields for testing validation errors.
     *
     * @return array
     */
    private function invalidEmails(): array
    {
        return [
            'required' => '',
            'string' => ['INVALID_STRING'],
            'email' => 'INVALID_EMAIL',
            'max:255' => str_repeat('A', 256 - 9) . '@mail.com',
        ];
    }
}
