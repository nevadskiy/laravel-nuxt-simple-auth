<?php

namespace Tests\Feature\Auth;

use App\Services\Auth\PasswordHasherInterface;
use App\User;
use Tests\ApiTestCase;
use Mockery;

class ExampleTest extends ApiTestCase
{
    /** @test */
    public function guests_can_sign_up_with_email_and_password(): void
    {
        $hasher = Mockery::mock(PasswordHasherInterface::class)
            ->shouldReceive('hash')
            ->once()
            ->with('secret123')
            ->andReturn('secret123')
            ->getMock();

        $this->app->instance(PasswordHasherInterface::class, $hasher);

        $response = $this->postJson(route('api.signup.store'), [
            'email' => 'guest@mail.com',
            'passport' => 'secret123',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas(User::TABLE, [
            'email' => 'guest@mail.com',
            'password' => 'secret123',
        ]);
    }
}
