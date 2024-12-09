<?php

namespace App\Services;

use Carbon\Carbon;

class SourceKeeper{
    private $sources = [];
    private $lastCallService;

    public function __construct(){
        $this->lastCallService = new LastCallService();
        $this->setSources();
    }

    public function getSources(){
        return $this->sources;
    }

    private function setSources(){
        $this->sources = [
            [
                'name' => 'NYTimes',
                'url' => env('NYTIMES_API_URL'),
                'params' => ['begin_date' => $this->lastCallService->getLastCall('NYTimes'), 'end_date' => Carbon::now()->toDateString()],
                'headers' => ['api-key' => env('NYTIMES_API_KEY')],
                'start_page' => 0,
                'total_key' => 'response.meta.hits',
                'page_size' => 10,
                'mapper' => new NYTMapper()
            ],
            [
                'name' => 'Guardian',
                'url' => env('GUARDIAN_API_URL'),
                'params' => ['from-date' => $this->lastCallService->getLastCall('Guardian'), 'to-date' => Carbon::now()->toDateString()],
                'headers' => ['api-key' => env('GUARDIAN_API_KEY')],
                'start_page' => 1,
                'total_key' => 'response.total',
                'page_size' => 10,
                'mapper' => new GuardianMapper()
            ],
            [
                'name' => 'NewsAPI',
                'url' => env('NEWSAPI_API_URL'),
                'params' => ['from' => $this->lastCallService->getLastCall('NewsAPI'), 'to' => Carbon::now()->toDateString(), 'q' => 'BBC'],
                'headers' => ['apiKey' => env('NEWSAPI_API_KEY')],
                'start_page' => 1,
                'total_key' => 'totalResults',
                'page_size' => 100,
                'mapper' => new NewsAPIMapper()
            ],

            // Add more sources here
            // [
            //     'name' => 'ExampleNews',
            //     'url' => env('EXAMPLENEWS_API_URL'),
            //     'params' => ['start_date' => $this->getLastCall('ExampleNews'), 'end_date' => Carbon::now()->toDateString()],
            //     'headers' => ['your-source-needed-headers' => 'your-source-headers-value'],
            //     'start_page' => 'your-source-api-response-start-page',
            //     'total_key' => 'number: your-source-api-response-total-key',
            //     'page_size' => 'number: your-source-api-response-page-size',
            //     'mapper' => new ExampleMapper()
            // ]
        ];
    }
}