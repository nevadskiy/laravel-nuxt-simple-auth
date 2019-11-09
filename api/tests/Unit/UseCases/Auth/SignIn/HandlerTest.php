<?php

namespace Tests\Feature\Auth\SignIn;

use App\Services\Auth\ApiTokenGenerator;
use App\UseCases\Auth\SignIn\Command;
use App\UseCases\Auth\SignIn\Handler;
use App\User;
use DomainException;
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

        $this->mockHashCheck('request.password', 'database.password', true);

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

        $this->mockHashCheck('request.password', 'database.password', true);

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

        $this->mockHashCheck('password', 'database.password', false);

        $command = new Command('user@mail.com', 'password');

        $spy = $this->spy(ApiTokenGenerator::class);

        try {
            app(Handler::class)->handle($command);
            $this->fail('Exception was not thrown but should.');
        } catch (DomainException $e) {
            $spy->shouldNotHaveReceived('generate');
        }
    }
}
