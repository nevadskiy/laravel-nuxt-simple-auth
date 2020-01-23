<?php

namespace Module\Auth\UseCases\SignOut;

use Module\Auth\Models\User;

class SignOutCommand
{
    /**
     * @var User
     */
    public $user;

    /**
     * SignOutCommand constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
