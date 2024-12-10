<?php

namespace App\Actions;

use Carbon\Carbon;
use App\Services\NYTMapper;
use App\Services\NewsFetcher;
use App\Services\SourceKeeper;
use App\Services\NewsAPIMapper;
use App\Services\GuardianMapper;
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
                Log::info('No new articles found in ' . $source['name']);
                continue;
            }

            StoreArticles::dispatch($flattened_data, $source['name']);
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
        $query = http_build_query($source['params']);
        $url = "{$source['url']}?$query";
        
        $fetcher = new NewsFetcher($url, $source['headers'], $source['start_page'], $source['total_key'], $source['page_size']);
        $data = $fetcher->getData();
        $mapped_data[] = $source['mapper']->mapData($data);
        
        return array_merge(...$mapped_data);
    }
}
