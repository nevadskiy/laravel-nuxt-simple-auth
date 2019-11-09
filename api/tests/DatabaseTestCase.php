<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class DatabaseTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * To be signed in.
     *
     * @param User|null $user
     * @return DatabaseTestCase
     */
    public function signIn(User $user = null): self
    {
        return $this->be($user ?: factory(User::class)->create());
    }
}
