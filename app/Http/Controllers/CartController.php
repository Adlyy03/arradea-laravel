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
        $total = $carts->sum(fn($cart) => $cart->product->price * $cart->quantity);

        return view('buyer.cart.index', compact('carts', 'total'));
    }

    /**
     * Add product to cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi.']);
        }

        $cart = auth()->user()->carts()->where('product_id', $request->product_id)->first();

        if ($cart) {
            $cart->increment('quantity', $request->quantity);
        } else {
            auth()->user()->carts()->create([
                'product_id' => $request->product_id,
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
    public function checkout()
    {
        $carts = auth()->user()->carts()->with('product')->get();

        if ($carts->isEmpty()) {
            return back()->withErrors(['cart' => 'Keranjang kosong.']);
        }

        foreach ($carts as $cart) {
            // Validate product exists and has reasonable price
            if (!$cart->product || $cart->product->price <= 0 || $cart->product->price > 100000000) {
                return back()->withErrors(['price' => "Harga produk {$cart->product->name} tidak valid."]);
            }

            // Validate quantity is reasonable
            if ($cart->quantity <= 0 || $cart->quantity > 1000) {
                return back()->withErrors(['quantity' => "Quantity untuk {$cart->product->name} tidak valid."]);
            }

            if ($cart->product->stock < $cart->quantity) {
                return back()->withErrors(['stock' => "Stok {$cart->product->name} tidak mencukupi."]);
            }
        }

        foreach ($carts as $cart) {
            $totalPrice = $cart->product->price * $cart->quantity;
            
            // Validate total price doesn't exceed reasonable limits
            if ($totalPrice > 999999999999999) {
                return back()->withErrors(['price' => "Total harga untuk {$cart->product->name} terlalu besar. Silakan hubungi seller."]);
            }

            $order = auth()->user()->orders()->create([
                'store_id'    => $cart->product->store_id,
                'product_id'  => $cart->product->id,
                'quantity'    => $cart->quantity,
                'total_price' => $totalPrice,
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
