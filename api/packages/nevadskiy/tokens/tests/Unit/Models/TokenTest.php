<?php

namespace Nevadskiy\Tokens\Tests\Unit\Models;

use DateTimeInterface;
use Nevadskiy\Tokens\Tests\Support\Models\User;
use Nevadskiy\Tokens\Tests\TestCase;
use Nevadskiy\Tokens\Token;

/**
 * @see Token
 */
class TokenTest extends TestCase
{
    /** @test */
    public function it_has_tokenable_relation(): void
    {
        $user = factory(User::class)->create();

        $token = factory(Token::class)->create([
            'tokenable_type' => get_class($user),
            'tokenable_id' => $user->id,
        ]);

        $this->assertTrue($token->tokenable->is($user));
    }

    /** @test */
    public function it_can_fill_tokenable_attributes_for_models(): void
    {
        $user = factory(User::class)->create();

        $token = factory(Token::class)->make();
        $token->fillTokenable($user);
        $token->save();

        $this->assertTrue($token->tokenable->is($user));
    }

    /** @test */
    public function it_has_used_at_timestamp(): void
    {
        $now = $this->freezeTime();

        $token = factory(Token::class)->create(['used_at' => now()]);

        $this->assertInstanceOf(DateTimeInterface::class, $token->used_at);
        $this->assertEquals($now, $token->used_at);
    }

    /** @test */
    public function it_has_expired_at_timestamp(): void
    {
        $now = $this->freezeTime();

        $token = factory(Token::class)->create(['expired_at' => now()]);

        $this->assertInstanceOf(DateTimeInterface::class, $token->expired_at);
        $this->assertEquals($now, $token->expired_at);
    }

    /** @test */
    public function it_can_be_marked_as_used(): void
    {
        $now = $this->freezeTime();

        $token = factory(Token::class)->create(['used_at' => null]);

        $this->assertNull($token->used_at);

        $token->markAsUsed();

        $this->assertEquals($now, $token->fresh()->used_at);
    }

    /** @test */
    public function it_can_be_used_as_string(): void
    {
        $token = factory(Token::class)->make(['token' => 'TEST_TOKEN']);

        $this->assertEquals('TEST_TOKEN', $token);
    }

    /** @test */
    public function it_knows_if_it_is_expired(): void
    {
        $activeToken = factory(Token::class)->make(['expired_at' => now()->addMinute()]);
        $expiredToken = factory(Token::class)->make(['expired_at' => now()->subMinute()]);

        $this->assertFalse($activeToken->isExpired());
        $this->assertTrue($expiredToken->isExpired());
    }

    /** @test */
    public function it_knows_if_it_is_already_used(): void
    {
        $activeToken = factory(Token::class)->make(['used_at' => null]);
        $usedToken = factory(Token::class)->make(['used_at' => now()->subMinute()]);

        $this->assertFalse($activeToken->isUsed());
        $this->assertTrue($usedToken->isUsed());
    }

    /** @test */
    public function it_has_active_scope(): void
    {
        $used = $this->tokenFactory()->ofType('verification')->used()->create();
        $active = $this->tokenFactory()->ofType('verification')->create();
        $expired = $this->tokenFactory()->ofType('verification')->expired()->create();
        $usedExpired = $this->tokenFactory()->ofType('password')->used()->expired()->create();

        $tokens = Token::active()->get();

        $this->assertCount(1, $tokens);
        $this->assertTrue($tokens[0]->is($active));
    }

    /** @test */
    public function it_can_be_continued_to_the_given_date(): void
    {
        $this->freezeTime();

        $token = factory(Token::class)->create(['expired_at' => now()->addMinute()]);

        $this->assertEquals(now()->addMinute(), $token->expired_at);

        $token->continueTo(now()->addMonth());

        $this->assertEquals(now()->addMonth(), $token->fresh()->expired_at);
    }
}
