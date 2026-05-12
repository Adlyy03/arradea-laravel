@extends('layouts.dashboard')
@section('title', 'Bukti Pembayaran — Arradea')
@section('page_title', 'Bukti Pembayaran')

@section('content')
@php
    $orders = auth()->user()->orders()
        ->where('payment_method', 'qris')
        ->with(['product', 'store'])
        ->latest()
        ->get();
    
    $statusMap = [
        'waiting_confirmation' => ['Menunggu Konfirmasi', 'bg-amber-100 text-amber-700', '⏳'],
        'paid' => ['Terkonfirmasi', 'bg-green-100 text-green-700', '✅'],
        'rejected' => ['Ditolak', 'bg-red-100 text-red-700', '❌'],
    ];
@endphp

<div class="max-w-4xl mx-auto space-y-4 fade-up">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-gray-900">💳 Bukti <span style="color:#72bf77">Pembayaran</span></h1>
        <span class="px-3 py-1 rounded-xl text-xs font-bold" style="background:rgba(114,191,119,.12);color:#3fa348">
            {{ $orders->count() }} transaksi
        </span>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4">
        <div class="flex items-center gap-3">
            <span class="text-xl">✅</span>
            <p class="font-bold text-green-900 text-sm">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
        <div class="flex items-start gap-3">
            <span class="text-xl">❌</span>
            <div class="flex-1">
                <p class="font-black text-red-900 text-sm mb-1">Terjadi Kesalahan</p>
                <ul class="text-xs text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    @forelse($orders as $order)
    @php
        [$statusLabel, $statusClass, $statusIcon] = $statusMap[$order->payment_status] ?? ['Unknown', 'bg-gray-100 text-gray-500', '❓'];
    @endphp
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-md transition">
        <div class="p-4 lg:p-5">
            <div class="flex items-start gap-4">
                {{-- Product Image --}}
                <img src="{{ $order->product->image ?? 'https://via.placeholder.com/200x200/f0faf1/72bf77?text=Produk' }}"
                     alt="{{ $order->product->name }}"
                     class="w-20 h-20 lg:w-24 lg:h-24 rounded-xl object-cover flex-shrink-0"
                     onerror="this.src='https://via.placeholder.com/200x200/f0faf1/72bf77?text=Produk'">
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-widest mb-0.5" style="color:#72bf77">
                                Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                            </p>
                            <h3 class="font-black text-gray-900 leading-tight mb-1">{{ $order->product->name }}</h3>
                            <p class="text-xs text-gray-400">{{ $order->quantity }}× · Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                        <span class="{{ $statusClass }} px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest flex-shrink-0">
                            {{ $statusIcon }} {{ $statusLabel }}
                        </span>
                    </div>
                    
                    {{-- Payment Proof --}}
                    @if($order->payment_proof)
                    <div class="mt-3 rounded-xl border border-gray-200 bg-gray-50 p-3">
                        <p class="text-xs font-bold text-gray-700 mb-2">📸 Bukti Pembayaran:</p>
                        <a href="{{ asset('storage/'.$order->payment_proof) }}" target="_blank" class="block">
                            <img src="{{ asset('storage/'.$order->payment_proof) }}" 
                                 alt="Bukti Pembayaran" 
                                 class="w-full max-w-xs rounded-lg shadow-md hover:shadow-lg transition">
                        </a>
                        <p class="text-[10px] text-gray-400 mt-2">Klik gambar untuk memperbesar</p>
                    </div>
                    @endif
                    
                    {{-- Rejection Reason --}}
                    @if($order->payment_status === 'rejected' && $order->rejected_reason)
                    <div class="mt-3 rounded-xl border border-red-200 bg-red-50 p-3">
                        <p class="text-xs font-bold text-red-900 mb-1">❌ Alasan Penolakan:</p>
                        <p class="text-xs text-red-700">{{ $order->rejected_reason }}</p>
                    </div>
                    @endif
                    
                    {{-- Actions --}}
                    <div class="mt-3 flex items-center gap-2 flex-wrap">
                        @if($order->payment_status === 'rejected')
                        <button type="button" 
                                onclick="openReuploadModal({{ $order->id }})" 
                                class="px-4 py-2 rounded-lg text-xs font-bold text-white transition hover:opacity-90" 
                                style="background:#72bf77">
                            🔄 Upload Ulang Bukti
                        </button>
                        @endif
                        
                        <a href="{{ route('chat.show', $order) }}" 
                           class="px-4 py-2 rounded-lg text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                            💬 Chat Seller
                        </a>
                        
                        <span class="text-[10px] text-gray-400 ml-auto">{{ $order->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-gray-100 flex flex-col items-center justify-center py-20 text-center">
        <span class="text-5xl mb-4">💳</span>
        <h3 class="text-xl font-black text-gray-900 mb-2">Belum ada pembayaran QRIS</h3>
        <p class="text-sm text-gray-400 mb-6">Transaksi dengan pembayaran QRIS akan muncul di sini.</p>
        <a href="{{ route('buyer.products') }}" class="px-6 py-3 rounded-2xl font-bold text-sm text-white transition hover:opacity-90" style="background:#72bf77">
            Mulai Belanja
        </a>
    </div>
    @endforelse
</div>

{{-- Reupload Modal --}}
<div id="reupload-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full">
        <div class="border-b border-gray-100 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-black text-gray-900">🔄 Upload Ulang Bukti</h3>
            <button type="button" onclick="closeReuploadModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <form id="reupload-form" action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-black text-gray-700 mb-2">Upload Bukti Baru <span class="text-red-400">*</span></label>
                <input type="file" name="payment_proof" accept="image/*" required class="w-full px-4 py-3 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl focus:outline-none focus:border-primary-500 transition file:mr-3 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-primary-600 file:text-white file:font-black file:text-xs">
                <p class="text-[10px] text-gray-400 mt-2">Format: JPG, PNG, WebP. Maksimal 4MB.</p>
            </div>
            
            <button type="submit" class="w-full py-3 rounded-xl font-black text-base text-white transition hover:opacity-90 active:scale-95" style="background:#72bf77">
                ✅ Kirim Bukti Baru
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openReuploadModal(orderId) {
    const modal = document.getElementById('reupload-modal');
    const form = document.getElementById('reupload-form');
    form.action = `/buyer/payments/${orderId}/reupload`;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeReuploadModal() {
    document.getElementById('reupload-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Close on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeReuploadModal();
    }
});

// Close on backdrop click
document.getElementById('reupload-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReuploadModal();
    }
});
</script>
@endpush
@endsection
