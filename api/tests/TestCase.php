<?php

namespace Tests;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery\MockInterface;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Mock the hasher.
     *
     * @param string $raw
     * @param string $hash
     * @param bool $result
     * @return Hasher|MockInterface
     */
    protected function mockHashCheck(string $raw = 'password', string $hash = 'hash', bool $result = true)
    {
        $hasher = $this->mock(Hasher::class)
            ->shouldReceive('check')
            ->once()
            ->with($raw, $hash)
            ->andReturn($result)
            ->getMock();

        $this->app->instance('hash', $hasher);

        return $hasher;
    }
}
