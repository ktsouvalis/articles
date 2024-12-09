<?php

namespace App\Console\Commands;

use App\Actions\CallSources as JobCallSources;
use Illuminate\Console\Command;

class CallSources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:call-sources';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches the job app/Actions/CallSources.php';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        JobCallSources::dispatch();
    }
}
