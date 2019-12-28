<?php

namespace Module\Auth\Tests\Unit\UseCases\SignOut;

use Module\Auth\UseCases\SignOut\SignOutHandler;
use Module\Auth\Models\User;
use Module\Auth\Tests\DatabaseTestCase;

/**
 * @see SignOutHandler
 */
class SignOutHandlerTest extends DatabaseTestCase
{
    /** @test */
    public function it_clears_api_token_for_the_given_user(): void
    {
        $user = factory(User::class)->create([
            'email' => 'user@mail.com',
            'password' => 'database.password',
            'api_token' => 'TEST_API_TOKEN',
        ]);

        (new SignOutHandler)->handle($user);

        $this->assertEmpty($user->fresh()->api_token);
    }
}
