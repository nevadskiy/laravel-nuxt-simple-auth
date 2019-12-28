<?php

namespace Module\Auth\UseCases\PasswordForgot;

class PasswordForgotCommand
{
    /**
     * @var string
     */
    public $email;

    /**
     * Command constructor.
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }
}
