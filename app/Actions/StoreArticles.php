<?php

namespace App\Actions;

use App\Models\Article;
use App\Services\NYTimesService;
use App\Services\GuardianService;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreArticles
{
    use AsAction;

    public function handle(array $mappedData)
    {
        // dump($mappedData);
        foreach ($mappedData as $source) {
            foreach($source as $data){
                if(Article::where('doc_id', $data['doc_id'])->exists()){
                    continue;
                }
                Article::create([
                    'doc_id' => $data['doc_id'],
                    'source' => $data['source'],
                    'published_at' => $data['date'],
                    'author' => $data['author'] ?? null,
                    'category' => $data['section'],
                    'content' => json_encode($data['content']),
                ]);
            }
        }
    }

    public function asJob(array $mappedData)
    {
        return $this->handle($mappedData);
    }
}
