<?php

namespace App\Actions;

use Exception;
use Carbon\Carbon;
use App\Models\Article;
use App\Services\LastCallService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreArticles
{
    use AsAction;

    public function handle(array $mappedData, string $sourceName){
        $old_count = Article::count();
        $articles = [];
        foreach ($mappedData as $data) {
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
    
        try {
            Article::insertOrIgnore($articles);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            exit;            
        }

        $lastCallService = new LastCallService();
        $lastCallService->updateLastCall($sourceName);
        Log::info('STORER: '.Article::count() - $old_count . ' articles stored successfully from ' . $sourceName);
    }

    public function asJob(array $mappedData, string $sourceName){ 
        return $this->handle($mappedData, $sourceName);
    }
}
