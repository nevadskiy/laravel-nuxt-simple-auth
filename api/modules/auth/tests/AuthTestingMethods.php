<?php

namespace Module\Auth\Tests;

use Module\Auth\Models\User;

/**
 * @mixin DatabaseTestCase
 */
trait AuthTestingMethods
{
    /**
     * To be signed in.
     *
     * @param User|null $user
     * @return AuthTestingMethods
     */
    public function signIn(User $user = null): self
    {
        return $this->be($user ?: factory(User::class)->create());
    }
}
