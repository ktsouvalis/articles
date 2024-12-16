<?php

namespace App\Services;

use Carbon\Carbon;

class SourceKeeper {
    private $sources = [];
    private $lastCallService;

    public function __construct() {
        $this->lastCallService = new LastCallService();
        $this->setSources();
    }

    private function setSources() {
        $this->sources = [
            [
                'url' => env('NYTIMES_API_URL'),
                'params' => ['api-key' => env('NYTIMES_API_KEY'),'begin_date' => $this->lastCallService->getLastCall('NYTimes'), 'end_date' => Carbon::now()->toDateString()],
                'start_page' => 0,
                'total_key' => 'response.meta.hit',
                'articles_key' => 'response.doc',
                'page_size' => 10,
                'fields' => [
                    'doc_id' => '_id',
                    'published_at' => 'pub_date',
                    'category' => 'section_name',
                    'author' => 'byline.original',
                    'title' => 'headline.main',
                    'url' => 'web_url',
                    'summary' => 'abstract',
                    'source' => 'NYTimes'
                ]
            ],
            [
                'url' => env('GUARDIAN_API_URL'),
                'params' => ['api-key' => env('GUARDIAN_API_KEY'),'from-date' => $this->lastCallService->getLastCall('Guardian'), 'to-date' => Carbon::now()->toDateString()],
                'start_page' => 1,
                'total_key' => 'response.total',
                'articles_key' => 'response.results',
                'page_size' => 10,
                'fields' => [
                    'doc_id' => 'id',
                    'published_at' => 'webPublicationDate',
                    'category' => 'sectionId',
                    'author' => null,
                    'title' => 'webTitle',
                    'url' => 'webUrl',
                    'summary' => 'webTitle',
                    'source' => 'Guardian'
                ]
            ],
            [
                'url' => env('NEWSAPI_API_URL'),
                'params' => ['apiKey' => env('NEWSAPI_API_KEY'),'from' => $this->lastCallService->getLastCall('NewsAPI'), 'to' => Carbon::now()->toDateString(), 'q' => 'BBC'],
                'start_page' => 1,
                'total_key' => 'totalResults',
                'articles_key' => 'articles',
                'page_size' => 100,
                'fields' => [
                    'doc_id' => 'url',
                    'published_at' => 'publishedAt',
                    'category' => 'title',
                    'author' => 'author',
                    'title' => 'title',
                    'url' => 'url',
                    'summary' => 'description',
                    'source' => 'NewsAPI'
                ]
            ],
            // Add more sources here
            // [
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
            //         'source' => 'your_source_api_chosen_name'
            //     ]
            // ],
        ];
    }

    public function getSources() {
        return $this->sources;
    }
}