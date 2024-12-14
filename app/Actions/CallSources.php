<?php

namespace App\Actions;

use Carbon\Carbon;
use App\Services\Mapper;
use App\Services\NewsFetcher;
use App\Services\SourceKeeper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class CallSources
{
    use AsAction;

    public function handle()
    {
        $keeper = new SourceKeeper();
        $sources = $keeper->getSources();

        foreach ($sources as $source) {
            $flattened_data = $this->fetchAndMapData($source);
            if (empty($flattened_data)) {
                Log::info('No new articles found in ' . $source['fields']['source']);
                continue;
            }
            
            StoreArticles::dispatch($flattened_data);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function asJob()
    {
        return $this->handle();
    }

    private function fetchAndMapData($source){
        $fetcher = new NewsFetcher($source);
        $data = $fetcher->getData();
        $mapper = new Mapper($source);
        $mapped_data[] = $mapper->mapData($data);
        return array_merge(...$mapped_data);
    }
}
