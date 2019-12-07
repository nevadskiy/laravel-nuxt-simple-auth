<?php

namespace Module\Auth\Tests\Unit\UseCases\ForgotPassword;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Notification;
use Module\Auth\Models\User;
use Module\Auth\Notifications\ResetPasswordNotification;
use Module\Auth\UseCases\ForgotPassword\Command;
use Module\Auth\UseCases\ForgotPassword\Handler;
use Module\Auth\Tests\DatabaseTestCase;
use Nevadskiy\Tokens\TokenEntity;

/**
 * @see Handler
 */
class ForgotPasswordHandlerTest extends DatabaseTestCase
{
    /** @test */
    public function it_sends_reset_password_link(): void
    {
        Notification::fake();

        $user = factory(User::class)->create(['email' => 'user@mail.com']);

        app(Handler::class)->handle(new Command('user@mail.com'));

        $token = TokenEntity::last();

        $this->assertTrue($token->tokenable->is($user));

        Notification::assertSentTo($user, ResetPasswordNotification::class, function (ResetPasswordNotification $n) use ($token) {
            return $n->token === $token->toString();
        });
    }

    /** @test */
    public function it_throws_an_exception_if_send_returns_different_result(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(Handler::class)->handle(new Command('test@mail.com'));
    }
}
