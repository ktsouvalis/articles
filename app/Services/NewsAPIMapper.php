<?php
namespace App\Services;

use Carbon\Carbon;
use App\Interfaces\Mapper;
use Illuminate\Support\Facades\Log;

class NewsAPIMapper implements Mapper
{
    public function mapData($data){
        $articles = [];
        foreach($data as $page){
            foreach($page['articles'] as $article){
                $articles[] = [
                    'doc_id' => $article['url'],
                    'published_at' => Carbon::parse($article['publishedAt']),
                    'category' => $article['title'], // something to help searching
                    'author' => $article['author'] ?? null,
                    'content' => method_exists($this, 'getInterestingData') ? $this->getInterestingData($article) : $article,
                    'source' => $article['source']['name'],
                ];
            }
        }
        Log::info("MAPPER: Mapped " . count($articles) . " articles from NewsAPI");
        return $articles;
    }

    private function getInterestingData($article){
        return [ 
            'category' => 'Uncategorized', // newasapi does not provide category to this call
            'author' => $article['author'] ?? null,
            'title' => $article['title'],
            'url' => $article['url'],
            'summary' => $article['description'],
            'published_at' =>Carbon::parse($article['publishedAt']),
        ];
    }
}