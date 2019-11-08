<?php

namespace App\UseCases\Auth\ForgottenPassword;

use DomainException;
use Illuminate\Contracts\Auth\PasswordBroker;

class Handler
{
    /**
     * @var PasswordBroker
     */
    private $broker;

    /**
     * Handler constructor.
     *
     * @param PasswordBroker $broker
     */
    public function __construct(PasswordBroker $broker)
    {
        $this->broker = $broker;
    }

    /**
     * Handle the sign up use case.
     *
     * @param Command $command
     * @return void
     */
    public function handle(Command $command): void
    {
        $result = $this->broker->sendResetLink((array) $command);

        if (PasswordBroker::RESET_LINK_SENT !== $result) {
            throw new DomainException(__($result));
        }
    }
}
