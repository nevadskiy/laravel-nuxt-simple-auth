<?php

namespace Module\Auth\UseCases\PasswordReset;

class PasswordResetCommand
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
    public function __construct(string $email, string $password, $token)
    {
        $this->email = $email;
        $this->password = $password;
        $this->token = $token;
    }
}
