<?php

namespace App\Auth\UseCases\SignIn;

use App\Auth\Models\User;
use App\Auth\Services\TokenGenerator\ApiTokenGenerator;
use App\Core\Services\RateLimiter\RateLimiter;
use Carbon\CarbonInterval;
use DomainException;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Handler
{
    /**
     * @var UserProvider
     */
    private $provider;

    /**
     * @var ApiTokenGenerator
     */
    private $generator;

    /**
     * @var RateLimiter
     */
    private $limiter;

    /**
     * @var Request
     */
    private $request;

    /**
     * Handler constructor.
     *
     * @param UserProvider $provider
     * @param ApiTokenGenerator $generator
     * @param RateLimiter $limiter
     * @param Request $request
     */
    public function __construct(
        UserProvider $provider,
        ApiTokenGenerator $generator,
        RateLimiter $limiter,
        Request $request
    )
    {
        $this->provider = $provider;
        $this->generator = $generator;
        $this->limiter = $limiter;
        $this->request = $request;
    }

    /**
     * Handle the sign in use case.
     *
     * @param Command $command
     * @return User
     */
    public function handle(Command $command): User
    {
        return $this->limiter->limit(
            $this->getThrottleKey($command->email),
            config('auth.sign_in.rate_limiter.max_attempts'),
            CarbonInterval::seconds(config('auth.sign_in.rate_limiter.seconds')),
            function () use ($command) {
                return $this->signIn($command);
            }
        );
    }

    /**
     * Handle the sign in use case.
     *
     * @param Command $command
     * @return User
     */
    public function signIn(Command $command): User
    {
        $user = $this->findUser($command);

        if (! $user || ! $this->validateUserCredentials($command, $user)) {
            throw new DomainException(__('auth.failed'));
        }

        $this->generateToken($user);

        // TODO: probably swap with guard->auth() method
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
     * @param User $user
     */
    private function generateToken(User $user): void
    {
        $user->update(['api_token' => $this->generator->generate()]);
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param string $email
     * @return string
     */
    private function getThrottleKey(string $email): string
    {
        return Str::lower("{$email}|{$this->request->ip()}");
    }
}
