<?php

namespace Module\Auth\UseCases\PasswordReset;

use Module\Auth\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Hashing\Hasher;
use Module\Auth\Repository\UserRepository;
use Nevadskiy\Tokens\Exceptions\TokenException;
use Nevadskiy\Tokens\TokenManager;

class PasswordResetHandler
{
    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Handler constructor.
     *
     * @param TokenManager $tokenManager
     * @param Hasher $hasher
     * @param UserRepository $userRepository
     */
    public function __construct(TokenManager $tokenManager, Hasher $hasher, UserRepository $userRepository)
    {
        $this->tokenManager = $tokenManager;
        $this->hasher = $hasher;
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the reset password use case.
     *
     * @param PasswordResetCommand $command
     * @return void
     * @throws TokenException
     */
    public function handle(PasswordResetCommand $command): void
    {
        $this->tokenManager->useFor(
            $command->token,
            'password.reset',
            $this->userRepository->getByEmail($command->email),
            function (User $user) use ($command) {
                $this->reset($user, $command->password);
            }
        );
    }

    /**
     * Reset the user password.
     *
     * @param User $user
     * @param string $password
     */
    public function reset(User $user, string $password): void
    {
        $this->setUserPassword($user, $password);
        $this->clearApiToken($user);
        $user->save();

        event(new PasswordReset($user));
    }

    /**
     * Set the password for the given user.
     *
     * @param User $user
     * @param string $password
     */
    private function setUserPassword(User $user, string $password): void
    {
        $user->password = $this->hasher->make($password);
    }

    /**
     * Clear the user api token.
     *
     * @param User $user
     */
    private function clearApiToken(User $user): void
    {
        $user->api_token = null;
    }
}
