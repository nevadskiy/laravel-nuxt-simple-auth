<?php

namespace Module\Auth\UseCases\PasswordForgot;

use Module\Auth\Notifications\ResetPasswordNotification;
use DomainException;
use Module\Auth\Repository\UserRepository;
use Nevadskiy\Tokens\Exceptions\LockoutException;
use Nevadskiy\Tokens\TokenManager;

class PasswordForgotHandler
{
    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Handler constructor.
     *
     * @param TokenManager $tokenManager
     * @param UserRepository $userRepository
     */
    public function __construct(TokenManager $tokenManager, UserRepository $userRepository)
    {
        $this->tokenManager = $tokenManager;
        $this->userRepository = $userRepository;
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
        $user = $this->userRepository->getByEmail($command->email);

        $token = $this->tokenManager->generateFor($user, 'password.reset');

        $user->notify(new ResetPasswordNotification($token));
    }
}
