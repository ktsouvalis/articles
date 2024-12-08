<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NewsFetcher
{
    protected $baseUrl;
    protected $headers;
    protected $start_page;
    protected $data;

    public function __construct($baseUrl, $headers, $start_page)
    {
        $this->baseUrl = $baseUrl;
        $this->headers = $headers;
        $this->start_page = $start_page;
        $this->fetchArticles();
    }

    private function fetchArticles()
    {
        $page = $this->start_page;
        $allData = [];
        $i=1;

        do {
            $query = array_merge($this->headers, ['page' => $page]);
            $url = $this->baseUrl . '&' . http_build_query($query);

            $response = Http::withHeaders($this->headers)->get($url);

            Log::info("Try to fetch from $url");
            
            if ($response->status() != 200) {
                Log::error('Error '.$response->status().': '.$response->body());
                break;
            }

            Log::info("Success");

            $data = $response->json();
            $allData[] = $data;
            $page++;
        } while (true);

        $this->data = $allData;
    }

    public function getData()
    {
        return $this->data;
    }
}