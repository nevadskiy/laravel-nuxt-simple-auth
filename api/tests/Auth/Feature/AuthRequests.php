<?php

namespace Tests\Auth\Feature;

use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

/**
 * @mixin TestCase
 */
trait AuthRequests
{
    /**
     * Send a sign up request.
     *
     * @param array $overrides
     * @return TestResponse
     */
    public function signUpRequest(array $overrides = []): TestResponse
    {
        return $this->postJson(route('api.auth.signup.store'), array_merge([
            'email' => 'user@mail.com',
            'password' => 'secret123',
        ], $overrides));
    }

    /**
     * Send a sign in request.
     *
     * @param array $overrides
     * @return TestResponse
     */
    private function signInRequest(array $overrides = []): TestResponse
    {
        return $this->postJson(route('api.auth.signin.store'), array_merge([
            'email' => 'user@mail.com',
            'password' => 'secret123',
        ], $overrides));
    }

    /**
     * Sign out request.
     *
     * @return TestResponse
     */
    private function signOutRequest(): TestResponse
    {
        return $this->deleteJson(route('api.auth.signout.destroy'));
    }

    /**
     * Request info about the currently authenticated user.
     *
     * @param string $apiToken
     * @return TestResponse
     */
    private function getUserRequest(string $apiToken): TestResponse
    {
        return $this->getJson(route('api.auth.user.index'), [
            'Authorization' => "Bearer {$apiToken}"
        ]);
    }

    /**
     * Get the API token by credentials.
     *
     * @param array $credentials
     * @return string
     */
    private function apiTokenRequest(array $credentials): string
    {
        return $this->signInRequest($credentials)->json('api_token');
    }

    /**
     * Send a forgot password request.
     *
     * @param array $overrides
     * @return TestResponse
     */
    private function forgotPasswordRequest(array $overrides = []): TestResponse
    {
        return $this->postJson(route('api.auth.forgotten-password.store'), array_merge([
            'email' => 'user@mail.com'
        ], $overrides));
    }

    /**
     * Send a reset password request.
     *
     * @param array $overrides
     * @return TestResponse
     */
    private function resetPasswordRequest(array $overrides = []): TestResponse
    {
        return $this->putJson(route('api.auth.forgotten-password.update'), array_merge([
            'email' => 'user@mail.com',
            'password' => 'NEW_PASSWORD',
            'token' => 'RESET_PASSWORD_TOKEN',
        ], $overrides));
    }
}
