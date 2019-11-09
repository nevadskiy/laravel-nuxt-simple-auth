<?php

namespace App\UseCases\Auth\SignOut;

use App\User;

class Handler
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
