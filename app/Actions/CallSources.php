<?php

namespace App\Actions;

use App\Models\Article;
use App\Services\NYTMapper;
use App\Services\NewsFetcher;
use App\Services\NewsAPIMapper;
use App\Services\GuardianMapper;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CallSources
{
    use AsAction;

    public function handle()
    {
        $sources = [
            [
                'name' => 'NYTimes',
                'url' => env('NYTIMES_API_URL'),
                'params' => ['begin_date' => $this->getLastCall('NYTimes'), 'end_date' => Carbon::now()->toDateString()],
                'headers' => ['api-key' => env('NYTIMES_API_KEY')],
                'start_page' => 0,
                'mapper' => new NYTMapper()
            ],
            [
                'name' => 'Guardian',
                'url' => env('GUARDIAN_API_URL'),
                'params' => ['from-date' => $this->getLastCall('Guardian'), 'to-date' => Carbon::now()->toDateString()],
                'headers' => ['api-key' => env('GUARDIAN_API_KEY')],
                'start_page' => 1,
                'mapper' => new GuardianMapper()
            ],
            [
                'name' => 'NewsAPI',
                'url' => env('NEWSAPI_API_URL'),
                'params' => ['from' => $this->getLastCall('NewsAPI'), 'to' => Carbon::now()->toDateString(), 'q' => 'BBC'],
                'headers' => ['apiKey' => env('NEWSAPI_API_KEY')],
                'start_page' => 1,
                'mapper' => new NewsAPIMapper()
            ],

            // Add more sources here
            // [
            //     'name' => 'your-api-name',
            //     'url' => env('your-api-url'),
            //     'params' => ['your-api-source-from-date-key' => $this->getLastCall('your-api-name'),'your-api-source-to-date-key' => Carbon::now()->toDateString()],
            //     'headers' => ['api-key' => env('your-api_API_KEY')],
            //     'start_page' => your-api-start-paging-number,
            //     'mapper' => new ExampleMapper()
            // ],
        ];

        
        foreach ($sources as $source) {
            $mapped_data = [];
            $query = http_build_query($source['params']);
            $url = "{$source['url']}?$query";
            $mapped_data[] = $this->fetchAndMap($url, $source['headers'], $source['mapper'], $source['start_page']);
        

            $flattened_data = array_merge(...$mapped_data);

            if (empty($flattened_data)) {
                Log::info('No new articles found in ' . $source['name']);
                continue;
            }

            StoreArticles::dispatch($flattened_data, $source['name']);
        }
        // exit;
    }

    private function getLastCall($sourceName)
    {
        $lastCall = DB::table('last_news')->where('name', $sourceName)->value('last_call');
        return $lastCall ? Carbon::parse($lastCall)->toDateString() : Carbon::now()->subDay()->toDateString();
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
