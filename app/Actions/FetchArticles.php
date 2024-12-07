<?php

namespace App\Actions;

use App\Models\Article;
use App\Services\NYTimesService;
use App\Services\GuardianService;
use Lorisleiva\Actions\Concerns\AsAction;

class FetchArticles
{
    use AsAction;

    public function handle()
    {
        // $this->fetchFromService(GuardianService::class, 'guardian');
        $this->fetchFromService(NYTimesService::class, 'nyt');
    }

    private function fetchFromService(string $serviceClass, string $source)
    {
        $service = new $serviceClass();
        $articles = $service->fetchArticles();
        $mappedData = $service->mapData($articles);
        $this->saveArticles($mappedData, $source);
    }

    private function saveArticles(array $mappedData, string $source)
    {
        foreach ($mappedData as $data) {
            Article::updateOrCreate(
                ['doc_id' => $data['doc_id']],
                [
                    'source' => $source,
                    'published_at' => $data['date'],
                    'author' => $data['author'] ?? null,
                    'category' => $data['section'],
                    'content' => json_encode($data['content']),
                ]
            );
        }
    }

    public function asJob()
    {
        return $this->handle();
    }
}
