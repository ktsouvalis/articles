<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NewsFetcher
{
    protected $baseUrl;
    protected $headers;
    protected $start_page;
    protected $total_key;
    protected $page_size;

    protected $data;
    protected $total_pages;

    public function __construct($baseUrl, $headers, $start_page, $total_key, $page_size){
        $this->baseUrl = $baseUrl;
        $this->headers = $headers;
        $this->start_page = $start_page;
        $this->total_key = $total_key;
        $this->page_size = $page_size;
        $this->total_pages = 1;
        $this->fetchArticles();
    }

    private function fetchArticles(){
        $page = $this->start_page;
        $allData = [];

        while ($page <= $this->total_pages) {
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

            if ($page == $this->start_page) {
                $resultsNumber = $this->retreiveResultsNumberFromResponse($data);
                Log::info("Total results: $resultsNumber");
                $this->total_pages = $this->calculatePages($resultsNumber);
                Log::info("Total pages: $this->total_pages");
            }

            $page++;
        }

        $this->data = $allData;
    }

    private function retreiveResultsNumberFromResponse($data){
        $keys = explode('.', $this->total_key);
        $value = $data;
        foreach ($keys as $key) {
            Log::info("Checking array for key '$key'");
            if (isset($value[$key])) {
                $value = $value[$key];
                Log::info("Retreived key $key");
            } else {
                Log::error("Key '$key' not found in array");
                return 0;
            }
        }
        return $value;   
    }

    private function calculatePages($total){
        return ceil($total / $this->page_size);
    }

    public function getData(){
        return $this->data;
    }
}