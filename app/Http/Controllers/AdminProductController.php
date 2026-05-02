<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    /**
     * Display a listing of all products (Admin only)
     */
    public function index(Request $request)
    {
        $query = Product::with(['store.user', 'category'])
            ->latest();

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out') {
                $query->where('stock', 0);
            } elseif ($request->stock_status === 'low') {
                $query->where('stock', '>', 0)->where('stock', '<=', 10);
            } elseif ($request->stock_status === 'available') {
                $query->where('stock', '>', 10);
            }
        }

        $products = $query->paginate(20)->withQueryString();

        // Get filters data
        $stores = Store::whereHas('user', function($q) {
            $q->where('is_seller', true);
        })->orderBy('name')->get();

        $categories = Category::orderBy('name')->get();

        // Stats
        $totalProducts = Product::count();
        $outOfStock = Product::where('stock', 0)->count();
        $lowStock = Product::where('stock', '>', 0)->where('stock', '<=', 10)->count();
        $totalValue = Product::sum(\DB::raw('price * stock'));

        return view('admin.products.index', compact(
            'products',
            'stores',
            'categories',
            'totalProducts',
            'outOfStock',
            'lowStock',
            'totalValue'
        ));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $product->load(['store', 'category']);
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['discount_percent'] = (float) ($validated['discount_percent'] ?? 0);

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        // Check if product has orders
        $ordersCount = $product->orders()->count();
        
        if ($ordersCount > 0) {
            return back()->withErrors([
                'message' => "Produk tidak dapat dihapus karena memiliki {$ordersCount} pesanan terkait."
            ]);
        }

        $storeName = $product->store->name ?? 'Unknown';
        $productName = $product->name;

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$productName}' dari toko '{$storeName}' berhasil dihapus!");
    }

    /**
     * Bulk update stock
     */
    public function bulkUpdateStock(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array'],
            'product_ids.*' => ['exists:products,id'],
            'action' => ['required', 'in:add,subtract,set'],
            'value' => ['required', 'integer', 'min:0'],
        ]);

        $products = Product::whereIn('id', $validated['product_ids'])->get();

        foreach ($products as $product) {
            switch ($validated['action']) {
                case 'add':
                    $product->stock += $validated['value'];
                    break;
                case 'subtract':
                    $product->stock = max(0, $product->stock - $validated['value']);
                    break;
                case 'set':
                    $product->stock = $validated['value'];
                    break;
            }
            $product->save();
        }

        return back()->with('success', count($validated['product_ids']) . ' produk berhasil diperbarui!');
    }

    /**
     * Bulk delete products
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array'],
            'product_ids.*' => ['exists:products,id'],
        ]);

        // Check if any product has orders
        $productsWithOrders = Product::whereIn('id', $validated['product_ids'])
            ->has('orders')
            ->count();

        if ($productsWithOrders > 0) {
            return back()->withErrors([
                'message' => "{$productsWithOrders} produk tidak dapat dihapus karena memiliki pesanan terkait."
            ]);
        }

        Product::whereIn('id', $validated['product_ids'])->delete();

        return back()->with('success', count($validated['product_ids']) . ' produk berhasil dihapus!');
    }
}
