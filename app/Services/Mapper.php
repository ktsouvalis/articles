<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Mapper
{
    private $fields;
    private $articles_key;

    public function __construct($source) {
        $this->fields = $source['fields'];
        $this->articles_key = explode('.', $source['articles_key']);
    }

    public function mapData($data) {
        $articles = [];
        foreach ($data as $page) {
            $nestedData = $page;
            foreach ($this->articles_key as $key) {
                if (isset($nestedData[$key])) {
                    $nestedData = $nestedData[$key];
                } else {
                    Log::error("Key '$key' not found in page data");
                    break 2; // Break both loops
                }
            }
            foreach ($nestedData as $article) {
                $mappedArticle = [];
                foreach ($this->fields as $key => $field) {
                    $mappedArticle[$key] = $this->getFieldValue($article, $field);
                }
                $mappedArticle['source'] = strtolower($this->fields['source']);
                $articles[] = $mappedArticle;
            }
        }
        Log::info("MAPPER: Mapped " . count($articles) . " articles");
        return $articles;
    }

    private function getFieldValue($article, $field) {
        if (is_null($field)) {
            return null;
        }
        $keys = explode('.', $field);
        $value = $article;
        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return null;
            }
        }
        return $value;
    }
}