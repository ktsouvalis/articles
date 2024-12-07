<?php
namespace App\Services;

use Carbon\Carbon;
use App\Interfaces\ArticleService;
use Illuminate\Support\Facades\Http;

class GuardianService implements ArticleService
{
    public function fetchArticles()
    {
        $response = Http::get('content.guardianapis.com/search', [
            'api-key' => env('GUARDIAN_API_KEY'),
        ]);

        return $response->json();
    } 

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
            ];
        }

        return $articles;
    }
}