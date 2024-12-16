<?php

namespace App\Actions;

use Exception;
use Carbon\Carbon;
use App\Models\Article;
use App\Services\LastCall;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreArticles
{
    use AsAction;

    public function handle(string $name, array $mappedData) {
        $old_count = Article::count();
        $articles = [];
        foreach ($mappedData as $data) {
            $articles[] = [
                'doc_id' => $data['doc_id'],
                'source' => strtolower($name),
                'published_at' => Carbon::parse($data['published_at']),
                'author' => $data['author'] ?? null,
                'category' => $data['category'],
                'content' => json_encode($data),
                'created_at' => Carbon::now(),
            ];
        }

        try {
            Article::insertOrIgnore($articles);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            exit;
        }

        $lastCallService = new LastCall();
        $lastCallService->updateLastCall($articles[0]['source']);
        Log::info('STORER: ' . (Article::count() - $old_count) . ' articles stored successfully from ' . $articles[0]['source']);
    }

    public function asJob(string $name, array $mappedData) { 
        return $this->handle($name, $mappedData);
    }
}
