<?php

namespace App\Console\Commands;

use App\Actions\CallSources as JobCallSources;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;

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
        try {
            JobCallSources::dispatch();
            $this->info('Job dispatched successfully.');
            Log::info('CallSources job dispatched successfully.');
            return 0;
        } catch (Exception $e) {
            $this->error('Failed to dispatch job.');
            Log::error('Failed to dispatch CallSources job: ' . $e->getMessage());
            return 1;
        }
    }
}
