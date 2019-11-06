<?php

namespace Tests\Feature\Auth;

use Tests\DatabaseTestCase;
use Tests\Factory\UserFactory;

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

        $this->assertAuthenticatedAs($user);
        $response->assertOk();
        $response->assertExactJson([
            'data' => [
                'id' => $user->id,
                'email' => 'user@mail.com',
            ]
        ]);
    }

    /** @test */
    public function guests_cannot_request_any_information_without_api_token(): void
    {
        $response = $this->getUser('');

        $this->assertGuest();
        $response->assertUnauthorized();
    }

    /** @test */
    public function guests_cannot_request_any_information_with_wrong_api_token(): void
    {
        app(UserFactory::class)->withCredentials('user@mail.com', 'secret123')->create();

        $token = $this->getApiToken([
            'email' => 'user@mail.com',
            'password' => 'secret123',
        ]);

        $response = $this->getUser("INVALID{$token}");

        $this->assertGuest();
        $response->assertUnauthorized();
    }
}
