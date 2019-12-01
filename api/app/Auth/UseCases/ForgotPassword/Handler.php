<?php

namespace App\Auth\UseCases\ForgotPassword;

use App\Auth\Models\User;
use App\Auth\Notifications\ResetPasswordNotification;
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
        $user = User::where('email', $command->email)->firstOrFail();

        $token = $this->tokenManager->generateFor($user, 'password.reset');

        $user->notify(new ResetPasswordNotification($token));
    }
}
