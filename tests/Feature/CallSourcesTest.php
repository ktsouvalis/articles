<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Actions\CallSources;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CallSourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_call_sources_combined()
    {
        Http::fake([
            'nytimes.com/*' => Http::response(['response' => ['docs' => [['pub_date' => '2024-12-08', '_id' => '1', 'section_name' => 'Tech', 'byline' => ['original' => 'John Doe'], 'headline' => ['main' => 'Tech News'], 'web_url' => 'http://example.com', 'abstract' => 'Summary of the article']]]], 200),
            'guardianapis.com/*' => Http::response(['response' => ['results' => [['webPublicationDate' => '2024-12-08', 'id' => '2', 'sectionId' => 'Tech', 'sectionName' => 'Technology', 'webTitle' => 'Tech News', 'webUrl' => 'http://example.com']]]], 200),
            'newsapi.org/*' => Http::response(['articles' => [['publishedAt' => '2024-12-08', 'url' => '3', 'title' => 'Tech News', 'author' => 'John Doe', 'description' => 'Summary of the article', 'source' => ['name' => 'NewsAPI']]]], 200),
        ]);

        $callSources = new CallSources();
        $callSources->handle();

        $this->assertDatabaseHas('articles', ['doc_id' => '1']);
        $this->assertDatabaseHas('articles', ['doc_id' => '2']);
        $this->assertDatabaseHas('articles', ['doc_id' => '3']);
    }

    public function test_call_sources_first_not_responding()
    {
        Http::fake([
            'nytimes.com/*' => Http::response(null, 404),
            'guardianapis.com/*' => Http::response(['response' => ['results' => [['webPublicationDate' => '2024-12-08', 'id' => '2', 'sectionId' => 'Tech', 'sectionName' => 'Technology', 'webTitle' => 'Tech News', 'webUrl' => 'http://example.com']]]], 200),
            'newsapi.org/*' => Http::response(['articles' => [['publishedAt' => '2024-12-08', 'url' => '3', 'title' => 'Tech News', 'author' => 'John Doe', 'description' => 'Summary of the article', 'source' => ['name' => 'NewsAPI']]]], 200),
        ]);

        $callSources = new CallSources();
        $callSources->handle();

        $this->assertDatabaseMissing('articles', ['doc_id' => '1']);
        $this->assertDatabaseHas('articles', ['doc_id' => '2']);
        $this->assertDatabaseHas('articles', ['doc_id' => '3']);
    }

    public function test_call_sources_second_not_responding()
    {
        Http::fake([
            'nytimes.com/*' => Http::response(['response' => ['docs' => [['pub_date' => '2024-12-08', '_id' => '1', 'section_name' => 'Tech', 'byline' => ['original' => 'John Doe'], 'headline' => ['main' => 'Tech News'], 'web_url' => 'http://example.com', 'abstract' => 'Summary of the article']]]], 200),
            'guardianapis.com/*' => Http::response(null, 404),
            'newsapi.org/*' => Http::response(['articles' => [['publishedAt' => '2024-12-08', 'url' => '3', 'title' => 'Tech News', 'author' => 'John Doe', 'description' => 'Summary of the article', 'source' => ['name' => 'NewsAPI']]]], 200),
        ]);

        $callSources = new CallSources();
        $callSources->handle();

        $this->assertDatabaseHas('articles', ['doc_id' => '1']);
        $this->assertDatabaseMissing('articles', ['doc_id' => '2']);
        $this->assertDatabaseHas('articles', ['doc_id' => '3']);
    }

    public function test_call_sources_first_returns_no_results()
    {
        Http::fake([
            'nytimes.com/*' => Http::response(['response' => ['docs' => []]], 200),
            'guardianapis.com/*' => Http::response(['response' => ['results' => [['webPublicationDate' => '2024-12-08', 'id' => '2', 'sectionId' => 'Tech', 'sectionName' => 'Technology', 'webTitle' => 'Tech News', 'webUrl' => 'http://example.com']]]], 200),
            'newsapi.org/*' => Http::response(['articles' => [['publishedAt' => '2024-12-08', 'url' => '3', 'title' => 'Tech News', 'author' => 'John Doe', 'description' => 'Summary of the article', 'source' => ['name' => 'NewsAPI']]]], 200),
        ]);

        $callSources = new CallSources();
        $callSources->handle();

        $this->assertDatabaseMissing('articles', ['doc_id' => '1']);
        $this->assertDatabaseHas('articles', ['doc_id' => '2']);
        $this->assertDatabaseHas('articles', ['doc_id' => '3']);
    }

    public function test_call_sources_third_returns_no_results()
    {
        Http::fake([
            'nytimes.com/*' => Http::response(['response' => ['docs' => [['pub_date' => '2024-12-08', '_id' => '1', 'section_name' => 'Tech', 'byline' => ['original' => 'John Doe'], 'headline' => ['main' => 'Tech News'], 'web_url' => 'http://example.com', 'abstract' => 'Summary of the article']]]], 200),
            'guardianapis.com/*' => Http::response(['response' => ['results' => [['webPublicationDate' => '2024-12-08', 'id' => '2', 'sectionId' => 'Tech', 'sectionName' => 'Technology', 'webTitle' => 'Tech News', 'webUrl' => 'http://example.com']]]], 200),
            'newsapi.org/*' => Http::response(['articles' => [], 200]),
        ]);

        $callSources = new CallSources();
        $callSources->handle();

        $this->assertDatabaseHas('articles', ['doc_id' => '1']);
        $this->assertDatabaseHas('articles', ['doc_id' => '2']);
        $this->assertDatabaseMissing('articles', ['doc_id' => '3']);
    }

    public function test_call_sources_api_responds_empty()
    {
        Http::fake([
            'nytimes.com/*' => Http::response(['response' => ['docs' => []]], 200),
            'guardianapis.com/*' => Http::response(['response' => ['results' => []]], 200),
            'newsapi.org/*' => Http::response(['articles' => []], 200),
        ]);

        $callSources = new CallSources();
        $callSources->handle();

        $this->assertDatabaseMissing('articles', ['doc_id' => '1']);
        $this->assertDatabaseMissing('articles', ['doc_id' => '2']);
        $this->assertDatabaseMissing('articles', ['doc_id' => '3']);
    }

    // public function test_call_sources_rate_limiter()
    // {
    //     Http::fake([
    //         'nytimes.com/*' => Http::response(['message' => 'Rate limit exceeded'], 429),
    //         'guardianapis.com/*' => Http::response(['message' => 'Rate limit exceeded'], 429),
    //         'newsapi.org/*' => Http::response(['message' => 'Rate limit exceeded'], 429),
    //     ]);

    //     $callSources = new CallSources();
    //     $callSources->handle();

    //     // Assert that no articles are stored due to rate limiting
    //     $this->assertDatabaseMissing('articles', ['doc_id' => '1']);
    //     $this->assertDatabaseMissing('articles', ['doc_id' => '2']);
    //     $this->assertDatabaseMissing('articles', ['doc_id' => '3']);
    // }
}