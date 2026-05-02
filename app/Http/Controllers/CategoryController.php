<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories (Admin only)
     */
    public function index()
    {
        $categories = \Illuminate\Support\Facades\Cache::remember(
            'admin:categories:list',
            600, // 10 minutes
            function () {
                return Category::select(['id', 'name', 'slug', 'parent_id', 'sort_order', 'is_featured'])
                    ->withCount('products')
                    ->with('parent:id,name')
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get();
            }
        );

        // Get products count per store for each category
        $categoriesWithStores = $categories->map(function ($category) {
            $storesData = Product::where('category_id', $category->id)
                ->select(['id', 'store_id'])
                ->with('store:id,name')
                ->get()
                ->groupBy('store_id')
                ->map(function ($products, $storeId) {
                    return [
                        'store_name' => $products->first()->store->name ?? 'Unknown',
                        'product_count' => $products->count(),
                    ];
                })
                ->values();

            $category->stores_data = $storesData;
            return $category;
        });

        return view('admin.categories.index', [
            'categories' => $categoriesWithStores
        ]);
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', [
            'parentCategories' => $parentCategories
        ]);
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set default values
        $validated['is_featured'] = $request->has('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Category::create($validated);

        // Clear cache
        \App\Services\CacheService::clearCategoryCache();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', [
            'category' => $category,
            'parentCategories' => $parentCategories
        ]);
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug,' . $category->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set default values
        $validated['is_featured'] = $request->has('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $category->update($validated);

        // Clear cache
        \App\Services\CacheService::clearCategoryCache();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return back()->withErrors([
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki ' . $category->products()->count() . ' produk.'
            ]);
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return back()->withErrors([
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki sub-kategori.'
            ]);
        }

        $category->delete();

        // Clear cache
        \App\Services\CacheService::clearCategoryCache();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}
