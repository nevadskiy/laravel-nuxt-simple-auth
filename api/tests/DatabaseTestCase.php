<?php

namespace Tests;

use App\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class DatabaseTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * Create user with provided credentials.
     *
     * @param string $email
     * @param string $password
     * @return User
     */
    protected function createUserWithCredentials(string $email = 'user@mail.com', string $password = 'secret123'): User
    {
        return factory(User::class)->create([
            'email' => $email,
            'password' => resolve(Hasher::class)->make($password),
        ]);
    }
}
