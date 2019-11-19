<?php

namespace App\Auth\UseCases\SignIn;

class Command
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * Command constructor.
     *
     * @param string $email
     * @param string $password
     */
    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }
}
