<?php

namespace Tests\Unit\Services\RateLimiter;

use App\Services\RateLimiter\LockoutException;
use App\Services\RateLimiter\RateLimiter;
use Illuminate\Cache\RateLimiter as CacheRateLimiter;
use Carbon\CarbonInterval;
use RuntimeException;
use Tests\TestCase;

/**
 * @see RateLimiter
 */
class RateLimiterTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_if_too_many_attempts(): void
    {
        $this->freezeTime();

        $limiter = $this->mock(CacheRateLimiter::class);
        $limiter->shouldReceive('tooManyAttempts')->with('test-key', 5)->andReturn(true);
        $limiter->shouldReceive('availableIn')->with('test-key')->andReturn(now()->addMinutes(10)->diffInSeconds());

        try {
            app(RateLimiter::class)->limit(
                'test-key',
                5,
                CarbonInterval::minutes(10),
                function () {}
            );

            $this->fail('Exception was not thrown when should.');
        } catch (LockoutException $e) {
            $this->assertEquals(now()->addMinutes(10), $e->getUnlockTime());
        }
    }

    /** @test */
    public function it_increments_timeout_when_callback_throws_an_exception(): void
    {
        $limiter = $this->mock(CacheRateLimiter::class);
        $limiter->shouldReceive('tooManyAttempts')->with('test-throttle-key', 10)->andReturn(false);
        $limiter->shouldReceive('hit')->with('test-throttle-key', CarbonInterval::minutes(10));

        $this->expectException(RuntimeException::class);

        app(RateLimiter::class)->limit(
            'test-throttle-key',
            10,
            CarbonInterval::minutes(10),
            function () {
                throw new RuntimeException('Simple exception');
            }
        );
    }

    /** @test */
    public function it_clears_attempts_after_success_callback_execution(): void
    {
        $this->freezeTime();

        $timeout = CarbonInterval::minutes(10);

        $limiter = $this->mock(CacheRateLimiter::class);
        $limiter->shouldReceive('tooManyAttempts')->with('test-throttle-key', 10)->andReturn(false);
        $limiter->shouldReceive('hit')->with('test-throttle-key', $timeout);
        $limiter->shouldReceive('clear')->with('test-throttle-key');

        $result = app(RateLimiter::class)->limit('test-throttle-key', 10, $timeout, function () {
            return 'Test result';
        });

        $this->assertEquals('Test result', $result);
    }
}
