@extends('layouts.dashboard')

@section('title', 'Chat - Arradea')
@section('page_title', 'Chat dengan ' . ($order->user_id === auth()->id() ? $order->store->user->name : $order->user->name))

@section('content')
<div class="space-y-6 lg:space-y-12">
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-8 lg:p-6 lg:p-12 shadow-sm border border-gray-100">
        <div class="mb-8 flex justify-between items-start">
            <div>
                <h3 class="text-xl lg:text-2xl font-black text-gray-900">Order #{{ $order->id }}</h3>
                <p class="text-xs lg:text-sm text-gray-500 font-bold uppercase tracking-widest mt-1">{{ $order->product->name }} ({{ $order->quantity }}x)</p>
            </div>
            <span class="px-4 py-2 bg-primary-50 text-primary-600 rounded-xl text-[10px] font-black uppercase tracking-widest">{{ $order->status }}</span>
        </div>

        <div class="flex flex-col h-[60vh] lg:h-96">
            <div id="messages" class="flex-1 overflow-y-auto p-4 lg:p-6 bg-gray-50/50 rounded-2xl lg:rounded-2xl lg:rounded-3xl mb-6 space-y-4">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] lg:max-w-xs px-5 py-3 rounded-2xl {{ $message->sender_id === auth()->id() ? 'bg-primary-600 text-white shadow-lg shadow-primary-100' : 'bg-white text-gray-900 border border-gray-100 shadow-sm' }}">
                            <p class="text-sm font-medium leading-relaxed">{{ $message->message }}</p>
                            <p class="text-[9px] opacity-70 mt-1 font-bold text-right">{{ $message->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-6 lg:py-12">
                        <div class="text-4xl mb-2">💬</div>
                        <p class="text-xs font-black uppercase tracking-widest">Belum ada pesan</p>
                    </div>
                @endforelse
            </div>

            <form action="{{ route('chat.store', $chat) }}" method="POST" class="flex gap-3">
                @csrf
                <input type="text" name="message" placeholder="Ketik pesan..." class="flex-1 h-14 bg-gray-50 border-none rounded-2xl px-6 focus:ring-2 focus:ring-primary-600 font-bold text-sm lg:text-base transition-all" required>
                <button type="submit" class="w-14 h-14 lg:w-auto lg:px-8 bg-primary-600 text-white rounded-2xl hover:bg-primary-700 shadow-lg shadow-primary-100 transition flex items-center justify-center">
                    <svg class="w-5 h-5 lg:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="hidden lg:inline font-black uppercase tracking-widest text-xs">Kirim</span>
                </button>
            </form>
        </div>

        <div class="mt-6">
            <a href="{{ auth()->user()->role === 'buyer' ? route('buyer.orders') : '/seller/orders' }}" class="text-primary-600 hover:text-primary-700">← Kembali ke Orders</a>
        </div>
    </div>
</div>

<script>
    // Auto scroll to bottom
    document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;

    // Simple polling for new messages (every 5 seconds)
    setInterval(function() {
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => response.text()).then(html => {
            // This is a simple implementation. In production, use WebSockets or better polling.
            // For now, just refresh the page if new messages might be there.
        });
    }, 5000);
</script>
@endsection