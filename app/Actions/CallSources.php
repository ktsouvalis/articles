<?php

namespace App\Actions;

use App\Models\Article;
use App\Services\NYTMapper;
use App\Services\NewsFetcher;
use App\Services\NewsAPIMapper;
use App\Services\GuardianMapper;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class CallSources
{
    use AsAction;

    public function handle()
    {
        $sources = [
            [
                'url' => 'api.nytimes.com/svc/search/v2/articlesearch.json',
                'params' => ['begin_date' => '2024-12-08'],
                'headers' => ['api-key' => env('NYTIMES_API_KEY')],
                'start_page' =>0,
                'mapper' => new NYTMapper()
            ],
            [
                'url' => 'content.guardianapis.com/search',
                'params' => ['from-date' => '2024-12-08'],
                'headers' => ['api-key' => env('GUARDIAN_API_KEY')],
                'start_page' =>1,
                'mapper' => new GuardianMapper()
            ],
            [
                'url' => 'newsapi.org/v2/everything',
                'params' => ['from' => '2024-12-07', 'q' => 'BBC'],
                'headers' => ['apiKey' => env('NEWSAPI_API_KEY')],
                'start_page' =>1,
                'mapper' => new NewsAPIMapper()
            ],
            // Add more sources here
            // [
            //     'url' => 'your-api-url',
            //     'params' => ['from-date' => 'your-date-for-initial-fetch'],
            //     'headers' => ['api-key' => env('your-api_API_KEY')],
            //     'start_page' => your-api-start-paging-number,
            //     'mapper' => new ExampleMapper()
            // ],
        ];

        $mapped_data = [];
        foreach ($sources as $source) {
            $query = http_build_query($source['params']);
            $url = "{$source['url']}?$query";
            $mapped_data[] = $this->fetchAndMap($url, $source['headers'], $source['mapper'], $source['start_page']);
        }

        $flattened_data = array_merge(...$mapped_data);

        if (empty($flattened_data)) {
            return;
        }
        StoreArticles::dispatch($flattened_data);
    }

    private function fetchAndMap($baseUrl, $headers, $mapper, $start_page)
    {
        $fetcher = new NewsFetcher($baseUrl, $headers, $start_page);
        $data = $fetcher->getData();
        if(empty($data)){
            return [];
        }
        return $mapper->mapData($data);
    }
    
    public function asJob()
    {
        return $this->handle();
    }
}
