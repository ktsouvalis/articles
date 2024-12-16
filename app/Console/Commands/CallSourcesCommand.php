<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Interfaces\SourceKeeper;
use Illuminate\Support\Facades\Log;
use App\Actions\CallSources;

class CallSourcesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:call-sources {sourceKeeper?}';

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
        if(!$this->argument('sourceKeeper')){
            $this->error('SourceKeeper argument is required.');
            return 1;
        }
        $sourceKeeperClass = 'App\\Services\\Keepers\\' . $this->argument('sourceKeeper');
        // Validate if the class exists and implements the required interface
        if(!class_exists($sourceKeeperClass)){
            $this->error('Class does not exist in app/Services/Keepers.');
            return 1;
        }
        if (!in_array(SourceKeeper::class, class_implements($sourceKeeperClass))) {
            $this->error('Keeper class must implement SourceKeeper interface.');
            return 1;
        }

        try {
            $sourceKeeper = new $sourceKeeperClass();
            $sources = $sourceKeeper->getSources();
            CallSources::dispatch($sources);
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
