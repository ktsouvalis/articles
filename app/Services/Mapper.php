<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Mapper
{
    private $fields;
    private $articles_key;
    private $name;

    public function __construct($source) {
        $this->name = $source['name'];
        $this->fields = $source['fields'];
        $this->articles_key = explode('.', $source['articles_key']);
    }

    public function mapData($data) {
        $articles = [];
        foreach ($data as $page) {
            $nestedData = $this->getNestedData($page);
            foreach ($nestedData as $article) {
                $mappedArticle = $this->mapArticle($article);
                if ($mappedArticle !== null) {
                    $articles[] = $mappedArticle;
                }
            }
        }
        Log::info("MAPPER: Mapped " . count($articles) . " articles");
        return $articles;
    }

    private function getNestedData($page) {
        $nestedData = $page;
        foreach ($this->articles_key as $key) {
            $nestedData = $nestedData[$key];
        }
        return $nestedData;
    }

    private function mapArticle($article) {
        $mappedArticle = [];
        foreach ($this->fields as $key => $field) {
            if ($field == null) {
                $mappedArticle[$key] = 'unknown';
                continue;
            }
            $fieldValue = $this->getFieldValue($article, $field);
            if (is_null($fieldValue)) {
                Log::error("MAPPER: $this->name Field $field not found in article");
                return null;
            }
            $mappedArticle[$key] = $fieldValue;
        }
        return $mappedArticle;
    }

    private function getFieldValue($article, $field) {
        $keys = explode('.', $field);
        $value = $article;
    
        foreach ($keys as $key) {
            if (array_key_exists($key, $value)) { // Check if the key exists in the array
                $value = $value[$key];
                if (is_null($value)) {
                    return 'unknown'; // Key exists but value is null
                }
            } else {
                return null; // Key does not exist
            }
        }
    
        return $value;
    }
}