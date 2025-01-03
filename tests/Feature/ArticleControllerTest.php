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

    public function test_get_articles_by_published_at_range()
    {
        Article::factory()->create(['author' => 'John Doe', 'category' => 'Tech', 'published_at' => '2024-12-08']);
        Article::factory()->create(['author' => 'Jane Smith', 'category' => 'Health', 'published_at' => '2024-12-09']);
        Article::factory()->create(['author' => 'Alice Johnson', 'category' => 'Tech', 'published_at' => '2024-12-10']);

        $response = $this->get('/api/articles?published_at_start=2024-12-08&published_at_end=2024-12-09');
        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_get_articles_by_single_published_at_start()
    {
        Article::factory()->create(['author' => 'John Doe', 'category' => 'Tech', 'published_at' => '2024-12-08']);
        Article::factory()->create(['author' => 'Jane Smith', 'category' => 'Health', 'published_at' => '2024-12-09']);
        Article::factory()->create(['author' => 'Alice Johnson', 'category' => 'Tech', 'published_at' => '2024-12-10']);

        $response = $this->get('/api/articles?published_at_start=2024-12-09');
        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_get_articles_by_single_published_at_end()
    {
        Article::factory()->create(['author' => 'John Doe', 'category' => 'Tech', 'published_at' => '2024-12-08']);
        Article::factory()->create(['author' => 'Jane Smith', 'category' => 'Health', 'published_at' => '2024-12-09']);
        Article::factory()->create(['author' => 'Alice Johnson', 'category' => 'Tech', 'published_at' => '2024-12-10']);

        $response = $this->get('/api/articles?published_at_end=2024-12-09');
        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_get_articles_by_source()
    {
        Article::factory()->create(['author' => 'John Doe', 'category' => 'Tech', 'published_at' => '2024-12-08', 'source' => 'TechCrunch']);
        Article::factory()->create(['author' => 'Jane Smith', 'category' => 'Health', 'published_at' => '2024-12-09', 'source' => 'HealthLine']);

        $response = $this->get('/api/articles?source=TechCrunch');
        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_get_articles_with_pagination()
    {
        Article::factory()->count(30)->create(['author' => 'John Doe', 'category' => 'Tech', 'published_at' => '2024-12-08']);

        $response = $this->get('/api/articles?page_size=10');
        $response->assertStatus(200)->assertJson([
            'total' => 30,
            'per_page' => 10,
            'current_page' => 1,
            'last_page' => 3,
        ]);
    }

    public function test_validation_error_author()
    {
        $response = $this->get('/api/articles?author=' . str_repeat('a', 256));
        $response->assertStatus(422)->assertJsonValidationErrors(['author']);
    }

    public function test_validation_error_category()
    {
        $response = $this->get('/api/articles?category=' . str_repeat('a', 256));
        $response->assertStatus(422)->assertJsonValidationErrors(['category']);
    }

    public function test_validation_error_published_at_start()
    {
        $response = $this->get('/api/articles?published_at_start=invalid-date');
        $response->assertStatus(422)->assertJsonValidationErrors(['published_at_start']);
    }

    public function test_validation_error_published_at_end()
    {
        $response = $this->get('/api/articles?published_at_end=invalid-date');
        $response->assertStatus(422)->assertJsonValidationErrors(['published_at_end']);
    }

    public function test_validation_error_published_at()
    {
        $response = $this->get('/api/articles?published_at=invalid-date');
        $response->assertStatus(422)->assertJsonValidationErrors(['published_at']);
    }

    public function test_validation_error_source()
    {
        $response = $this->get('/api/articles?source=' . str_repeat('a', 256));
        $response->assertStatus(422)->assertJsonValidationErrors(['source']);
    }

    public function test_validation_error_page_size_min()
    {
        $response = $this->get('/api/articles?page_size=0');
        $response->assertStatus(422)->assertJsonValidationErrors(['page_size']);
    }

    public function test_validation_error_page_size_max()
    {
        $response = $this->get('/api/articles?page_size=51');
        $response->assertStatus(422)->assertJsonValidationErrors(['page_size']);
    }
}