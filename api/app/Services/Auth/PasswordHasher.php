<?php

namespace App\Services\Auth;

class PasswordHasher implements PasswordHasherInterface
{
    /**
     * Hash the given password.
     *
     * @param string $password
     * @return string
     */
    public function hash(string $password): string
    {
        return bcrypt($password);
    }
}
