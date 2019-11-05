<?php

namespace Tests\Feature\Auth\SignIn;

use App\Services\Auth\ApiTokenGenerator;
use App\UseCases\Auth\SignIn\Command;
use App\UseCases\Auth\SignIn\Handler;
use App\User;
use DomainException;
use Illuminate\Contracts\Hashing\Hasher;
use Tests\DatabaseTestCase;

class HandlerTest extends DatabaseTestCase
{
    /** @test */
    public function it_retrieves_user_by_credentials(): void
    {
        $user = factory(User::class)->create([
            'email' => 'user@mail.com',
            'password' => 'database.password',
        ]);

        $this->mockPasswordHasher('request.password', 'database.password', true);

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

        $this->mockPasswordHasher('request.password', 'database.password', true);

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
    public function it_throws_an_exception_if_password_is_not_correct(): void
    {
        factory(User::class)->create([
            'email' => 'user@mail.com',
            'password' => 'database.password',
        ]);

        $this->mockPasswordHasher('password', 'database.password', false);

        $command = new Command('user@mail.com', 'password');

        $spy = $this->spy(ApiTokenGenerator::class);

        try {
            app(Handler::class)->handle($command);
            $this->fail('Exception was not thrown but should.');
        } catch (DomainException $e) {
            $spy->shouldNotHaveReceived('generate');
        }
    }

    /**
     * Mock the password hasher.
     *
     * @param string $requestPassword
     * @param string $databasePassword
     * @param bool $result
     */
    private function mockPasswordHasher(
        string $requestPassword = 'request.password',
        string $databasePassword = 'database.password',
        bool $result = true
    ): void
    {
        $hasher = $this->mock(Hasher::class)
            ->shouldReceive('check')
            ->once()
            ->with($requestPassword, $databasePassword)
            ->andReturn($result)
            ->getMock();

        $this->app->instance('hash', $hasher);
    }
}
