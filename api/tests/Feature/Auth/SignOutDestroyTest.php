<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Http\Response;
use Tests\DatabaseTestCase;

class SignOutDestroyTest extends DatabaseTestCase
{
    use AuthRequests;

    /** @test */
    public function users_can_sign_out(): void
    {
        $user = factory(User::class)->create(['api_token' => 'TEST_API_TOKEN']);
        $this->be($user);

        $response = $this->signOutRequest();

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertEmpty($user->fresh()->api_token);
    }

    /** @test */
    public function guests_cannot_sign_out(): void
    {
        $this->signOutRequest()->assertUnauthorized();
    }
}
