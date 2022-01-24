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
        dispatch(new GetBotUpdates())->delay(10);
        dispatch(new GetBotUpdates())->delay(20);
        dispatch(new GetBotUpdates())->delay(30);
        dispatch(new GetBotUpdates())->delay(40);
        dispatch(new GetBotUpdates())->delay(50);
    }
}
