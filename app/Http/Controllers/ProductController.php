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
