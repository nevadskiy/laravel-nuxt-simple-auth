<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Module\Auth\UseCases\SignUp\SignUpCommand;
use Module\Auth\UseCases\SignUp\SignUpHandler;

class UserCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user';

    /**
     * Execute the console command.
     *
     * @param SignUpHandler $handler
     */
    public function handle(SignUpHandler $handler): void
    {
        $handler->handle(
            new SignUpCommand(
                $this->ask('Enter an email'),
                $this->secret('Enter a password')
            )
        );
    }
}
