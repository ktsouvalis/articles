<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use App\Actions\StoreArticles;
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
}