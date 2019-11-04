<?php

namespace Tests\Feature\Auth;

use App\Services\Auth\PasswordHasherInterface;
use App\UseCases\Auth\SignUp\Command;
use App\UseCases\Auth\SignUp\Handler;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Tests\DatabaseTestCase;

class HandlerTest extends DatabaseTestCase
{
    /** @test */
    public function it_creates_users_with_correct_passwords(): void
    {
        $this->mock(PasswordHasherInterface::class)
            ->shouldReceive('hash')
            ->once()
            ->with('secret')
            ->andReturn('secret');

        $command = new Command('guest@mail.com', 'secret');

        $user = app(Handler::class)->handle($command);

        $this->assertCount(1, User::all());
        $this->assertEquals('guest@mail.com', $user->email);
        $this->assertEquals('secret', $user->password);
    }

    /** @test */
    public function it_fires_registered_event(): void
    {
        Event::fake(Registered::class);

        $command = new Command('guest@mail.com', 'secret');

        $user = app(Handler::class)->handle($command);

        Event::assertDispatched(Registered::class, 1);
        Event::assertDispatched(Registered::class, function (Registered $event) use ($user) {
            return $event->user->is($user);
        });
    }
}
