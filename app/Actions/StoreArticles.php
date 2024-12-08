<?php

namespace App\Actions;

use Exception;
use Carbon\Carbon;
use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreArticles
{
    use AsAction;

    public function handle(array $mappedData)
    {
        foreach ($mappedData as $source) {
            foreach($source as $data){
                $articles = [];
                $articles[] = [
                    'doc_id' => $data['doc_id'],
                    'source' => $data['source'],
                    'published_at' => $data['published_at'],
                    'author' => $data['author'] ?? null,
                    'category' => $data['category'],
                    'content' => json_encode($data['content']),
                    'created_at' => Carbon::now(),
                ];
                try{
                    Article::insertOrIgnore($articles);
                } 
                catch (Exception $e){
                    Log::error($e->getMessage());
                    continue;
                }
            }
        }
    }

    public function asJob(array $mappedData)
    {
        return $this->handle($mappedData);
    }
}
