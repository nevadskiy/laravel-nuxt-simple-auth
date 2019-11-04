<?php

namespace App\Services\Auth;

interface PasswordHasherInterface
{
    /**
     * Hash the given password.
     *
     * @param string $password
     * @return string
     */
    public function hash(string $password): string;
}
