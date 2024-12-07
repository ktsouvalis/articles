<?php
namespace App\Services;

use ArticleService;
use Illuminate\Support\Facades\Http;

class GuardianService implements ArticleService
{
    public function fetchArticles()
    {
        $response = Http::get('https://content.guardianapis.com/search', [
            'api-key' => env('GUARDIAN_API_KEY'),
            'show-fields' => 'all',
        ]);

        return $response->json();
    } 
}