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
        if(empty($sources)){
            Log::info('No sources found');
            return;
        }
        foreach ($sources as $source) {
            $flattenedData = $this->fetchAndMapData($source);
            if (empty($flattenedData)) {
                Log::info('No new articles found in ' . $source['name']);
                continue;
            }
            
            StoreArticles::dispatch($source['name'], $flattenedData);
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
        if (empty($data)) {
            return [];
        }
        $mapper = new Mapper($source);
        $mappedData[] = $mapper->mapData($data);
        return array_merge(...$mappedData);
    }
}
