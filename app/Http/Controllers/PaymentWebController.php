<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Notifications\OrderPaymentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentWebController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $store = $user->store;

        if (! $user->is_seller || ! $store) {
            abort(403, 'Hanya seller yang dapat mengakses halaman ini.');
        }

        $orders = $store->orders()
            ->with(['user', 'product'])
            ->where('payment_method', 'qris')
            ->where('payment_status', 'waiting_confirmation')
            ->latest()
            ->get();

        return view('seller.payments.index', compact('orders', 'store'));
    }

    public function uploadProof(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->payment_method !== 'qris') {
            return back()->withErrors(['payment_method' => 'Pesanan ini tidak menggunakan pembayaran QRIS.']);
        }

        $validated = $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($order->payment_proof && Storage::disk('public')->exists($order->payment_proof)) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payments', 'public');

        $order->update([
            'payment_proof' => $path,
            'payment_status' => 'waiting_confirmation',
            'rejected_reason' => null,
        ]);

        $seller = $order->store?->user;
        if ($seller) {
            $seller->notify(new OrderPaymentNotification($order, 'submitted'));
        }

        return back()->with('success', 'Bukti pembayaran berhasil diunggah dan menunggu konfirmasi seller.');
    }

    public function approve(Request $request, Order $order)
    {
        $store = $request->user()->store;

        if (! $store || $order->store_id !== $store->id) {
            abort(403);
        }

        if ($order->payment_method !== 'qris') {
            return back()->withErrors(['payment_method' => 'Hanya pesanan QRIS yang dapat dikonfirmasi.']);
        }

        if ($order->payment_status !== 'waiting_confirmation') {
            return back()->withErrors(['payment_status' => 'Status pembayaran belum siap untuk dikonfirmasi.']);
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'rejected_reason' => null,
                'status' => 'processing',
            ]);
        });

        if ($order->user) {
            $order->user->notify(new OrderPaymentNotification($order, 'approved'));
        }

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function reject(Request $request, Order $order)
    {
        $validated = $request->validate([
            'rejected_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $store = $request->user()->store;

        if (! $store || $order->store_id !== $store->id) {
            abort(403);
        }

        if ($order->payment_method !== 'qris') {
            return back()->withErrors(['payment_method' => 'Hanya pesanan QRIS yang dapat ditolak.']);
        }

        if ($order->payment_status !== 'waiting_confirmation') {
            return back()->withErrors(['payment_status' => 'Status pembayaran belum siap untuk ditolak.']);
        }

        DB::transaction(function () use ($order, $validated) {
            // Tandai bukti pembayaran sebagai ditolak, biarkan pesanan tetap aktif
            $order->update([
                'payment_status' => 'rejected',
                'paid_at' => null,
                'rejected_reason' => $validated['rejected_reason'] ?? null,
            ]);
        });

        if ($order->user) {
            $order->user->notify(new OrderPaymentNotification($order, 'rejected'));
        }

        return back()->with('success', 'Pembayaran ditolak. Pembeli dapat mengunggah ulang bukti pembayaran.');
    }

    public function reuploadProof(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->payment_method !== 'qris') {
            return back()->withErrors(['payment_method' => 'Pesanan ini tidak menggunakan pembayaran QRIS.']);
        }

        if ($order->payment_status !== 'rejected') {
            return back()->withErrors(['payment_status' => 'Hanya bukti pembayaran yang ditolak yang dapat diupload ulang.']);
        }

        $validated = $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'payment_proof.required' => 'Bukti pembayaran wajib diunggah.',
            'payment_proof.image' => 'File harus berupa gambar.',
            'payment_proof.mimes' => 'Format gambar harus JPG, PNG, atau WebP.',
            'payment_proof.max' => 'Ukuran gambar maksimal 4MB.',
        ]);

        // Delete old proof
        if ($order->payment_proof && Storage::disk('public')->exists($order->payment_proof)) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        // Upload new proof
        $path = $request->file('payment_proof')->store('payments', 'public');

        $order->update([
            'payment_proof' => $path,
            'payment_status' => 'waiting_confirmation',
            'rejected_reason' => null,
        ]);

        // Send to chat
        $chat = \App\Models\Chat::where('order_id', $order->id)->first();
        if ($chat) {
            $message = "📸 Bukti pembayaran baru untuk pesanan #{$order->id}\n\n";
            $message .= "Total: Rp " . number_format($order->total_price, 0, ',', '.') . "\n";
            $message .= "Produk: {$order->product->name}\n\n";
            $message .= "[BUKTI_PEMBAYARAN:{$path}]";
            
            $chatMessage = \App\Models\Message::create([
                'chat_id' => $chat->id,
                'sender_id' => auth()->id(),
                'message' => $message,
                'is_read' => false,
            ]);
            
            // Notify seller about new message
            $seller = $order->store?->user;
            if ($seller) {
                $seller->notify(new \App\Notifications\ChatMessageNotification($chatMessage));
            }
        }

        // Notify seller about payment resubmission
        $seller = $order->store?->user;
        if ($seller) {
            $seller->notify(new OrderPaymentNotification($order, 'resubmitted'));
        }

        return redirect()->route('buyer.payments')->with('success', 'Bukti pembayaran baru berhasil diunggah dan dikirim ke seller via chat.');
    }
}