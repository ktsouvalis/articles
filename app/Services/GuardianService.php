<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class GuardianService
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