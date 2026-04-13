<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the cart.
     */
    public function index()
    {
        $carts = auth()->user()->carts()->with('product.store')->get();
        $totalOriginal = 0;
        $totalFinal = 0;

        foreach ($carts as $cart) {
            $pricing = $cart->product->calculatePricing($cart->variant_key, $cart->quantity);
            $cart->pricing = $pricing;
            $totalOriginal += $pricing['total_original'];
            $totalFinal += $pricing['total_final'];
        }

        return view('buyer.cart.index', compact('carts', 'totalOriginal', 'totalFinal'));
    }

    /**
     * Add product to cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_key' => 'nullable|string|max:120',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $variantKey = $request->input('variant_key', 'default');

        if ($variantKey !== 'default' && ! $product->getVariant($variantKey)) {
            return back()->withErrors(['variant_key' => 'Varian produk tidak valid.']);
        }

        if ($product->stock < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi.']);
        }

        $cart = auth()->user()->carts()
            ->where('product_id', $request->product_id)
            ->where('variant_key', $variantKey)
            ->first();

        if ($cart) {
            $cart->increment('quantity', $request->quantity);
        } else {
            auth()->user()->carts()->create([
                'product_id' => $request->product_id,
                'variant_key' => $variantKey,
                'quantity'   => $request->quantity,
            ]);
        }

        return back()->with('success', 'Produk ditambahkan ke keranjang!');
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($cart->product->stock < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi.']);
        }

        $cart->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Keranjang diperbarui!');
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        $cart->delete();

        return back()->with('success', 'Item dihapus dari keranjang!');
    }

    /**
     * Checkout cart to orders.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $carts = auth()->user()->carts()->with('product')->get();

        if ($carts->isEmpty()) {
            return back()->withErrors(['cart' => 'Keranjang kosong.']);
        }

        foreach ($carts as $cart) {
            // Validate product exists and has reasonable price
            if (!$cart->product || $cart->product->price <= 0 || $cart->product->price > 100000000) {
                $productName = $cart->product?->name ?? 'produk';
                return back()->withErrors(['price' => "Harga {$productName} tidak valid."]);
            }

            if ($cart->product->store && (int) $cart->product->store->user_id === (int) auth()->id()) {
                return back()->withErrors(['cart' => "Anda tidak bisa membeli produk milik toko Anda sendiri ({$cart->product->name})."]);
            }

            // Validate quantity is reasonable
            if ($cart->quantity <= 0 || $cart->quantity > 1000) {
                return back()->withErrors(['quantity' => "Quantity untuk {$cart->product->name} tidak valid."]);
            }

            if ($cart->product->stock < $cart->quantity) {
                return back()->withErrors(['stock' => "Stok {$cart->product->name} tidak mencukupi."]);
            }

            if ($cart->variant_key !== 'default' && ! $cart->product->getVariant($cart->variant_key)) {
                return back()->withErrors(['variant_key' => "Varian untuk {$cart->product->name} tidak ditemukan."]);
            }
        }

        foreach ($carts as $cart) {
            $pricing = $cart->product->calculatePricing($cart->variant_key, $cart->quantity);
            $totalPrice = $pricing['total_final'];
            
            // Validate total price doesn't exceed reasonable limits
            if ($totalPrice > 999999999999999) {
                return back()->withErrors(['price' => "Total harga untuk {$cart->product->name} terlalu besar. Silakan hubungi seller."]);
            }

            $order = auth()->user()->orders()->create([
                'store_id'    => $cart->product->store_id,
                'product_id'  => $cart->product->id,
                'variant_key' => $cart->variant_key,
                'quantity'    => $cart->quantity,
                'unit_price_original' => $pricing['unit_original'],
                'unit_price_final' => $pricing['unit_final'],
                'discount_percent_applied' => $pricing['discount_percent'],
                'total_price' => $totalPrice,
                'notes'       => $validated['notes'] ?? null,
                'status'      => 'pending',
            ]);

            // Notify seller
            $seller = $cart->product->store->user;
            if ($seller) {
                $seller->notify(new \App\Notifications\NewOrderNotification($order));
            }

            $cart->product->decrement('stock', $cart->quantity);
            $cart->delete();
        }

        return redirect()->route('buyer.orders')->with('success', 'Checkout berhasil! Pesanan telah dibuat.');
    }
}
