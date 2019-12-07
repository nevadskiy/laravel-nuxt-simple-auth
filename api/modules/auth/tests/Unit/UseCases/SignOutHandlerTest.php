<?php

namespace Module\Auth\Tests\Unit\UseCases\SignOut;

use Module\Auth\UseCases\SignOut\Handler;
use Module\Auth\Models\User;
use Module\Auth\Tests\DatabaseTestCase;

/**
 * @see Handler
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

        (new Handler)->handle($user);

        $this->assertEmpty($user->fresh()->api_token);
    }
}
