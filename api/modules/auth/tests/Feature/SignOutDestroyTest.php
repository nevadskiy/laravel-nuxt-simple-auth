<?php

namespace Module\Auth\Tests\Feature;

use Module\Auth\Models\User;
use Module\Auth\Tests\DatabaseTestCase;

class SignOutDestroyTest extends DatabaseTestCase
{
    use AuthRequests;

    /** @test */
    public function users_can_sign_out(): void
    {
        $user = factory(User::class)->create(['api_token' => 'TEST_API_TOKEN']);
        $this->be($user);

        $response = $this->signOutRequest();

        $response->assertNoContent();
        $this->assertEmpty($user->fresh()->api_token);
    }

    /** @test */
    public function guests_cannot_sign_out(): void
    {
        $this->signOutRequest()->assertUnauthorized();
    }
}
