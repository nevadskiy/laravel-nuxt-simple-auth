<?php

namespace Nevadskiy\Tokens\Tests\Feature;

use Nevadskiy\Tokens\Tests\TestCase;

class BasicUsageTest extends TestCase
{
    /** @test */
    public function it_generates_tokens_for_models(): void
    {
        $user = factory(User::class)->create();

        $token = app(TokenManager::class)->generateFor($user);

        $this->assertEquals($token->tokenable->is($user));

        $this->assertDatabaseHas('tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
        ]);
    }
}
