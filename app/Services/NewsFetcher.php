<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsFetcher
{
    public function fetchArticles($url, $headers)
    {
        $response = Http::get($url, $headers);

        return $response->json();
    } 
}