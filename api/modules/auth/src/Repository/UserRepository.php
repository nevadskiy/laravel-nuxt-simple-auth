<?php

namespace Module\Auth\Repository;

use Module\Auth\Models\User;

interface UserRepository
{
    /**
     * Get the user by the given email.
     *
     * @param string $email
     * @return User
     */
    public function getByEmail(string $email): User;
}
