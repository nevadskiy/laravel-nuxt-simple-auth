<?php

namespace App\UseCases\Auth\SignUp;

use App\Services\Auth\PasswordHasherInterface;
use App\User;
use Illuminate\Auth\Events\Registered;

class Handler
{
    /**
     * @var PasswordHasherInterface
     */
    private $hasher;

    /**
     * Handler constructor.
     *
     * @param PasswordHasherInterface $hasher
     */
    public function __construct(PasswordHasherInterface $hasher)
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
            'password' => $this->hasher->hash($command->password),
        ]);

        $user->save();

        event(new Registered($user));

        return $user;
    }
}
