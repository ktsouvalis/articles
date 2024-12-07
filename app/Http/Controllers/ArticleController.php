<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query();

        if ($request->has('author')) {
            $query->where('author', $request->input('author'));
        }

        if ($request->has('source')) {
            $query->where('source', $request->input('source'));
        }

        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        return response()->json($query->get());
    }
}
