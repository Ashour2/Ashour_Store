<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('products')->latest('id')->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $category = new Category();
        return view('admin.categories.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'icon_code' => 'nullable|string|max:100',
        ]);

        $name = [
            'en' => $request->name_en,
            'ar' => $request->name_ar,
        ];

        $desc = [
            'en' => $request->description_en,
            'ar' => $request->description_ar,
        ];

        $category = Category::create([
            // 'name' => $request->name,
            // 'description' => $request->description
            'name' => json_encode($name, JSON_UNESCAPED_UNICODE),
            'description' => json_encode($desc, JSON_UNESCAPED_UNICODE),
            'icon_code' => $request->icon_code,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('msg', 'Category added successfully')
            ->with('type', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        // $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'icon_code' => 'nullable|string|max:100',
        ]);

        $desc = [
            'en' => $request->description_en,
            'ar' => $request->description_ar,
        ];
        $name = [
            'en' => $request->name_en,
            'ar' => $request->name_ar,
        ];

        $category->update([
            'name' => json_encode($name, JSON_UNESCAPED_UNICODE),
            'description' => json_encode($desc, JSON_UNESCAPED_UNICODE),
            'icon_code' => $request->icon_code,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('msg', 'Category updated successfully')
            ->with('type', 'info');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('msg', 'Category deleted successfully')
            ->with('type', 'danger');
    }
}
