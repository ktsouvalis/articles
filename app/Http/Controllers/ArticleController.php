<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function getArticlesByCriteria(Request $request){

        $query = Article::query();

        if ($request->has('author')){
            $query->where('author', 'like', '%' . $request->input('author') . '%');
        }

        if ($request->has('category')){
            $query->where('category', 'like', '%' . $request->input('category') . '%');
        }

        if ($request->has('published_at_start') xor $request->has('published_at_end')){
            $query->whereDate('published_at', $request->input('published_at_start') ?? $request->input('published_at_end'));
        } 
        elseif ($request->has('published_at_start') && $request->has('published_at_end')){
            $query->whereBetween('published_at', [$request->input('published_at_start'), $request->input('published_at_end')]);
        } 
        elseif ($request->has('published_at')){
            $query->whereDate('published_at', $request->input('published_at'));
        }

        if ($request->has('source')){
            $query->where('source', 'like', '%' . $request->input('source') . '%');
        }

        $pageSize = min($request->input('page_size', 20), 50);
        $articles = $query->paginate($pageSize, ['content']);

        // JSON decode the content subkey of each article
        $articles->transform(function ($article){
            $article->content = json_decode($article->content);
            return $article;
        });

        if ($articles->isEmpty()){
            return response()->json(['message' => 'No articles found matching the given criteria.'], 404);
        }

        // Append query parameters to pagination URLs
        $articles->appends($request->except('page'));

        return response()->json([
            'total' => $articles->total(),
            'current_page' => $articles->currentPage(),
            'last_page' => $articles->lastPage(),
            'per_page' => $articles->perPage(),
            'next_page_url' => $articles->nextPageUrl(),
            'prev_page_url' => $articles->previousPageUrl(),
            'data' => $articles->items(),
        ], 200);
    }
}
