<?php

namespace Tests\Factory;

use App\User;
use Illuminate\Contracts\Hashing\Hasher;

class UserFactory
{
    /**
     * @var string
     */
    private $email = 'user@mail.com';

    /**
     * @var string
     */
    private $password = 'secret123';

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * UserFactory constructor.
     *
     * @param Hasher $hasher
     */
    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Set user credentials
     *
     * @param string $email
     * @param string $password
     * @return $this
     */
    public function withCredentials(string $email, string $password = null): self
    {
        $this->email = $email;
        $this->password = $password ?: $this->password;

        return $this;
    }

    /**
     * Create the user.
     *
     * @return User
     */
    public function create(): User
    {
        return factory(User::class)->create([
            'email' => $this->email,
            'password' => $this->hasher->make($this->password),
        ]);
    }
}
