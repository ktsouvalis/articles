<?php

namespace Tests\Actions;

use Tests\TestCase;
use App\Actions\CallSources;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use App\Jobs\StoreArticles;
use App\Services\Fetcher;
use App\Services\Mapper;
use Mockery;

class CallSourcesTest extends TestCase
{
    public function testHandleNoSources()
    {
        Log::shouldReceive('info')->once()->with('No sources found');
        
        $action = new CallSources();
        $action->handle([]);
    }

    public function testHandleWithEmptyData()
    {
        Queue::fake();

        $sources = [
            [
                'name' => 'NYTimes',
                'url' => 'api.nytimes.com/svc/search/v2/articlesearch.json',
                'params' => ['api-key' => 'fake-api-key', 'begin_date' => '2022-01-01', 'end_date' => '2022-01-02'],
                'start_page' => 0,
                'total_key' => 'response.meta.hits',
                'articles_key' => 'response.docs',
                'page_size' => 10,
                'fields' => [
                    'doc_id' => '_id',
                    'published_at' => 'pub_date',
                    'category' => 'section_name',
                    'author' => 'byline.original',
                    'title' => 'headline.main',
                    'url' => 'web_url',
                    'summary' => 'abstract',
                ]
            ]
        ];

        $fetcherMock = Mockery::mock(Fetcher::class);
        $fetcherMock->shouldReceive('getData')->andReturn([]);
        $this->app->instance(Fetcher::class, $fetcherMock);

        $action = new CallSources();
        $action->handle($sources);

        Queue::assertNotPushed(StoreArticles::class);
    }
}
