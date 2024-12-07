<?php
namespace App\Services;

use Carbon\Carbon;
use App\Interfaces\Mapper;


class NYTMapper implements Mapper
{
    public function mapData($data)
    {
        $articles = [];
        foreach($data['response']['docs'] as $article){
            $articles[] = [
                'doc_id' => $article['_id'],
                'published_at' => Carbon::parse($article['pub_date']),
                'category' => $article['section_name'],
                'author' => $article['byline']['original'],
                'content' => $article,
                'source' => 'nytimes',
            ];
        }

        return $articles;
    }
}