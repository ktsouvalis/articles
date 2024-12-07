<?php

namespace App\Actions;

use App\Models\Article;
use App\Services\NYTMapper;
use App\Services\NewsFetcher;
use App\Services\GuardianMapper;
use Lorisleiva\Actions\Concerns\AsAction;

class CallSources
{
    use AsAction;

    public function handle()
    {
        $mapped_data = [];
        $fetcher = new NewsFetcher();

        $mapped_data[] = $this->fetchAndMap($fetcher, 'content.guardianapis.com/search', env('GUARDIAN_API_KEY'), new GuardianMapper());
        $mapped_data[] = $this->fetchAndMap($fetcher, 'api.nytimes.com/svc/search/v2/articlesearch.json', env('NYTIMES_API_KEY'), new NYTMapper());

        StoreArticles::dispatch($mapped_data);
    }

    private function fetchAndMap($fetcher, $url, $apiKey, $mapper)
    {
        $data = $fetcher->fetchArticles($url, $apiKey);
        return $mapper->mapData($data);
    }
    
    public function asJob()
    {
        return $this->handle();
    }
}
