<?php
namespace App\Services;

use Carbon\Carbon;
use App\Interfaces\Mapper;

class NewsAPIMapper implements Mapper
{
    public function mapData($data)
    {
        $articles = [];
        foreach($data['articles'] as $article){
            $articles[] = [
                'doc_id' => $article['url'],
                'published_at' => Carbon::parse($article['publishedAt']),
                'category' => $article['title'],
                'author' => $article['author'] ?? null,
                'content' => $article,
                'source' => 'NewsApi:'.$article['source']['name'],
            ];
        }

        return $articles;
    }
}