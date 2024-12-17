<?php
namespace Tests\Feature;

use Mockery;
use Exception;
use Tests\TestCase;
use App\Models\Article;
use App\Actions\StoreArticles;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_articles()
    {
        $mappedData = [
            [
                'doc_id' => '1',
                'source' => 'nytimes',
                'published_at' => '2024-12-08',
                'author' => 'John Doe',
                'category' => 'Tech',
                'content' => ['title' => 'Test Article']
            ]
        ];

        $storeArticles = new StoreArticles();
        $storeArticles->handle('NYTimes',$mappedData);

        $this->assertDatabaseHas('articles', ['doc_id' => '1']);
    }

    public function test_handle_throws_exception_on_invalid_data()
    {
        $this->expectException(Exception::class);

        $storeArticles = new StoreArticles();
        $storeArticles->handle('NYTimes', [
            [
                'doc_id' => null, // Invalid data
                'published_at' => 'invalid-date', // Invalid date
                'author' => 'John Doe',
                'category' => 'Tech',
                'content' => ['title' => 'Test Article']
            ]
        ]);
    }
}