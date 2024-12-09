<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_articles_by_criteria()
    {
        Article::factory()->create(['author' => 'John Doe', 'category' => 'Tech', 'published_at' => '2024-12-08']);
        Article::factory()->create(['author' => 'Jane Smith', 'category' => 'Health', 'published_at' => '2024-12-09']);

        $response = $this->get('/api/articles?author=John');
        $response->assertStatus(200)->assertJsonCount(1, 'data');

        $response = $this->get('/api/articles?category=Health');
        $response->assertStatus(200)->assertJsonCount(1, 'data');

        $response = $this->get('/api/articles?published_at=2024-12-08');
        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_get_articles_no_match()
    {
        Article::factory()->create(['author' => 'John Doe', 'category' => 'Tech', 'published_at' => '2024-12-08']);
        Article::factory()->create(['author' => 'Jane Smith', 'category' => 'Health', 'published_at' => '2024-12-09']);

        $response = $this->get('/api/articles?author=NonExistentAuthor');
        $response->assertStatus(404)->assertJson(['message' => "No articles found matching the given criteria."]);

        $response = $this->get('/api/articles?category=NonExistentCategory');
        $response->assertStatus(404)->assertJson(['message' => "No articles found matching the given criteria."]);

        $response = $this->get('/api/articles?published_at=2025-01-01');
        $response->assertStatus(404)->assertJson(['message' => "No articles found matching the given criteria."]);
    }

    public function test_get_articles_endpoint_not_responding()
    {
        // Simulate endpoint not responding by disabling the route
        // This is just a placeholder, actual implementation may vary
        $this->withoutExceptionHandling();

        $response = $this->get('/api/articles');
        $response->assertStatus(404);
    }
}