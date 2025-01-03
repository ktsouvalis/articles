<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    /**
     * Retrieve articles based on the given criteria.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArticlesByCriteria(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'author' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'published_at_start' => 'nullable|date',
            'published_at_end' => 'nullable|date|after_or_equal:published_at_start',
            'published_at' => 'nullable|date',
            'source' => 'nullable|string|max:255',
            'page_size' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = Article::query();

        if ($request->filled('author')) {
            $query->where('author', 'like', '%' . $request->input('author') . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', 'like', '%' . $request->input('category') . '%');
        }

        if ($request->filled('published_at_start') xor $request->filled('published_at_end')){
            $query->whereDate('published_at', $request->input('published_at_start') ?? $request->input('published_at_end'));
        } 
        elseif ($request->filled('published_at_start') && $request->filled('published_at_end')){
            $query->whereBetween('published_at', [$request->input('published_at_start'), $request->input('published_at_end')]);
        } 
        elseif ($request->filled('published_at')){
            $query->whereDate('published_at', $request->input('published_at'));
        }

        if ($request->filled('source')) {
            $query->where('source', 'like', '%' . $request->input('source') . '%');
        }

        $pageSize = $request->input('page_size', 20);
        $articles = $query->paginate($pageSize, ['doc_id', 'author', 'category', 'published_at', 'source', 'content']);

        if ($articles->isEmpty()) {
            return response()->json(['message' => 'No articles found matching the given criteria.'], 404);
        }

        // Append query parameters to pagination URLs
        $articles->appends($request->except('page'));

        return response()->json([
            'meta' => [
                'total' => $articles->total(),
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'next_page_url' => $articles->nextPageUrl(),
                'prev_page_url' => $articles->previousPageUrl(),
            ],
            'data' => ArticleResource::collection($articles),
        ], 200);
    }
}