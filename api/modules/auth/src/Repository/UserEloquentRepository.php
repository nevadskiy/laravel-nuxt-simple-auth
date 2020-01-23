<?php

namespace Module\Auth\Repository;

use Module\Auth\Models\User;

class UserEloquentRepository implements UserRepository
{
    /**
     * @inheritDoc
     */
    public function getByEmail(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }
}
