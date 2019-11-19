<?php

namespace App\Core\Services\RateLimiter;

use DateInterval;
use Illuminate\Cache\RateLimiter as CacheRateLimiter;

class RateLimiter
{
    /**
     * @var CacheRateLimiter
     */
    private $limiter;

    /**
     * RateLimiter constructor.
     *
     * @param CacheRateLimiter $limiter
     */
    public function __construct(CacheRateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle a callback with rate limiting applied.
     *
     * @param string $key
     * @param int $maxAttempts
     * @param DateInterval $timeout
     * @param callable $callback
     * @return mixed
     */
    public function limit(string $key, int $maxAttempts, DateInterval $timeout, callable $callback)
    {
        $this->guardTooManyAttempts($key, $maxAttempts);

        $this->attempt($key, $timeout);

        $result = $callback();

        $this->clear($key);

        return $result;
    }

    /**
     * Guard against too many request attempts.
     *
     * @param string $key
     * @param int $maxAttempts
     */
    private function guardTooManyAttempts(string $key, int $maxAttempts): void
    {
        if ($this->isLocked($key, $maxAttempts)) {
            $this->throwLockoutException($key);
        }
    }

    /**
     * Determine if the client reached the maximum available request attempts by the key.
     *
     * @param string $key
     * @param int $maxAttempts
     * @return bool
     */
    private function isLocked(string $key, int $maxAttempts): bool
    {
        return $this->limiter->tooManyAttempts($key, $maxAttempts);
    }

    /**
     * Clear the attempts by the key.
     *
     * @param string $key
     * @return void
     */
    private function clear(string $key): void
    {
        $this->limiter->clear($key);
    }

    /**
     * Increment the attempts for by the key with timeout.
     *
     * @param string $key
     * @param DateInterval $timeout
     * @return void
     */
    private function attempt(string $key, DateInterval $timeout): void
    {
        $this->limiter->hit($key, $timeout);
    }

    /**
     * Throw an exception when a lockout occurs.
     *
     * @param string $key
     */
    private function throwLockoutException(string $key): void
    {
        throw LockoutException::withTimeout(
            now()->addSeconds(
                $this->limiter->availableIn($key)
            )
        );
    }
}
