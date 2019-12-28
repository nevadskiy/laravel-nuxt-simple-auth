<?php

namespace Module\Auth\Services\TokenGenerator;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Str;

class RandomTokenGenerator implements ApiTokenGenerator
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * RandomTokenGenerator constructor.
     *
     * @param Guard $guard
     */
    public function __construct(Guard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Generate an api token.
     *
     * @return string
     */
    public function generate(): string
    {
        $token = $this->createToken();

        if (! $this->tokenExists($token)) {
            return $token;
        }

        return $this->generate();
    }

    /**
     * Determine if token exists.
     *
     * @param string $token
     * @return bool
     */
    private function tokenExists(string $token): bool
    {
        return $this->guard->validate(['api_token' => $token]);
    }

    /**
     * Create a random token.
     *
     * @return string
     */
    private function createToken(): string
    {
        return Str::random(128);
    }
}
