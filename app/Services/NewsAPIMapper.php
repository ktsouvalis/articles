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
                    'category' => $article['title'],
                    'author' => $article['author'] ?? null,
                    'content' => $article,
                    'source' => $article['source']['name'],
                ];
            }
        }
        Log::info("MAPPER: Mapped " . count($articles) . " articles from NewsAPI");
        return $articles;
    }
}