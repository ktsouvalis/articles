<?php

namespace App\Actions;

use Carbon\Carbon;
use App\Services\Mapper;
use App\Services\Fetcher;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class CallSources
{
    use AsAction;

    public function handle($sources)
    {
        if(empty($sources)){
            Log::info('No sources found');
            return;
        }
        foreach ($sources as $source) {
            $flattenedData = $this->fetchAndMapData($source);
            if (empty($flattenedData)) {
                Log::info('No new articles found in ' . $source['name']);
                continue;
            }
            // $flattendedData are like this:
            // $data = [
            //     [
            //         "doc_id" => "",
            //         "published_at" => "",
            //         "category" => ",
            //         "author" => "",
            //         "title" => "",
            //         "url" => "",
            //         "summary" => ""
            //     ],
            //     [
            //         "doc_id" => "",
            //         "published_at" => "",
            //         "category" => "",
            //         "author" => "",
            //         "title" => "",
            //         "url" => "",
            //         "summary" => ""
            //     ]
            // ];
            StoreArticles::dispatch($source['name'], $flattenedData);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function asJob($sources)
    {
        return $this->handle($sources);
    }

    private function fetchAndMapData($source){
        $fetcher = new Fetcher($source);
        $data = $fetcher->getData();
        if (empty($data)) {
            return [];
        }
        $mapper = new Mapper($source);
        $mappedData[] = $mapper->mapData($data);
        return array_merge(...$mappedData);
    }
}
