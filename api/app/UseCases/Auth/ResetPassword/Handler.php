<?php

namespace App\UseCases\Auth\ResetPassword;

use App\User;
use DomainException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Hashing\Hasher;

class Handler
{
    /**
     * @var PasswordBroker
     */
    private $broker;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * Handler constructor.
     *
     * @param PasswordBroker $broker
     * @param Hasher $hasher
     */
    public function __construct(PasswordBroker $broker, Hasher $hasher)
    {
        $this->broker = $broker;
        $this->hasher = $hasher;
    }

    /**
     * Handle the sign up use case.
     *
     * @param Command $command
     * @return void
     */
    public function handle(Command $command): void
    {
        $result = $this->broker->reset((array) $command, function (User $user, string $password) {
            $this->setUserPassword($user, $password);
            $this->clearApiToken($user);
            $user->save();

            event(new PasswordReset($user));
        });

        if (PasswordBroker::PASSWORD_RESET !== $result) {
            throw new DomainException(__($result));
        }
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
