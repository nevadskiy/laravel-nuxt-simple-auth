<?php

namespace Nevadskiy\Tokens;

use DateInterval;
use DateTimeInterface;

interface Token
{
    /**
     * Get the token name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the token expiration date.
     *
     * @return DateInterval|DateTimeInterface|int
     */
    public function getExpirationDate();

    /**
     * Get the token generation strategy name.
     * Can be one of ['remove', 'keep', 'reuse'].
     *
     * @return string
     */
    public function getGenerationStrategy(): string;

    /**
     * Determine if the token generation throttling is enabled.
     *
     * @return bool
     */
    public function isGenerationThrottlingEnabled(): bool;

    /**
     * Get the key for identifying attempts for throttling limiter on generation process.
     *
     * @return string
     */
    public function getGenerationLimiterKey(): string;

    /**
     * Get maximum token generation attempts amount for throttling limiter
     *
     * @return int
     */
    public function getGenerationAttempts(): int;

    /**
     * Get the time interval limited generation attempts can be exhausted within.
     *
     * @return DateInterval|DateTimeInterface|int
     */
    public function getGenerationAttemptsInterval();

    /**
     * Determine if the token usage throttling is enabled.
     *
     * @return bool
     */
    public function isUsageThrottlingEnabled(): bool;

    /**
     * Get the key for identifying attempts for throttling limiter on usage process.
     *
     * @return string
     */
    public function getUsageLimiterKey(): string;

    /**
     * Get maximum token usage attempts amount for throttling limiter
     *
     * @return int
     */
    public function getUsageAttempts(): int;

    /**
     * Get the time interval limited usage attempts can be exhausted within.
     *
     * @return DateInterval|DateTimeInterface|int
     */
    public function getUsageAttemptsInterval();
}
