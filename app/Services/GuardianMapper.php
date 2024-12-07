<?php
namespace App\Services;

use Carbon\Carbon;
use App\Interfaces\Mapper;


class GuardianMapper implements Mapper
{
    public function mapData($data)
    {
        $articles = [];
        foreach($data['response']['results'] as $article){
            $articles[] = [
                'doc_id' => $article['id'],
                'published_at' => Carbon::parse($article['webPublicationDate']),
                'category' => $article['sectionId'],
                'author' => null,
                'content' => $article,
                'source' => 'guardian',
            ];
        }

        return $articles;
    }
}