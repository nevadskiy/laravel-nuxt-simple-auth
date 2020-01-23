<?php

namespace Module\Auth\UseCases\SignOut;

class SignOutHandler
{
    /**
     * Handle the sign out use case.
     *
     * @param SignOutCommand $command
     */
    public function handle(SignOutCommand $command): void
    {
        $command->user->update(['api_token' => null]);
    }
}
