<?php

namespace App\Actions;

use Exception;
use Carbon\Carbon;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreArticles
{
    use AsAction;

    public function handle(array $mappedData, string $sourceName)
    {
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

        $this->updateLastCall($sourceName); // Ensure last call is updated
        Log::info('STORER: '.Article::count() - $old_count . ' articles stored successfully from ' . $sourceName);
        // exit;
    }

    private function updateLastCall($sourceName)
    {
        DB::table('last_news')->updateOrInsert(
            ['name' => $sourceName],
            ['last_call' => Carbon::now()]
        );
    }

    // public function handle(array $mappedData)
    // {
    //     $totalRecords = count($mappedData);
    //     $uniqueRecords = [];

    //     foreach ($mappedData as $data) {
    //         $uniqueRecords[$data['doc_id']] = [
    //             'doc_id' => $data['doc_id'],
    //             'source' => $data['source'],
    //             'published_at' => $data['published_at'],
    //             'author' => $data['author'] ?? null,
    //             'category' => $data['category'],
    //             'content' => json_encode($data['content']),
    //             'created_at' => Carbon::now(),
    //             'updated_at' => Carbon::now(),
    //         ];
    //     }

    //     $uniqueRecords = array_values($uniqueRecords);
    //     $uniqueCount = count($uniqueRecords);

    //     try {
    //         Article::insertOrIgnore($uniqueRecords);
    //     } catch (Exception $e) {
    //         Log::error($e->getMessage());
    //     }

    //     $ignoredRecords = $totalRecords - $uniqueCount;
    //     Log::info("Total records: $totalRecords, Unique records: $uniqueCount, Ignored records: $ignoredRecords");
    // }

    public function asJob(array $mappedData, string $sourceName)
    {
        return $this->handle($mappedData, $sourceName);
    }
}
