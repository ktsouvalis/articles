<?php

namespace App\Actions;

use App\Models\Article;
use Lorisleiva\Actions\Concerns\AsAction;
use Carbon\Carbon;

class StoreArticles
{
    use AsAction;

    public function handle(array $mappedData)
    {
        foreach ($mappedData as $source) {
            $articles = [];
            foreach($source as $data){
                $articles[] = [
                    'doc_id' => $data['doc_id'],
                    'source' => $data['source'],
                    'published_at' => $data['published_at'],
                    'author' => $data['author'] ?? null,
                    'category' => $data['category'],
                    'content' => json_encode($data['content']),
                    'created_at' => Carbon::now(),
                ];
            }
            Article::insertOrIgnore($articles);
        }
    }

    public function asJob(array $mappedData)
    {
        return $this->handle($mappedData);
    }
}
