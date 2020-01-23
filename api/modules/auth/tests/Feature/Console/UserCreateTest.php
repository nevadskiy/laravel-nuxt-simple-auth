<?php

namespace Module\Auth\Tests\Feature\Console;

use Illuminate\Contracts\Hashing\Hasher;
use Module\Auth\Models\User;
use Module\Auth\Tests\DatabaseTestCase;

class UserCreateTest extends DatabaseTestCase
{
    // TODO: add validation testing...
    // TODO: add duplication testing...

    /** @test */
    public function user_can_be_registered_using_console(): void
    {
        $this->mock(Hasher::class)
            ->shouldReceive('make')
            ->once()
            ->with('secret123')
            ->andReturn('secret_hash');

        $this->artisan('user:create')
            ->expectsQuestion('Enter an email', 'john@mail.com')
            ->expectsQuestion('Enter a password', 'secret123')
            ->assertExitCode(0);

        $this->assertDatabaseHas(User::TABLE, [
            'email' => 'john@mail.com',
            'password' => 'secret_hash',
        ]);
    }
}
