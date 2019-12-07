<?php

namespace Module\Auth\Tests\Unit\Services\TokenGenerator;

use Module\Auth\Services\TokenGenerator\RandomTokenGenerator;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Str;
use Mockery;
use Module\Auth\Tests\AuthTestCase;

/**
 * @see RandomTokenGenerator
 */
class RandomTokenGeneratorTest extends AuthTestCase
{
    /** @test */
    public function it_generates_long_api_tokens(): void
    {
        $this->assertEquals(128, Str::length(app(RandomTokenGenerator::class)->generate()));
    }

    /** @test */
    public function it_generates_unique_api_tokens(): void
    {
        $tokens = [];

        $mock = $this->mock(Guard::class);

        $mock->shouldReceive('validate')
            ->once()
            ->with(Mockery::on(function ($token) use (&$tokens) {
                $tokens[] = $token['api_token'];
                return true;
            }))
            ->andReturn(true);

        $mock->shouldReceive('validate')
            ->once()
            ->with(Mockery::on(function ($token) use (&$tokens) {
                $tokens[] = $token['api_token'];
                return true;
            }))
            ->andReturn(false);


        app(RandomTokenGenerator::class)->generate();

        $this->assertCount(2, array_unique($tokens));
    }
}
