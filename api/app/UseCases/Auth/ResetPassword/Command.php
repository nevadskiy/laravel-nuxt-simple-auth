<?php

namespace App\UseCases\Auth\ResetPassword;

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
     * @var string
     */
    public $token;

    /**
     * Command constructor.
     *
     * @param string $email
     * @param string $password
     * @param string $token
     */
    public function __construct(string $email, string $password, string $token)
    {
        $this->email = $email;
        $this->password = $password;
        $this->token = $token;
    }
}
