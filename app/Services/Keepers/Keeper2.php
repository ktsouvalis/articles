<?php

namespace App\Services\Keepers;

use Carbon\Carbon;
use App\Interfaces\SourceKeeper;
use App\Services\LastCall;

class Keeper2 implements SourceKeeper{
    private $sources = [];
    private $lastCallService;

    public function __construct() {
        $this->lastCallService = new LastCall();
        $this->sources = [
            [
                //TO USE THIS SOURCE:
                // 1. Download the latest CA certificate bundle:

                //     - Download the cacert.pem file from https://curl.se/ca/cacert.pem.
                //     - Configure PHP to use the downloaded certificate bundle:
                    
                // 2. Open your php.ini file.
                //     - Find the line with curl.cainfo and openssl.cafile.
                //     - Set them to the path of the downloaded cacert.pem file.
                // 3. Have in mind that the free plan returns 30 latest articles, that's why it is in a seperate keeper
                // which is configured to be called hourly()
                'name' => 'Currents',
                'url' => env('CURRENTS_API_URL'),
                'params' => ['apiKey'=>env('CURRENTS_API_KEY')],
                'headers'=>null,
                'start_page' => 1,
                'total_key' => null,
                'articles_key' => 'news',
                'page_size' => 30,
                'fields' => [
                    'doc_id' => 'id',
                    'published_at' => 'published',
                    'category' => 'category.0',
                    'author' => 'author',
                    'title' => 'title',
                    'url' => 'url',
                    'summary' => 'description',
                ]
            ]
            // Add more sources here
            // [
            //     'name' => 'your_source_api_chosen_name',
            //     'url' => env('your_source_api_url'),
            //     'params' => ['apiKey' => env('your_source_api_key'),'from' => $this->lastCallService->getLastCall('your_source_api_chosen_name'), 'to' => Carbon::now()->toDateString()],
            //     'headers'=>['Authorization' => 'Bearer '.env(your_source_api_key)], //if needed inside the headers
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