<?php

namespace App\Services\Keepers;

use Carbon\Carbon;
use App\Interfaces\SourceKeeper;
use App\Services\LastCall;

class ExampleKeeper implements SourceKeeper{
    private $sources = [];
    private $lastCallService;

    public function __construct() {
        // $this->lastCallService = new LastCall(); // Uncomment this line if you need the LastCall service
        $this->sources = [
            // Add more sources here
            // [
            //     'name' => 'your_source_api_chosen_name',
            //     'url' => env('your_source_api_url'),
            //     'params' => ['apiKey' => env('your_source_api_key'),'from' => $this->lastCallService->getLastCall('your_source_api_chosen_name'), 'to' => Carbon::now()->toDateString()],
            //     'start_page' => your_source_api_start_page, //some sources start from page 0, others from page 1
            //     'total_key' => 'your_source_api_response_total_results_key',
            //     'articles_key' => 'your_source_api_response_articles_key',
            //     'page_size' => your_source_api_page_size,
            //     'fields' => [
            //         'doc_id' => 'your_source_api_response_unique_article_identfier_key',
            //         'published_at' => 'your_source_api_response_article_date_of_publication_key',
            //         'category' => 'your_source_api_response_article_category_key',
            //         'author' => 'your_source_api_response_article_author_key',
            //         'title' => 'your_source_api_response_article_title_key',
            //         'url' => 'your_source_api_response_article_url_key',
            //         'summary' => 'your_source_api_response_article_description_key',
            //     ]
            // ],
        ];
    }

    public function getSources() {
        return $this->sources;
    }
}