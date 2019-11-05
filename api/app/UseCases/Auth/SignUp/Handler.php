<?php

namespace App\UseCases\Auth\SignUp;

use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Hashing\Hasher;

class Handler
{
    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * Handler constructor.
     *
     * @param Hasher $hasher
     */
    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Handle the sign up use case.
     *
     * @param Command $command
     * @return User
     */
    public function handle(Command $command): User
    {
        $user = new User([
            'email' => $command->email,
            'password' => $this->hasher->make($command->password),
        ]);

        $user->save();

        event(new Registered($user));

        return $user;
    }
}
