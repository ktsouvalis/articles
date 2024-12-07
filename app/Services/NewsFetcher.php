<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsFetcher
{
    public function fetchArticles($url, $key)
    {
        $response = Http::get($url, [
            'api-key' => $key,
        ]);

        return $response->json();
    } 
}