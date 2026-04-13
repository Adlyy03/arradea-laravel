<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * Public: list all products with store info.
     */
    public function index()
    {
        $products = Product::with('store:id,name,address')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $products,
        ]);
    }

    /**
     * GET /api/products/search
     * Public: search products by name or category using LIKE.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'max:100'],
        ]);

        $keyword = trim($validated['q']);

        $products = Product::with('store:id,name,address', 'category:id,name')
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($keyword) {
                        $categoryQuery->where('name', 'like', "%{$keyword}%");
                    });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'query' => $keyword,
            'data' => $products,
        ]);
    }

    /**
     * GET /api/products/{product}
     * Public: show product detail.
     */
    public function show(Product $product)
    {
        $product->load('store:id,name,address');

        return response()->json([
            'success' => true,
            'data'    => $product,
        ]);
    }

    /**
     * GET /api/products/updates
     * Public: lightweight updates endpoint for visible products.
     */
    public function updates(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['nullable', 'array'],
            'ids.*' => ['integer', 'exists:products,id'],
            'since' => ['nullable', 'date'],
        ]);

        $query = Product::query()->select([
            'id',
            'store_id',
            'category_id',
            'name',
            'price',
            'stock',
            'image',
            'updated_at',
        ])->with('store:id,name');

        if (!empty($validated['ids'])) {
            $query->whereIn('id', $validated['ids']);
        }

        if (!empty($validated['since'])) {
            $query->where('updated_at', '>', $validated['since']);
        }

        $products = $query->get()->map(function (Product $product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'stock' => (int) $product->stock,
                'status' => $product->stock > 0 ? 'available' : 'out_of_stock',
                'image' => $product->image,
                'store_id' => $product->store_id,
                'store_name' => $product->store?->name,
                'updated_at' => $product->updated_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * POST /api/products
     * Seller only: create a product in own store.
     */
    public function store(ProductRequest $request)
    {
        $store = $request->user()->store;

        if (! $store) {
            return response()->json([
                'success' => false,
                'message' => 'You must create a store first.',
            ], 422);
        }

        $product = $store->products()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data'    => $product,
        ], 201);
    }

    /**
     * PUT /api/products/{product}
     * Seller only: update a product that belongs to own store.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $this->authorizeOwnership($request, $product);

        $product->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data'    => $product,
        ]);
    }

    /**
     * DELETE /api/products/{product}
     * Seller only: delete a product that belongs to own store.
     */
    public function destroy(Request $request, Product $product)
    {
        $this->authorizeOwnership($request, $product);

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    /**
     * Ensure the authenticated seller owns this product.
     */
    protected function authorizeOwnership(Request $request, Product $product)
    {
        $store = $request->user()->store;

        if (! $store || $product->store_id !== $store->id) {
            abort(response()->json([
                'success' => false,
                'message' => 'This product does not belong to your store.',
            ], 403));
        }
    }
}
