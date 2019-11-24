<?php

namespace Nevadskiy\Tokens\Tests\Unit\Repository;

use Nevadskiy\Tokens\Exceptions\TokenNotFoundException;
use Nevadskiy\Tokens\Repository\TokenRepository;
use Nevadskiy\Tokens\Tests\TestCase;
use Nevadskiy\Tokens\Token;

/**
 * @see TokenRepository
 */
class TokenRepositoryTest extends TestCase
{
    /** @test */
    public function it_returns_token_model_by_token_string_and_type(): void
    {
        $token = factory(Token::class)->create(['token' => 'SECRET_TOKEN', 'type' => 'test.type']);

        $result = app(TokenRepository::class)->getByTokenType('SECRET_TOKEN', 'test.type');

        $this->assertTrue($token->is($result));
    }

    /** @test */
    public function it_returns_the_latest_token(): void
    {
        $token = factory(Token::class)->create(['token' => 'SECRET_TOKEN', 'type' => 'test.type']);
        $latestToken = factory(Token::class)->create(['token' => 'SECRET_TOKEN', 'type' => 'test.type']);

        $result = app(TokenRepository::class)->getByTokenType('SECRET_TOKEN', 'test.type');

        $this->assertTrue($latestToken->is($result));
    }

    /** @test */
    public function it_throws_an_exception_if_token_is_not_found(): void
    {
        $this->expectException(TokenNotFoundException::class);
        app(TokenRepository::class)->getByTokenType('SECRET_TOKEN', 'test.type');
    }

    /** @test */
    public function it_find_tokens_correctly(): void
    {
        $token1 = factory(Token::class)->create(['token' => 'SECRET_TOKEN', 'type' => 'password.reset']);
        $token2 = factory(Token::class)->create(['token' => 'SECRET_TOKEN', 'type' => 'magic.link']);
        $token3 = factory(Token::class)->create(['token' => 'ANOTHER_TOKEN', 'type' => 'password.reset']);

        $result = app(TokenRepository::class)->getByTokenType('SECRET_TOKEN', 'magic.link');

        $this->assertTrue($token2->is($result));
    }

    /** @test */
    public function it_does_not_find_token_by_wrong_type(): void
    {
        factory(Token::class)->create(['token' => 'SECRET_TOKEN', 'type' => 'password.reset']);

        $this->expectException(TokenNotFoundException::class);

        app(TokenRepository::class)->getByTokenType('SECRET_TOKEN', 'magic.link');
    }

    /** @test */
    public function it_finds_active_tokens_for_given_models(): void
    {
        $user = $this->createTokenableEntity();

        $tokenForDifferentModel = $this->tokenFactory()->ofType('verification')->create();
        $activeToken = $this->tokenFactory()->ofType('verification')->for($user)->create();
        $expiredToken = $this->tokenFactory()->ofType('verification')->for($user)->expired()->create();
        $usedToken = $this->tokenFactory()->ofType('verification')->for($user)->used()->create();
        $anotherToken = $this->tokenFactory()->ofType('password')->for($user)->create();

        $token =  app(TokenRepository::class)->findActiveByTypeFor($user, 'verification');

        $this->assertTrue($token->is($activeToken));
    }

    /** @test */
    public function it_returns_null_if_active_token_is_not_found_for_given_model(): void
    {
        $user = $this->createTokenableEntity();

        $this->tokenFactory()->ofType('password')->for($user)->create();
        $this->tokenFactory()->ofType('verification')->for($user)->expired()->create();
        $this->tokenFactory()->ofType('verification')->for($user)->used()->create();
        $this->tokenFactory()->ofType('verification')->create();

        $this->assertNull(app(TokenRepository::class)->findActiveByTypeFor($user, 'verification'));
    }
}
