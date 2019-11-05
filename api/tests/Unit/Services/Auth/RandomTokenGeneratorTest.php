<?php

namespace Tests\Unit\Services\Auth;

use App\Services\Auth\RandomTokenGenerator;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Str;
use Mockery;
use Tests\DatabaseTestCase;

class HandlerTest extends DatabaseTestCase
{
    /** @test */
    public function it_generates_long_api_tokens(): void
    {
        $generator = app(RandomTokenGenerator::class);

        $this->assertEquals(128, Str::length($generator->generate()));
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
