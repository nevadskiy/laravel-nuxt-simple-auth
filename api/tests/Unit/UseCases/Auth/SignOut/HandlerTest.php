<?php

namespace Tests\Feature\Auth\SignOut;

use App\UseCases\Auth\SignOut\Handler;
use App\User;
use Tests\DatabaseTestCase;

/**
 * @see Handler
 */
class HandlerTest extends DatabaseTestCase
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
