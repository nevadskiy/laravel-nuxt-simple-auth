<?php

namespace Module\Auth\UseCases\PasswordForgot;

use Module\Auth\Models\User;
use Module\Auth\Notifications\ResetPasswordNotification;
use DomainException;
use Nevadskiy\Tokens\Exceptions\LockoutException;
use Nevadskiy\Tokens\TokenManager;

class PasswordForgotHandler
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
     * @param PasswordForgotCommand $command
     * @return void
     * @throws LockoutException|DomainException
     */
    public function handle(PasswordForgotCommand $command): void
    {
        $user = $this->getUser($command);

        $token = $this->tokenManager->generateFor($user, 'password.reset');

        $user->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the user user.
     *
     * @param PasswordForgotCommand $command
     * @return User
     */
    protected function getUser(PasswordForgotCommand $command): User
    {
        return User::where('email', $command->email)->firstOrFail();
    }
}
