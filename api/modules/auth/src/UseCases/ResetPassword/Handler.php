<?php

namespace Module\Auth\UseCases\ResetPassword;

use Module\Auth\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Hashing\Hasher;
use Nevadskiy\Tokens\Exceptions\TokenException;
use Nevadskiy\Tokens\TokenManager;

class Handler
{
    /**
     * @var TokenManager
     */
    protected $tokenManager;

    /**
     * @var Hasher
     */
    protected $hasher;

    /**
     * Handler constructor.
     *
     * @param TokenManager $tokenManager
     * @param Hasher $hasher
     */
    public function __construct(TokenManager $tokenManager, Hasher $hasher)
    {
        $this->tokenManager = $tokenManager;
        $this->hasher = $hasher;
    }

    /**
     * Handle the reset password use case.
     *
     * @param Command $command
     * @return void
     * @throws TokenException
     */
    public function handle(Command $command): void
    {
        $user = $this->getUser($command);

        $this->tokenManager->useFor($command->token, 'password.reset', $user, function (User $user) use ($command) {
            $this->setUserPassword($user, $command->password);
            $this->clearApiToken($user);
            $user->save();

            event(new PasswordReset($user));
        });
    }

    /**
     * Get the user.
     *
     * @param Command $command
     * @return User
     */
    protected function getUser(Command $command): User
    {
        return User::where('email', $command->email)->firstOrFail();
    }

    /**
     * Set the password for the given user.
     *
     * @param User $user
     * @param string $password
     */
    protected function setUserPassword(User $user, string $password): void
    {
        $user->password = $this->hasher->make($password);
    }

    /**
     * Clear the user api token.
     *
     * @param User $user
     */
    protected function clearApiToken(User $user): void
    {
        $user->api_token = null;
    }
}
