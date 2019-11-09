<?php

namespace Tests\Feature\Auth;

use App\Notifications\Auth\ResetPasswordNotification;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\DatabaseTestCase;
use Tests\Factory\UserFactory;

class ForgottenPasswordStoreTest extends DatabaseTestCase
{
    use AuthRequests;

    /** @test */
    public function guests_can_request_a_reset_password_link_to_their_account(): void
    {
        Notification::fake();

        $user = app(UserFactory::class)->withCredentials('user@mail.com')->create();

        $response = $this->forgotPasswordRequest(['email' => 'user@mail.com']);

        $response->assertCreated();
        $response->assertExactJson(['message' => __('passwords.sent')]);
        $this->assertDatabaseHas('password_resets', ['email' => 'user@mail.com']);
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    /** @test */
    public function guests_cannot_request_a_reset_password_link_with_unknown_email(): void
    {
        Notification::fake();

        $response = $this->forgotPasswordRequest(['email' => 'unknown@mail.com']);

        $response->assertJsonValidationErrors('email');
        $this->assertEmpty(DB::table('password_resets')->get());
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
            $this->assertEmpty(DB::table('password_resets')->get(), "Token was generated for invalid email on {$rule}");
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
