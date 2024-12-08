<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsFetcher
{
    protected $url;
    protected $headers;
    protected $data;

    public function __construct($url, $headers)
    {
        $this->url = $url;
        $this->headers = $headers;
        $this->fetchArticles();
    }

    private function fetchArticles()
    {
        $response = Http::get($this->url, $this->headers);
        $this->data = $response->json();
    }

    public function getData()
    {
        return $this->data;
    }
}