<?php
namespace App\Services;

use Carbon\Carbon;
use App\Interfaces\Mapper;
use Illuminate\Support\Facades\Log;

class ExampleMapper implements Mapper
{
    //mapData() is the only method required by the Mapper interface
    //It takes the raw data from the API and returns an array of articles for the database
    public function mapData($data){
        $articles = [];
        foreach($data as $page){
            // foreach($data['response']['results'] as $article){ // replace ['response']['results'] with the actual path to the articles in your API response
            //     $articles[] = [
            //         'doc_id' => $article['your-source-response-unique-key'],
            //         'published_at' => Carbon::parse($article['your-source-response-date-of-article-publication-key']),
            //         'category' => $article['your-source-response-category-key'],
            //         'author' => $article['your-source-response-author-key'] ?? null,
            //         'content' => method_exists($this, 'getInterestingData') ? $this->getInterestingData($article) : $article, // This is the entire article content or the preferred data if someone implemets private getInterestingData()
            //         'source' => 'your-source-name',
            //     ];
            // }
        }
        Log::info("MAPPER: Mapped " . count($articles) . " articles from Example");
        return $articles;
    }

    // OPTIONAL: Extracts only the data of interest from the article for our API response
    // private function getInterestingData($article){
    //     return [
    //         'category' => $article['your-source-response-category-key'],
    //         'author' => $article['your-source-response-author-key'] ?? null,
    //         'title' => $article['your-source-response-title-key'],
    //         'url' => $article['your-source-response-url-key'],
    //         'summary' => $article['your-source-response-summary-key'],
    //         'published_at' => Carbon::parse($article['your-source-response-date-of-article-publication-key']),
    //     ];
    // }
}