<?php

namespace Module\Auth\Tests\Unit\UseCases;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Module\Auth\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Module\Auth\Tests\DatabaseTestCase;
use Module\Auth\UseCases\PasswordReset\PasswordResetCommand;
use Module\Auth\UseCases\PasswordReset\PasswordResetHandler;
use Nevadskiy\Tokens\TokenManager;

/**
 * @see PasswordResetHandler
 */
class PasswordResetHandlerTest extends DatabaseTestCase
{
    /** @test */
    public function it_resets_password_for_user_with_token(): void
    {
        $user = factory(User::class)->create(['email' => 'user@mail.com']);

        $hasher = $this->mock(Hasher::class);
        $hasher->shouldReceive('make')->with('NEW_PASSWORD')->andReturn('HASH_NEW_PASSWORD');

        $this->mock(TokenManager::class)
            ->shouldReceive('useFor')
            ->andReturnUsing(function (string $token, string $type, User $u, callable $callback) use ($user) {
                $this->assertEquals('RESET_PASSWORD_TOKEN', $token);
                $this->assertEquals('password.reset', $type);
                $this->assertTrue($u->is($user));
                $callback($u, 'NEW_PASSWORD');
            });

        $command = new PasswordResetCommand('user@mail.com', 'NEW_PASSWORD', 'RESET_PASSWORD_TOKEN');

        app(PasswordResetHandler::class, ['hasher' => $hasher])->handle($command);

        $this->assertEquals('HASH_NEW_PASSWORD', $user->fresh()->password);
        $this->assertNull($user->fresh()->api_token);
    }

    /** @test */
    public function it_throws_an_exception_if_reset_returns_different_result(): void
    {
        $manager = $this->spy(TokenManager::class);

        try {
            app(PasswordResetHandler::class)->handle(new PasswordResetCommand('user@mail.com', 'NEW_PASSWORD', 'RESET_PASSWORD_TOKEN'));
            $this->fail('Reset password handler handled command when should not.');
        } catch (ModelNotFoundException $e) {
            $manager->shouldNotHaveReceived('useFor');
        }
    }
}
