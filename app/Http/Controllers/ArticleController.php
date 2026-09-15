<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::published()->with('image')->latest('id')->paginate(9);

        return view('site.articles.index', compact('articles'));
    }

    public function show(Article $article)
    {
        abort_unless($article->is_published, 404);

        $article->load('image');
        $related = Article::published()->where('id', '!=', $article->id)
            ->latest('id')->take(3)->get();

        return view('site.articles.show', compact('article', 'related'));
    }
}
