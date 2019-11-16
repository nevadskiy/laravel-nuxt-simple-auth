<?php

namespace Tests\Feature\Auth\SignIn;

use App\Services\Auth\TokenGenerator\ApiTokenGenerator;
use App\Services\RateLimiter\RateLimiter;
use App\UseCases\Auth\SignIn\Command;
use App\UseCases\Auth\SignIn\Handler;
use App\User;
use Carbon\CarbonInterval;
use DateInterval;
use DomainException;
use Illuminate\Contracts\Hashing\Hasher;
use Tests\DatabaseTestCase;

/**
 * @see Handler
 */
class HandlerTest extends DatabaseTestCase
{
    /** @test */
    public function it_retrieves_user_by_credentials(): void
    {
        $user = factory(User::class)->create([
            'email' => 'user@mail.com',
            'password' => 'database.password',
        ]);

        $hasher = $this->mock(Hasher::class)
            ->shouldReceive('check')
            ->once()
            ->with('request.password', 'database.password')
            ->andReturn(true)
            ->getMock();

        $this->app->instance('hash', $hasher);

        $command = new Command('user@mail.com', 'request.password');

        $authUser = app(Handler::class)->handle($command);

        $this->assertTrue($authUser->is($user));
    }

    /** @test */
    public function it_generates_a_api_token_for_the_found_user(): void
    {
        factory(User::class)->create([
            'email' => 'user@mail.com',
            'password' => 'database.password',
        ]);

        $hasher = $this->mock(Hasher::class)
            ->shouldReceive('check')
            ->once()
            ->with('request.password', 'database.password')
            ->andReturn(true)
            ->getMock();

        $this->app->instance('hash', $hasher);

        $this->mock(ApiTokenGenerator::class)
            ->shouldReceive('generate')
            ->once()
            ->andReturn('simple-api-token');

        $command = new Command('user@mail.com', 'request.password');

        $user = app(Handler::class)->handle($command);

        $this->assertEquals('simple-api-token', $user->fresh()->api_token);
    }

    /** @test */
    public function it_throws_an_exception_if_user_is_not_found(): void
    {
        $spy = $this->spy(ApiTokenGenerator::class);

        try {
            app(Handler::class)->handle(new Command('user@mail.com', 'password'));
            $this->fail('Exception was not thrown but should.');
        } catch (DomainException $e) {
            $spy->shouldNotHaveReceived('generate');
        }
    }

    /** @test */
    public function it_throws_an_exception_if_password_is_not_correct(): void
    {
        factory(User::class)->create([
            'email' => 'user@mail.com',
            'password' => 'database.password',
        ]);

        $hasher = $this->mock(Hasher::class)
            ->shouldReceive('check')
            ->once()
            ->with('password', 'database.password')
            ->andReturn(false)
            ->getMock();

        $this->app->instance('hash', $hasher);

        $command = new Command('user@mail.com', 'password');

        $spy = $this->spy(ApiTokenGenerator::class);

        try {
            app(Handler::class)->handle($command);
            $this->fail('Exception was not thrown but should.');
        } catch (DomainException $e) {
            $spy->shouldNotHaveReceived('generate');
        }
    }

    /** @test */
    public function it_uses_rate_limiter_for_sign_in_process(): void
    {
        $this->freezeTime();

        config(['auth.sign_in.rate_limiter.max_attempts' => 3]);
        config(['auth.sign_in.rate_limiter.seconds' => 40]);

        $user = factory(User::class)->create([
            'email' => 'user@mail.com',
            'password' => 'database.password',
        ]);

        $hasher = $this->app->instance('hash', $this->mock(Hasher::class));
        $hasher->shouldReceive('check')->with('password', 'database.password')->andReturn(true);

        $this->mock(RateLimiter::class)
            ->shouldReceive('limit')
            ->andReturnUsing(function (string $key, int $attempts, DateInterval $timeout, callable $callback) {
                $this->assertEquals('user@mail.com|127.0.0.1', $key);
                $this->assertEquals(3, $attempts);
                $this->assertEquals(CarbonInterval::second(40), $timeout);
                return $callback();
            });

        $this->assertTrue($user->is(app(Handler::class)->handle(new Command('user@mail.com', 'password'))));
    }
}
