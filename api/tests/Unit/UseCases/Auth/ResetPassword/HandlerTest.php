<?php

namespace Tests\Feature\Auth\ResetPassword;

use App\UseCases\Auth\ResetPassword\Command;
use App\UseCases\Auth\ResetPassword\Handler;
use App\User;
use DomainException;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Hashing\Hasher;
use Mockery;
use Tests\DatabaseTestCase;

/**
 * @see Handler
 */
class HandlerTest extends DatabaseTestCase
{
    /** @test */
    public function it_resets_password_for_user_with_token(): void
    {
        $user = factory(User::class)->create(['email' => 'user@mail.com']);

        $hasher = Mockery::mock(Hasher::class)
            ->shouldReceive('make')
            ->with('NEW_PASSWORD')
            ->andReturn('HASH_NEW_PASSWORD')
            ->getMock();

        $broker = Mockery::mock(PasswordBroker::class)
            ->shouldReceive('reset')
            ->with([
                'email' => 'user@mail.com',
                'password' => 'NEW_PASSWORD',
                'token' => 'RESET_PASSWORD_TOKEN',
            ], Mockery::on(function ($callback) use ($user) {
                $callback($user, 'NEW_PASSWORD');
                return true;
            }))
            ->andReturn(PasswordBroker::PASSWORD_RESET)
            ->getMock();

        (new Handler($broker, $hasher))->handle(new Command('user@mail.com', 'NEW_PASSWORD', 'RESET_PASSWORD_TOKEN'));

        $this->assertEquals('HASH_NEW_PASSWORD', $user->fresh()->password);
        $this->assertNull($user->fresh()->api_token);
    }

    /** @test */
    public function it_throws_an_exception_if_reset_returns_different_result(): void
    {
        $hasher = Mockery::mock(Hasher::class)
            ->shouldNotReceive('make')
            ->getMock();

        $broker = Mockery::mock(PasswordBroker::class)
            ->shouldReceive('reset')
            ->with([
                'email' => 'user@mail.com',
                'password' => 'NEW_PASSWORD',
                'token' => 'RESET_PASSWORD_TOKEN',
            ], Mockery::type('callable'))
            ->andReturn(PasswordBroker::INVALID_TOKEN)
            ->getMock();

        $this->expectException(DomainException::class);

        (new Handler($broker, $hasher))->handle(new Command('user@mail.com', 'NEW_PASSWORD', 'RESET_PASSWORD_TOKEN'));
    }
}
