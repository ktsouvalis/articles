<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class BBCService
{
    public function fetchArticles()
    {
        $response = Http::get('https://newsapi.org/v2/top-headlines', [
            'apiKey' => env('BBC_API_KEY'),
            'sources' => 'bbc-news',
        ]);

        return $response->json();
    }
}