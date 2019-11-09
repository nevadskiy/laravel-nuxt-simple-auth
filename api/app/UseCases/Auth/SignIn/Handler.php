<?php

namespace App\UseCases\Auth\SignIn;

use App\Services\Auth\ApiTokenGenerator;
use App\User;
use DomainException;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class Handler
{
    // TODO: add rate limiter

    /**
     * @var UserProvider
     */
    private $provider;

    /**
     * @var ApiTokenGenerator
     */
    private $generator;

    /**
     * Handler constructor.
     *
     * @param UserProvider $provider
     * @param ApiTokenGenerator $generator
     */
    public function __construct(UserProvider $provider, ApiTokenGenerator $generator)
    {
        $this->provider = $provider;
        $this->generator = $generator;
    }

    /**
     * Handle the sign in use case.
     *
     * @param Command $command
     * @return User
     */
    public function handle(Command $command): User
    {
        $user = $this->findUser($command);

        if (! $user || ! $this->validateUserCredentials($command, $user)) {
            throw new DomainException(__('auth.failed'));
        }

        $this->generateToken($user);

        event(new Authenticated('api', $user));

        return $user;
    }

    /**
     * Find a user.
     *
     * @param Command $command
     * @return User|Authenticatable|null
     */
    private function findUser(Command $command): ?User
    {
        return $this->provider->retrieveByCredentials((array) $command);
    }

    /**
     * Validate the user credentials.
     *
     * @param Command $command
     * @param User $user
     * @return bool
     */
    private function validateUserCredentials(Command $command, User $user): bool
    {
        return $this->provider->validateCredentials($user, (array) $command);
    }

    /**
     * Generate a token for the given user.
     *
     * @param User|null $user
     */
    private function generateToken(?User $user): void
    {
        $user->update(['api_token' => $this->generator->generate()]);
    }
}
