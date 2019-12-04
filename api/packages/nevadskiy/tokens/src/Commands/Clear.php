<?php

namespace Nevadskiy\Tokens\Commands;

use Illuminate\Console\Command;
use Nevadskiy\Tokens\TokenEntity;

class Clear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired, used and soft deleted tokens from the database.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        TokenEntity::dead()->forceDelete();
    }
}
