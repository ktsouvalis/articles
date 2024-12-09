<?php
namespace App\Services;

use Carbon\Carbon;
use App\Interfaces\Mapper;
use Illuminate\Support\Facades\Log;


class GuardianMapper implements Mapper
{
    public function mapData($data)
    {
        $articles = [];
        foreach($data as $page){
            foreach($page['response']['results'] as $article){
                $articles[] = [
                    'doc_id' => $article['id'],
                    'published_at' => Carbon::parse($article['webPublicationDate']),
                    'category' => $article['sectionId'],
                    'author' => null,
                    'content' => $article,
                    'source' => 'guardian',
                ];
            }
        }
        Log::info("MAPPER: Mapped " . count($articles) . " articles from Guardian");
        return $articles;
    }
}