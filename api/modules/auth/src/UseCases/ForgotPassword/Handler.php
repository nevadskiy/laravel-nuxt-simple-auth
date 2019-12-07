<?php

namespace Module\Auth\UseCases\ForgotPassword;

use Module\Auth\Models\User;
use Module\Auth\Notifications\ResetPasswordNotification;
use DomainException;
use Nevadskiy\Tokens\Exceptions\LockoutException;
use Nevadskiy\Tokens\TokenManager;

class Handler
{
    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * Handler constructor.
     *
     * @param TokenManager $tokenManager
     */
    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * Handle the forgot password use case.
     *
     * @param Command $command
     * @return void
     * @throws LockoutException|DomainException
     */
    public function handle(Command $command): void
    {
        $user = $this->getUser($command);

        $token = $this->tokenManager->generateFor($user, 'password.reset');

        $user->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the user user.
     *
     * @param Command $command
     * @return User
     */
    protected function getUser(Command $command): User
    {
        return User::where('email', $command->email)->firstOrFail();
    }
}
