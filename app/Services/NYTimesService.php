<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class NYTimesService
{
    public function fetchArticles()
    {
        $response = Http::get('https://api.nytimes.com/svc/topstories/v2/home.json', [
            'api-key' => env('NYTIMES_API_KEY'),
        ]);

        return $response->json();
    }
}