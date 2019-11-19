<?php

namespace Tests\Auth\Unit\UseCases\ForgotPassword;

use App\Auth\UseCases\ForgotPassword\Command;
use App\Auth\UseCases\ForgotPassword\Handler;
use DomainException;
use Illuminate\Contracts\Auth\PasswordBroker;
use Mockery;
use Tests\DatabaseTestCase;

/**
 * @see Handler
 */
class HandlerTest extends DatabaseTestCase
{
    /** @test */
    public function it_sends_reset_password_link(): void
    {
        $broker = Mockery::mock(PasswordBroker::class)
            ->shouldReceive('sendResetLink')
            ->with(['email' => 'test@mail.com'])
            ->andReturn(PasswordBroker::RESET_LINK_SENT)
            ->getMock();

        (new Handler($broker))->handle(new Command('test@mail.com'));
    }

    /** @test */
    public function it_throws_an_exception_if_send_returns_different_result(): void
    {
        $broker = Mockery::mock(PasswordBroker::class)
            ->shouldReceive('sendResetLink')
            ->with(['email' => 'test@mail.com'])
            ->andReturn(PasswordBroker::INVALID_USER)
            ->getMock();

        $this->expectException(DomainException::class);

        (new Handler($broker))->handle(new Command('test@mail.com'));
    }
}
