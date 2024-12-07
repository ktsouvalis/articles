<?php
namespace App\Services;

use Carbon\Carbon;


class NYTMapper
{
    public function mapData($data)
    {
        $articles = [];
        foreach($data['response']['docs'] as $article){
            $articles[] = [
                'doc_id' => $article['_id'],
                'date' => Carbon::parse($article['pub_date']),
                'section' => $article['section_name'],
                'author' => $article['byline']['original'],
                'content' => $article,
                'source' => 'nytimes',
            ];
        }

        return $articles;
    }
}