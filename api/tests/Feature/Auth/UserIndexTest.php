<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Auth;
use Tests\DatabaseTestCase;
use Tests\Factory\UserFactory;

/**
 * TODO: guest cannot do the same
 */
class UserIndexTest extends DatabaseTestCase
{
    use AuthRequests;

    /** @test */
    public function users_can_request_information_about_their_account_with_api_token(): void
    {
        $user = app(UserFactory::class)->withCredentials('user@mail.com', 'secret123')->create();

        $response = $this->getUser(
            $this->getApiToken([
                'email' => 'user@mail.com',
                'password' => 'secret123',
            ])
        );

        Auth::user()->is($user);
        $response->assertOk();
        $response->assertExactJson([
            'data' => [
                'id' => $user->id,
                'email' => 'user@mail.com',
            ]
        ]);
    }
}
