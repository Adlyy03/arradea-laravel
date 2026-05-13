<div class="space-y-4">
    @forelse($orders as $order)
    <div class="bg-gray-50 rounded-xl lg:rounded-2xl border border-gray-200 overflow-hidden hover:shadow-md transition">
        {{-- Header --}}
        <div class="p-4 lg:p-5 bg-white border-b border-gray-100">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <img src="{{ $order->product->image ?? 'https://via.placeholder.com/80x80/f0faf1/72bf77?text=P' }}"
                         alt="{{ $order->product->name }}"
                         class="w-16 h-16 lg:w-20 lg:h-20 rounded-xl object-cover flex-shrink-0 border border-gray-100"
                         onerror="this.src='https://via.placeholder.com/80x80/f0faf1/72bf77?text=P'">
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">
                            Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                        <h3 class="font-black text-gray-900 text-base lg:text-lg leading-tight mb-1">{{ $order->product->name }}</h3>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span class="font-bold">{{ $order->user->name ?? 'Buyer' }}</span>
                            <span>·</span>
                            <span>{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                <span class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-700 flex-shrink-0">
                    ⏳ Menunggu
                </span>
            </div>
        </div>

        {{-- Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
            {{-- Left: Actions --}}
            <div class="p-4 lg:p-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-white p-3 border border-gray-200">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total</p>
                        <p class="text-lg lg:text-xl font-black text-gray-900 mt-1">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-xl bg-white p-3 border border-gray-200">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Qty</p>
                        <p class="text-lg lg:text-xl font-black text-gray-900 mt-1">{{ $order->quantity }}×</p>
                    </div>
                </div>

                @if($order->notes)
                <div class="rounded-xl border border-gray-200 p-3 bg-white">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">💬 Catatan Buyer</p>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $order->notes }}</p>
                </div>
                @endif

                <form action="{{ route('seller.payments.approve', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 rounded-xl bg-emerald-600 text-white font-black text-sm hover:bg-emerald-700 transition active:scale-95 shadow-md">
                        ✅ Setujui Pembayaran
                    </button>
                </form>

                <form action="{{ route('seller.payments.reject', $order) }}" method="POST" class="space-y-2">
                    @csrf
                    <textarea name="rejected_reason" 
                              rows="2" 
                              placeholder="Alasan penolakan (opsional)" 
                              class="w-full px-3 py-2 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
                    <button type="submit" class="w-full px-4 py-3 rounded-xl bg-red-600 text-white font-black text-sm hover:bg-red-700 transition active:scale-95">
                        ❌ Tolak Pembayaran
                    </button>
                </form>
            </div>

            {{-- Right: Proof --}}
            <div class="p-4 lg:p-5 lg:border-l border-gray-100 bg-white">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-3">📸 Bukti Transfer</p>
                @if($order->payment_proof)
                <a href="{{ asset('storage/'.$order->payment_proof) }}" target="_blank" class="block rounded-xl overflow-hidden border-2 border-gray-200 hover:border-emerald-500 transition shadow-sm">
                    <img src="{{ asset('storage/'.$order->payment_proof) }}" 
                         alt="Bukti pembayaran" 
                         class="w-full h-auto object-cover">
                </a>
                <p class="text-[10px] text-gray-400 mt-2 text-center">Klik untuk memperbesar</p>
                @else
                <div class="rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                    <span class="text-4xl mb-2 block">🧾</span>
                    <p class="text-sm font-bold text-gray-400">Belum ada bukti</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-dashed border-gray-200 p-10 text-center">
        <span class="text-5xl mb-3 block">✅</span>
        <p class="text-lg font-black text-gray-900">Tidak ada pembayaran menunggu</p>
        <p class="text-sm text-gray-500 mt-1">Semua bukti QRIS sudah diproses.</p>
    </div>
    @endforelse
</div>
