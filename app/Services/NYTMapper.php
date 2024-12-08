<?php
namespace App\Services;

use Carbon\Carbon;
use App\Interfaces\Mapper;
use Illuminate\Support\Facades\Log;

class NYTMapper implements Mapper
{
    public function mapData($data)
    {
        $articles = [];
        foreach($data as $page){
            foreach($page['response']['docs'] as $article){
                $articles[] = [
                    'doc_id' => $article['_id'],
                    'published_at' => Carbon::parse($article['pub_date']),
                    'category' => $article['section_name'],
                    'author' => $article['byline']['original'],
                    'content' => $article,
                    'source' => 'nytimes',
                ];
            }
        }
        Log::info("Mapped " . count($articles) . " articles from NYT");
        return $articles;
    }
}