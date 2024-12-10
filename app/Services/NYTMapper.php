<?php
namespace App\Services;

use Carbon\Carbon;
use App\Interfaces\Mapper;
use Illuminate\Support\Facades\Log;

class NYTMapper implements Mapper
{
    public function mapData($data){
        $articles = [];
        foreach($data as $page){
            foreach($page['response']['docs'] as $article){
                $articles[] = [
                    'doc_id' => $article['_id'],
                    'published_at' => Carbon::parse($article['pub_date']),
                    'category' => $article['section_name'],
                    'author' => $article['byline']['original'],
                    'content'=> method_exists($this, 'getInterestingData') ? $this->getInterestingData($article) : $article,
                    'source' => 'nytimes',
                ];
            }
        }
        Log::info("MAPPER: Mapped " . count($articles) . " articles from NYT");
        return $articles;
    }

    private function getInterestingData($article){
        return [
            'category' => $article['section_name'],
            'author' => $article['byline']['original'] ?? null,
            'title' => $article['headline']['print_headline'] ?? $article['headline']['main'],
            'url' => $article['web_url'],
            'summary' => $article['abstract'],
            'published_at' => Carbon::parse($article['pub_date']),
        ];
    }
}