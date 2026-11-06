<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::latest('id')->paginate(10);

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $article = new Article();

        return view('admin.articles.create', compact('article'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'image' => 'required|image',
            'font_family' => 'required|in:cairo,tajawal,serif',
            'font_size' => 'required|in:small,medium,large,xlarge',
        ]);

        $article = Article::create($this->payload($request));
        $this->saveImage($request, $article);

        return redirect()->route('admin.articles.index')
            ->with('msg', 'Article added successfully')->with('type', 'success');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'image' => 'nullable|image',
            'font_family' => 'required|in:cairo,tajawal,serif',
            'font_size' => 'required|in:small,medium,large,xlarge',
        ]);

        $article->update($this->payload($request));

        if ($request->hasFile('image')) {
            if ($article->image && $article->image->path && $article->image->path !== 'no-image.png') {
                File::delete(public_path('images/' . $article->image->path));
            }
            $article->image()->delete();
            $this->saveImage($request, $article);
        }

        return redirect()->route('admin.articles.index')
            ->with('msg', 'Article updated successfully')->with('type', 'info');
    }

    public function destroy(Article $article)
    {
        if ($article->image && $article->image->path && $article->image->path !== 'no-image.png') {
            File::delete(public_path('images/' . $article->image->path));
        }
        $article->image()->delete();
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('msg', 'Article deleted successfully')->with('type', 'info');
    }

    private function payload(Request $request): array
    {
        return [
            'name' => json_encode(['en' => $request->name_en, 'ar' => $request->name_ar], JSON_UNESCAPED_UNICODE),
            'description' => json_encode(['en' => $request->description_en, 'ar' => $request->description_ar], JSON_UNESCAPED_UNICODE),
            'is_published' => $request->boolean('is_published'),
            'font_family' => $request->input('font_family', 'cairo'),
            'font_size' => $request->input('font_size', 'medium'),
        ];
    }

    private function saveImage(Request $request, Article $article): void
    {
        if (! $request->hasFile('image')) {
            return;
        }
        $img_name = rand() . time() . $request->file('image')->getClientOriginalName();
        $request->file('image')->move(public_path('images'), $img_name);
        $article->image()->create(['path' => $img_name]);
    }
}
