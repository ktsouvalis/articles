<?php

namespace App\Actions;

use App\Models\Article;
use App\Services\NYTMapper;
use App\Services\NewsFetcher;
use App\Services\NewsAPIMapper;
use App\Services\GuardianMapper;
use Lorisleiva\Actions\Concerns\AsAction;

class CallSources
{
    use AsAction;

    public function handle()
    {
        $mapped_data = [];
        $mapped_data[] = $this->fetchAndMap('content.guardianapis.com/search', ['api-key'=>env('GUARDIAN_API_KEY')], new GuardianMapper() );
        $mapped_data[] = $this->fetchAndMap('api.nytimes.com/svc/search/v2/articlesearch.json', ['api-key'=>env('NYTIMES_API_KEY')], new NYTMapper());
        $mapped_data[] = $this->fetchAndMap('newsapi.org/v2/everything', ['apiKey'=>env('NEWSAPI_API_KEY'), 'q'=>'BBC'], new NewsAPIMapper());

        StoreArticles::dispatch($mapped_data);
    }

    private function fetchAndMap($url, $headers, $mapper)
    {
        $fetcher = new NewsFetcher();
        $data = $fetcher->fetchArticles($url, $headers);
        
        return $mapper->mapData($data);
    }
    
    public function asJob()
    {
        return $this->handle();
    }
}
