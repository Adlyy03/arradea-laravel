<div class="space-y-3">
    @forelse($orders as $order)
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-sm transition">
        <div class="p-4 flex items-start gap-3">
            <img src="{{ $order->product->image ?? 'https://via.placeholder.com/60x60/f0faf1/72bf77?text=P' }}"
                 alt="{{ $order->product->name }}"
                 class="w-14 h-14 lg:w-16 lg:h-16 rounded-lg object-cover flex-shrink-0 border border-gray-100"
                 onerror="this.src='https://via.placeholder.com/60x60/f0faf1/72bf77?text=P'">
            
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">
                            Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                        <h3 class="font-black text-gray-900 text-sm lg:text-base leading-tight">{{ $order->product->name }}</h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider bg-green-100 text-green-700 flex-shrink-0">
                        ✅ Disetujui
                    </span>
                </div>
                
                <div class="flex items-center gap-3 text-xs text-gray-500 mb-2">
                    <span class="font-bold">{{ $order->user->name ?? 'Buyer' }}</span>
                    <span>·</span>
                    <span>{{ $order->quantity }}× · Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex items-center gap-2 text-[10px] text-gray-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Disetujui {{ $order->paid_at ? $order->paid_at->diffForHumans() : $order->updated_at->diffForHumans() }}</span>
                </div>
            </div>
            
            @if($order->payment_proof)
            <a href="{{ asset('storage/'.$order->payment_proof) }}" target="_blank" class="flex-shrink-0">
                <img src="{{ asset('storage/'.$order->payment_proof) }}" 
                     alt="Bukti" 
                     class="w-12 h-12 lg:w-14 lg:h-14 rounded-lg object-cover border border-gray-200 hover:border-emerald-500 transition">
            </a>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-dashed border-gray-200 p-10 text-center">
        <span class="text-4xl mb-3 block">📋</span>
        <p class="text-base font-black text-gray-900">Belum ada pembayaran disetujui</p>
        <p class="text-sm text-gray-500 mt-1">Riwayat pembayaran yang disetujui akan muncul di sini.</p>
    </div>
    @endforelse
</div>
