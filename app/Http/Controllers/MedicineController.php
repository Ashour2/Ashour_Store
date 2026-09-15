<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    /**
     * Public homepage: hero + search + categories + featured medicines.
     */
    public function home()
    {
        $categories = Category::withCount('products')->latest('id')->take(8)->get();
        $medicines = Product::with('image', 'category')->latest('id')->take(8)->get();
        $articles = Article::published()->with('image')->latest('id')->take(3)->get();

        return view('site.home', compact('categories', 'medicines', 'articles'));
    }

    /**
     * Medicines listing with search and category filter.
     */
    public function index(Request $request)
    {
        $q = $request->input('q');
        $category_id = $request->input('category');

        $medicines = Product::with('image', 'category')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('active_ingredient', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->when($category_id, fn ($query) => $query->where('category_id', $category_id))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('id')->get();

        return view('site.medicines.index', compact('medicines', 'categories', 'q', 'category_id'));
    }

    /**
     * Single medicine details.
     */
    public function show(Product $medicine)
    {
        $medicine->load('image', 'gallery', 'category');
        $related = Product::where('category_id', $medicine->category_id)
            ->where('id', '!=', $medicine->id)
            ->with('image')
            ->take(4)
            ->get();

        return view('site.medicines.show', compact('medicine', 'related'));
    }
}
