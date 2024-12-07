<?php
namespace App\Services;

use Carbon\Carbon;


class GuardianMapper
{
    public function mapData($data)
    {
        $articles = [];
        foreach($data['response']['results'] as $article){
            $articles[] = [
                'doc_id' => $article['id'],
                'date' => Carbon::parse($article['webPublicationDate']),
                'section' => $article['sectionId'],
                'author' => null,
                'content' => $article,
                'source' => 'guardian',
            ];
        }

        return $articles;
    }
}