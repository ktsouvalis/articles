<?php

namespace App\Interfaces;

interface ArticleService
{
    public function fetchArticles();
    public function mapData($data);
}