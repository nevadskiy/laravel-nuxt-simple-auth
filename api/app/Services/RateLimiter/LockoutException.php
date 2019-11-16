<?php

namespace App\Services\RateLimiter;

use DateTimeInterface;
use DomainException;

class LockoutException extends DomainException
{
    /**
     * @var DateTimeInterface
     */
    private $timeout;

    /**
     * @var string
     */
    private $key;

    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param DateTimeInterface $timeout
     * @param string $key
     */
    public function __construct(string $message = '', DateTimeInterface $timeout = null, string $key = null)
    {
        parent::__construct($message);
        $this->timeout = $timeout;
        $this->key = $key;
    }

    /**
     * Static constructor for the exception.
     *
     * @param DateTimeInterface|null $timeout
     * @param string|null $key
     * @param string $message
     * @return LockoutException
     */
    public static function withTimeout(DateTimeInterface $timeout = null, string $key = null, string $message = ''): self
    {
        return new static($message, $timeout, $key);
    }

    /**
     * Get the unlock time.
     *
     * @return DateTimeInterface
     */
    public function getUnlockTime(): DateTimeInterface
    {
        return $this->timeout;
    }

    /**
     * Get the lockout key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
