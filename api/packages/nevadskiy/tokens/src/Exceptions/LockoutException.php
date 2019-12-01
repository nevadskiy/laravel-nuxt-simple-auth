<?php

namespace Nevadskiy\Tokens\Exceptions;

use Carbon\Carbon;
use DateTimeInterface;

class LockoutException extends TokenException
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
     * @param Carbon $timeout
     * @param string $key
     */
    public function __construct(string $message = '', Carbon $timeout = null, string $key = null)
    {
        parent::__construct($message);
        $this->timeout = $timeout;
        $this->key = $key;
    }

    /**
     * Static constructor.
     *
     * @param Carbon $timeout
     * @param string $key
     * @param string $message
     * @return LockoutException
     */
    public static function withTimeout(Carbon $timeout = null, string $message = '', string $key = null): self
    {
        return new static($message, $timeout, $key);
    }

    /**
     * Get the unlock time.
     *
     * @return Carbon
     */
    public function getUnlockTime(): Carbon
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
