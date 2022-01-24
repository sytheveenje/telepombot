<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\GetBotUpdates;

class DispatchGetBotUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        GetBotUpdates::dispatch();
        GetBotUpdates::dispatch()->delay(10);
        GetBotUpdates::dispatch()->delay(20);
        GetBotUpdates::dispatch()->delay(30);
        GetBotUpdates::dispatch()->delay(40);
        GetBotUpdates::dispatch()->delay(50);
    }
}
