<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductWebController extends Controller
{
    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $store = auth()->user()->store;

        // Auto-create store if seller doesn't have one yet
        if (!$store) {
            $store = auth()->user()->store()->create([
                'name' => 'Toko ' . auth()->user()->name,
                'description' => 'Selamat datang di toko kami.',
            ]);
        }

        // Handle image upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                $path = $request->file('image')->store('products', 'public');
                $validated['image'] = '/storage/' . $path;
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Gagal upload foto. Coba lagi.'])->withInput();
            }
        } else {
            // Default placeholder jika ga ada image
            $validated['image'] = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=500&h=500';
        }

        $store->products()->create($validated);

        return redirect('/seller/products')->with('success', 'Produk berhasil ditambahkan ke katalog!');
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle image upload - jika ada file baru
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            try {
                $path = $request->file('image')->store('products', 'public');
                $validated['image'] = '/storage/' . $path;
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Gagal upload foto. Coba lagi.'])->withInput();
            }
        } else {
            // Jika tidak ada image baru, pertahankan image lama
            unset($validated['image']);
        }

        $product->update($validated);

        return redirect('/seller/products')->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect('/seller/products')->with('success', 'Produk telah dihapus dari katalog.');
    }
}
