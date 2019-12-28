<?php

namespace Module\Auth\UseCases\SignIn;

class SignInCommand
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
    public $ip;

    /**
     * Command constructor.
     *
     * @param string $email
     * @param string $password
     * @param string $ip
     */
    public function __construct(string $email, string $password, string $ip)
    {
        $this->email = $email;
        $this->password = $password;
        $this->ip = $ip;
    }
}
