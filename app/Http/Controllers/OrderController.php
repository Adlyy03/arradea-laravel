<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * GET /api/orders
     * Buyer → own orders. Seller → incoming orders for own store.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'seller') {
            $store = $user->store;

            if (! $store) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have a store yet.',
                ], 404);
            }

            $orders = $store->orders()
                ->with('user:id,name,email')
                ->latest()
                ->paginate(15);
        } else {
            $orders = $user->orders()
                ->with('store:id,name')
                ->latest()
                ->paginate(15);
        }

        return response()->json([
            'success' => true,
            'data'    => $orders,
        ]);
    }

    /**
     * GET /api/orders/{order}
     * Buyer (own order) or Seller (order in own store) can view.
     */
    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        $canView = ($user->role === 'buyer' && $order->user_id === $user->id)
            || ($user->role === 'seller' && $user->store && $order->store_id === $user->store->id);

        if (! $canView) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to view this order.',
            ], 403);
        }

        $order->load('user:id,name,email', 'store:id,name');

        return response()->json([
            'success' => true,
            'data'    => $order,
        ]);
    }

    /**
     * POST /api/orders
     * Buyer only: create a new order.
     */
    public function store(OrderRequest $request)
    {
        $user = $request->user();

        if ($request->filled('product_id')) {
            $product = Product::findOrFail($request->product_id);

            if ($product->stock < $request->quantity) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok produk tidak mencukupi.',
                    ], 422);
                }

                return back()->withInput()->withErrors(['quantity' => 'Stok produk tidak mencukupi.']);
            }

            $order = $user->orders()->create([
                'store_id'    => $product->store_id,
                'product_id'  => $product->id,
                'quantity'    => $request->quantity,
                'total_price' => $product->price * $request->quantity,
                'status'      => 'pending',
            ]);

            $product->decrement('stock', $request->quantity);
        } else {
            $order = $user->orders()->create([
                'store_id'    => $request->store_id,
                'total_price' => $request->total_price,
                'status'      => 'pending',
            ]);
        }

        $order->load('store:id,name', 'product:id,name');

        // Notify Seller
        $seller = $order->store->user ?? null;
        if ($seller) {
            $seller->notify(new \App\Notifications\NewOrderNotification($order));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully.',
                'data'    => $order,
            ], 201);
        }

        return redirect('/orders')->with('success', 'Order berhasil dibuat!');
    }

    /**
     * PUT /api/orders/{order}/status
     * Seller only: update order status to accepted | rejected | done.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:accepted,rejected,done'],
        ]);

        $store = $request->user()->store;

        if (! $store || $order->store_id !== $store->id) {
            return response()->json([
                'success' => false,
                'message' => 'This order does not belong to your store.',
            ], 403);
        }

        $order->update(['status' => $request->status]);

        // Notify buyer
        if ($order->user) {
            $order->user->notify(new \App\Notifications\OrderStatusNotification($order));
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated.',
            'data'    => $order,
        ]);
    }
}
