<?php

namespace App\UseCases\Auth\ForgottenPassword;

class Command
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
