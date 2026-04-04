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
            'name' => 'required',
            // 'name_ar' => 'required',
            'image' => 'required'
        ]);

        $data = $request->except('_token', 'image');

        // dd($request->all());

        // $desc = [
        //     'en' => $request->description_en,
        //     'ar' => $request->description_ar,
        // ];

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description
            // json_encode($desc, JSON_UNESCAPED_UNICODE)
        ]);

        // Add image to relation
        $img_name = rand().time().$request->file('image')->getClientOriginalName();
        $request->file('image')->move(public_path('images'), $img_name);

        $category->image()->create([
            'path' => $img_name
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
            'name' => 'required',
            // 'name_ar' => 'required'
            // 'image'=>'required'
        ]);

        // $desc = [
        //     'en' => $request->description_en,
        //     'ar' => $request->description_ar,
        // ];

        $category->update([
            'name' => $request->name,
            'description' => $request->description
            // json_encode($desc, JSON_UNESCAPED_UNICODE)
        ]);

        // Add image to relation

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
