<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
   git      dispatch(new \App\Jobs\GetBotUpdates());
        dispatch(new \App\Jobs\GetBotUpdates())->delay(10);
        dispatch(new \App\Jobs\GetBotUpdates())->delay(20);
        dispatch(new \App\Jobs\GetBotUpdates())->delay(30);
        dispatch(new \App\Jobs\GetBotUpdates())->delay(40);
        dispatch(new \App\Jobs\GetBotUpdates())->delay(50);
    }
}
