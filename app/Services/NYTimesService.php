<?php
namespace App\Services;

use Carbon\Carbon;
use App\Interfaces\ArticleService;
use Illuminate\Support\Facades\Http;

class NYTimesService implements ArticleService
{
    public function fetchArticles()
    {
        $response = Http::get('api.nytimes.com/svc/search/v2/articlesearch.json', [
            // 'fq' => 'section_name:("Sports")',
            'api-key' => env('NYTIMES_API_KEY'),
        ]);

        return $response->json();
    }

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
            ];
        }

        return $articles;
    }
}