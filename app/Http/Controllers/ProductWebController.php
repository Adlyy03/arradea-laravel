<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_start_at' => 'nullable|date',
            'discount_end_at' => 'nullable|date|after_or_equal:discount_start_at',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'variants_json' => 'nullable|json',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['discount_percent'] = (float) ($validated['discount_percent'] ?? 0);
        $validated['variants'] = $this->normalizeVariantsFromJson($validated['variants_json'] ?? null);
        unset($validated['variants_json']);

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
        $store = auth()->user()->store;

        if (! $store || (int) $product->store_id !== (int) $store->id) {
            abort(403, 'Akses ditolak. Produk bukan milik toko Anda.');
        }
        
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_start_at' => 'nullable|date',
            'discount_end_at' => 'nullable|date|after_or_equal:discount_start_at',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'variants_json' => 'nullable|json',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['discount_percent'] = (float) ($validated['discount_percent'] ?? 0);
        $validated['variants'] = $this->normalizeVariantsFromJson($validated['variants_json'] ?? null);
        unset($validated['variants_json']);

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

        $store = auth()->user()->store;
        if (! $store || (int) $product->store_id !== (int) $store->id) {
            abort(403, 'Akses ditolak. Produk bukan milik toko Anda.');
        }

        $product->delete();

        return redirect('/seller/products')->with('success', 'Produk telah dihapus dari katalog.');
    }

    /**
     * Normalize variants payload from textarea JSON into a safe array.
     */
    protected function normalizeVariantsFromJson(?string $rawVariants): array
    {
        if (! $rawVariants) {
            return [];
        }

        $decoded = json_decode($rawVariants, true);
        if (! is_array($decoded)) {
            return [];
        }

        $normalized = [];
        foreach ($decoded as $index => $variant) {
            if (! is_array($variant) || empty($variant['name']) || ! isset($variant['price'])) {
                continue;
            }

            $name = trim((string) $variant['name']);
            $key = $variant['key'] ?? (\Illuminate\Support\Str::slug($name) . '-' . ($index + 1));

            $normalized[] = [
                'key' => (string) $key,
                'name' => $name,
                'price' => (float) $variant['price'],
                'discount_percent' => min(100, max(0, (float) ($variant['discount_percent'] ?? 0))),
                'discount_start_at' => $variant['discount_start_at'] ?? null,
                'discount_end_at' => $variant['discount_end_at'] ?? null,
            ];
        }

        return $normalized;
    }
}
