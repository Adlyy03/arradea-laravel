<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
    /**
     * Display the cart.
     */
    public function index()
    {
        // Admin tidak bisa belanja
        if (auth()->user()->role === 'admin') {
            abort(403, 'Admin tidak memiliki akses ke fitur belanja.');
        }

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
        // Admin tidak bisa belanja
        if (auth()->user()->role === 'admin') {
            return back()->withErrors(['access' => 'Admin tidak memiliki akses ke fitur belanja.']);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_key' => 'nullable|string|max:120',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $variantKey = $request->input('variant_key', 'default');

        if (!$product->is_active) {
            return back()->withErrors(['product_id' => 'Produk tidak tersedia.']);
        }

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
        // Admin tidak bisa belanja
        if (auth()->user()->role === 'admin') {
            return back()->withErrors(['access' => 'Admin tidak memiliki akses ke fitur belanja.']);
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod,qris'],
            'payment_proof_base64' => ['nullable', 'string'],
        ], [
            'payment_method.required' => 'Pilih metode pembayaran.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
        ]);

        $carts = auth()->user()->carts()->with('product')->get();

        if ($carts->isEmpty()) {
            return back()->withErrors(['cart' => 'Keranjang kosong.']);
        }

        $paymentMethod = $validated['payment_method'];
        $uniqueStoreIds = $carts->pluck('product.store_id')->unique()->values();

        if ($paymentMethod === 'qris' && $uniqueStoreIds->count() !== 1) {
            return back()->withErrors(['payment_method' => 'Pembayaran QRIS manual hanya bisa untuk keranjang dengan 1 seller.']);
        }

        $qrisSeller = null;
        if ($paymentMethod === 'qris') {
            $qrisSeller = $carts->first()?->product?->store?->user;

            if (! $qrisSeller || ! $qrisSeller->hasQrisPaymentSetup()) {
                return back()->withErrors(['payment_method' => 'Seller belum menambahkan QRIS. Silakan pilih COD atau checkout ke seller lain.']);
            }

            if (empty($validated['payment_proof_base64'])) {
                return back()->withErrors(['payment_proof' => 'Bukti pembayaran QRIS wajib diunggah saat checkout.']);
            }
        }

        $paymentProofPath = null;
        if ($paymentMethod === 'qris' && !empty($validated['payment_proof_base64'])) {
            // Decode base64 image
            $base64Image = $validated['payment_proof_base64'];
            
            // Extract image data
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif, etc.
                
                if (!in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                    return back()->withErrors(['payment_proof' => 'Format gambar tidak valid. Gunakan JPG, PNG, atau WebP.']);
                }
                
                $base64Image = str_replace(' ', '+', $base64Image);
                $imageData = base64_decode($base64Image);
                
                if ($imageData === false) {
                    return back()->withErrors(['payment_proof' => 'Gagal memproses gambar. Silakan coba lagi.']);
                }
                
                // Save to storage
                $fileName = 'payment_' . time() . '_' . uniqid() . '.' . $type;
                $path = 'payments/' . $fileName;
                Storage::disk('public')->put($path, $imageData);
                $paymentProofPath = $path;
            } else {
                return back()->withErrors(['payment_proof' => 'Format gambar tidak valid.']);
            }
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

        $createdOrders = [];
        
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
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'qris' ? 'waiting_confirmation' : 'paid',
                'payment_proof'  => $paymentProofPath,
            ]);

            $createdOrders[] = $order;

            // Notify seller
            $seller = $cart->product->store->user;
            if ($seller) {
                $seller->notify(new \App\Notifications\NewOrderNotification($order));
                if ($paymentMethod === 'qris') {
                    $seller->notify(new \App\Notifications\OrderPaymentNotification($order, 'submitted'));
                }
            }

            $cart->product->decrement('stock', $cart->quantity);
            $cart->delete();
        }

        // Auto-create chat for QRIS orders
        if ($paymentMethod === 'qris' && $qrisSeller && !empty($createdOrders)) {
            foreach ($createdOrders as $order) {
                // Check if chat already exists
                $chat = \App\Models\Chat::where('order_id', $order->id)->first();
                
                if (!$chat) {
                    // Create new chat
                    $chat = \App\Models\Chat::create([
                        'order_id' => $order->id,
                        'buyer_id' => auth()->id(),
                        'seller_id' => $qrisSeller->id,
                    ]);
                }
                
                // Send payment proof as message
                $message = "📸 Bukti pembayaran QRIS untuk pesanan #{$order->id}\n\n";
                $message .= "Total: Rp " . number_format($order->total_price, 0, ',', '.') . "\n";
                $message .= "Produk: {$order->product->name}\n\n";
                $message .= "[BUKTI_PEMBAYARAN:{$paymentProofPath}]";
                
                $chatMessage = \App\Models\Message::create([
                    'chat_id' => $chat->id,
                    'sender_id' => auth()->id(),
                    'message' => $message,
                    'is_read' => false,
                ]);
                
                // Notify seller about new message
                $qrisSeller->notify(new \App\Notifications\ChatMessageNotification($chatMessage));
            }
        }

        return redirect()->route('buyer.orders')->with('success', 'Checkout berhasil! Pesanan telah dibuat.' . ($paymentMethod === 'qris' ? ' Bukti pembayaran telah dikirim ke seller via chat.' : ''));
    }
}
