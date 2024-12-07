<?php

namespace App\Actions;

use App\Models\Article;
use App\Services\BBCService;
use App\Services\NYTimesService;
use App\Services\GuardianService;
use Lorisleiva\Actions\Concerns\AsAction;

class FetchArticles
{
    use AsAction;

    public function handle()
    {
        $this->fetchFromGuardian();
        $this->fetchFromNYTimes();
        $this->fetchFromBBC();
    }

    private function fetchFromGuardian()
    {
        $service = new GuardianService();
        $articles = $service->fetchArticles();

        foreach ($articles['response']['results'] as $articleData) {
            Article::updateOrCreate(
                ['title' => $articleData['webTitle']],
                [
                    'content' => $articleData['fields']['bodyText'],
                    'author' => $articleData['fields']['byline'] ?? null,
                    'source' => 'The Guardian',
                    'url' => $articleData['webUrl'],
                ]
            );
        }
    }

    private function fetchFromNYTimes()
    {
        $service = new NYTimesService();
        $articles = $service->fetchArticles();

        foreach ($articles['results'] as $articleData) {
            Article::updateOrCreate(
                ['title' => $articleData['title']],
                [
                    'content' => $articleData['abstract'],
                    'author' => $articleData['byline'] ?? null,
                    'source' => 'New York Times',
                    'url' => $articleData['url'],
                ]
            );
        }
    }

    private function fetchFromBBC()
    {
        $service = new BBCService();
        $articles = $service->fetchArticles();

        foreach ($articles['articles'] as $articleData) {
            Article::updateOrCreate(
                ['title' => $articleData['title']],
                [
                    'content' => $articleData['content'],
                    'author' => $articleData['author'] ?? null,
                    'source' => 'BBC News',
                    'url' => $articleData['url'],
                ]
            );
        }
    }

    public function asJob()
    {
        return $this->handle;
    }
}
