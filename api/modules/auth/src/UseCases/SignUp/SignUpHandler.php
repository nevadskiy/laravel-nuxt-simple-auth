<?php

namespace Module\Auth\UseCases\SignUp;

use Module\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Hashing\Hasher;

class SignUpHandler
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
     * @param SignUpCommand $command
     * @return User
     */
    public function handle(SignUpCommand $command): User
    {
        $user = $this->createUser($command);

        event(new Registered($user));

        return $user;
    }

    /**
     * Create a user.
     *
     * @param SignUpCommand $command
     * @return User
     */
    protected function createUser(SignUpCommand $command): User
    {
        $user = new User([
            'email' => $command->email,
            'password' => $this->hasher->make($command->password),
        ]);

        $user->save();

        return $user;
    }
}
