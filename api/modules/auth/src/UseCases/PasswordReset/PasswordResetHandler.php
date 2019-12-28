<?php

namespace Module\Auth\UseCases\PasswordReset;

use Module\Auth\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Hashing\Hasher;
use Nevadskiy\Tokens\Exceptions\TokenException;
use Nevadskiy\Tokens\TokenManager;

class PasswordResetHandler
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
     * @param PasswordResetCommand $command
     * @return void
     * @throws TokenException
     */
    public function handle(PasswordResetCommand $command): void
    {
        $this->tokenManager->useFor(
            $command->token,
            'password.reset',
            $this->getUser($command),
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
     * Get the user.
     *
     * @param PasswordResetCommand $command
     * @return User
     */
    protected function getUser(PasswordResetCommand $command): User
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
