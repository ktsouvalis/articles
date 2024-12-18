<?php

namespace Tests\Unit;

use Tests\TestCase;
use ReflectionClass;
use App\Services\Fetcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FetcherTest extends TestCase
{
    public function test_fetch_articles_success()
    {
        Http::fake([
            '*' => Http::response([
                'response' => [
                    'meta' => [
                        'hits' => 140,
                        'offset' => 0,
                        'time' => 0
                    ],
                    'docs' => [
                        // ...mocked article data...
                    ]
                ]
            ], 200)
        ]);

        $source = [
            'url' => 'api.nytimes.com/svc/search/v2/articlesearch.json',
            'params' => [
                'api-key' => 'test-api-key',
                'begin_date' => '2024-12-16',
                'end_date' => '2024-12-17'
            ],
            'total_key' => 'response.meta.hits',
            'page_size' => 10
        ];

        $fetcher = new Fetcher($source);

        $this->assertNotEmpty($fetcher->getData());
        $this->assertCount(14, $fetcher->getData());
    }

    public function test_fetch_articles_error()
    {
        Http::fake([
            '*' => Http::response([], 429)
        ]);

        Log::shouldReceive('info')
            ->once()
            ->with('Try to fetch from api.nytimes.com/svc/search/v2/articlesearch.json?api-key=test-api-key&begin_date=2024-12-16&end_date=2024-12-17&page=1');

        Log::shouldReceive('error')
            ->once()
            ->with('Error 429: []');

        $source = [
            'url' => 'api.nytimes.com/svc/search/v2/articlesearch.json',
            'params' => [
                'api-key' => 'test-api-key',
                'begin_date' => '2024-12-16',
                'end_date' => '2024-12-17'
            ],
            'total_key' => 'response.meta.hits',
            'page_size' => 10
        ];

        $fetcher = new Fetcher($source);

        $this->assertEmpty($fetcher->getData());
    }

    public function test_initialize_pagination()
    {
        $fetcher = new Fetcher([
            'url' => 'api.nytimes.com/svc/search/v2/articlesearch.json',
            'params' => [],
            'total_key' => 'response.meta.hits',
            'page_size' => 10
        ]);

        $data = [
            'response' => [
                'meta' => [
                    'hits' => 140
                ]
            ]
        ];

        $reflection = new ReflectionClass($fetcher);
        $method = $reflection->getMethod('initializePagination');
        $method->setAccessible(true);
        $totalPages = $method->invokeArgs($fetcher, [$data]);

        $this->assertEquals(14, $totalPages);
    }

    public function test_retrieve_results_number_from_response()
    {
        $fetcher = new Fetcher([
            'url' => 'api.nytimes.com/svc/search/v2/articlesearch.json',
            'params' => [],
            'total_key' => 'response.meta.hits',
            'page_size' => 10
        ]);

        $data = [
            'response' => [
                'meta' => [
                    'hits' => 140
                ]
            ]
        ];

        $reflection = new ReflectionClass($fetcher);
        $method = $reflection->getMethod('retreiveResultsNumberFromResponse');
        $method->setAccessible(true);
        $resultsNumber = $method->invokeArgs($fetcher, [$data]);
       
        $this->assertEquals(140, $resultsNumber);
    }

    public function test_calculate_pages()
    {
        $fetcher = new Fetcher([
            'url' => 'api.nytimes.com/svc/search/v2/articlesearch.json',
            'params' => [],
            'total_key' => 'response.meta.hits',
            'page_size' => 10
        ]);

        $reflection = new ReflectionClass($fetcher);
        $method = $reflection->getMethod('calculatePages');
        $method->setAccessible(true);
        $pages = $method->invokeArgs($fetcher, [140]);
        
        $this->assertEquals(14, $pages);
    }

    public function test_mistake_in_key(){
        $fetcher = new Fetcher([
            'url' => 'api.nytimes.com/svc/search/v2/articlesearch.json',
            'params' => [],
            'total_key' => 'response.meta.mistake',
            'page_size' => 10
        ]);

        $data = [
            'response' => [
                'meta' => [
                    'hits' => 140
                ]
            ]
        ];

        $reflection = new ReflectionClass($fetcher);
        $method = $reflection->getMethod('retreiveResultsNumberFromResponse');
        $method->setAccessible(true);
        $resultsNumber = $method->invokeArgs($fetcher, [$data]);
       
        $this->assertEquals(0, $resultsNumber);
    }
}
