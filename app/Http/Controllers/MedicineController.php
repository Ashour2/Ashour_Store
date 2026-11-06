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
        // Four homepage galleries: one mixed selection and three categories.
        // The manager controls category order through home_display_order.
        $categories = Category::with('image')->whereHas('products', fn ($products) => $products->publiclyVisible())
            ->withCount(['products' => fn ($products) => $products->publiclyVisible()])
            ->orderBy('home_display_order')->orderBy('id')->get();

        // Prefer pain-relief and children when those categories exist. Any
        // remaining gallery slots follow the order saved by the manager.
        $pain = $categories->first(fn (Category $category) => str_contains(mb_strtolower($category->name_ar . ' ' . $category->name_en), 'مسكن') || str_contains(mb_strtolower($category->name_ar . ' ' . $category->name_en), 'pain'));
        $children = $categories->first(fn (Category $category) => str_contains(mb_strtolower($category->name_ar . ' ' . $category->name_en), 'طفل') || str_contains(mb_strtolower($category->name_ar . ' ' . $category->name_en), 'baby') || str_contains(mb_strtolower($category->name_ar . ' ' . $category->name_en), 'children'));
        $categoryWidgets = collect([$pain, $children])->filter()->unique('id');
        $categoryWidgets = $categoryWidgets->concat($categories->reject(fn (Category $category) => $categoryWidgets->contains('id', $category->id)))->take(3)->values();

        $categoryWidgets->each(function (Category $category) {
            $category->setRelation('galleryMedicines', Product::publiclyVisible()
                ->where('category_id', $category->id)->with(['image', 'category', 'pharmacies'])->latest('id')->take(10)->get());
        });
        $mixedMedicines = Product::publiclyVisible()->with(['image', 'category', 'pharmacies'])->inRandomOrder()->take(10)->get();
        $articles = Article::published()->with('image')->latest('id')->take(3)->get();

        return view('site.home', compact('categoryWidgets', 'mixedMedicines', 'articles'));
    }

    /**
     * Medicines listing with search and category filter.
     */
    public function index(Request $request)
    {
        $q = $request->input('q');
        $category_id = $request->input('category');

        $medicines = Product::publiclyVisible()->with('image', 'category', 'pharmacies')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('active_ingredient', 'like', "%{$q}%")
                        ->orWhere('scientific_name', 'like', "%{$q}%")
                        ->orWhere('trade_name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->when($category_id, fn ($query) => $query->where('category_id', $category_id))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::with('image')->orderBy('id')->get();

        return view('site.medicines.index', compact('medicines', 'categories', 'q', 'category_id'));
    }

    /**
     * Single medicine details.
     */
    public function show(Product $medicine)
    {
        abort_unless($medicine->is_active, 404);
        $medicine->load('image', 'gallery', 'category', 'pharmacies');
        $related = Product::publiclyVisible()->where('category_id', $medicine->category_id)
            ->where('id', '!=', $medicine->id)
            ->with('image', 'pharmacies')
            ->take(4)
            ->get();

        return view('site.medicines.show', compact('medicine', 'related'));
    }
}
