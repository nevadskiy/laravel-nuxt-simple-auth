<?php

namespace Module\Auth\UseCases\SignOut;

use Module\Auth\Models\User;

class SignOutHandler
{
    /**
     * Handle the sign out use case.
     *
     * @param User $user
     */
    public function handle(User $user): void
    {
        $user->update(['api_token' => null]);
    }
}
