<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Auth;
use Tests\DatabaseTestCase;

class UserIndexTest extends DatabaseTestCase
{
    // TODO: GUEST CANNOT DO THE SAME

    /** @test */
    public function users_can_request_information_about_their_account_with_api_token(): void
    {
        $user = $this->createUserWithCredentials('user@mail.com', 'secret123');

        $response = $this->getUser(
            $this->getApiTokenByCredentials('user@mail.com', 'secret123')
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

    /**
     * Request info about current user
     *
     * @param string $apiToken
     * @return TestResponse
     */
    private function getUser(string $apiToken): TestResponse
    {
        return $this->getJson(route('api.auth.user.index'), [
            'Authorization' => "Bearer {$apiToken}"
        ]);
    }

    /**
     * Get the API token by given credentials.
     *
     * @param string $email
     * @param string $password
     * @return string
     */
    private function getApiTokenByCredentials(string $email = 'user@mail.com', string $password = 'secret123'): string
    {
        $response = $this->postJson(route('api.auth.signin.store'), [
            'email' => $email,
            'password' => $password,
        ]);

        return $response->json('api_token');
    }
}
