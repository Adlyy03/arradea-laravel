@extends('layouts.dashboard')

@section('title', 'Chat - Arradea')
@section('page_title', 'Chat')

@section('content')
@php
    $withName = $order->user_id === auth()->id()
        ? ($order->store->user->name ?? 'Penjual')
        : ($order->user->name ?? 'Pembeli');
    $isSellerView = $order->user_id !== auth()->id();
    $statusMap = [
        'pending'    => ['Menunggu',  'bg-amber-100 text-amber-700'],
        'accepted'   => ['Diproses',  'bg-blue-100 text-blue-700'],
        'done'       => ['Selesai',   'bg-green-100 text-green-700'],
        'rejected'   => ['Ditolak',   'bg-red-100 text-red-700'],
        'dibatalkan' => ['Dibatalkan','bg-gray-100 text-gray-500'],
    ];
    [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status, 'bg-gray-100 text-gray-500'];
    $backRoute = $order->user_id === auth()->id() ? route('buyer.orders') : route('seller.orders');
@endphp

<div class="max-w-2xl mx-auto space-y-4 fade-up">

    {{-- Back --}}
    <a href="{{ $backRoute }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-gray-900 transition group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali
    </a>

    {{-- Chat Header --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        {{-- Top bar --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-50" style="background:linear-gradient(to right,#f0faf1,#fff)">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-sm font-black flex-shrink-0"
                     style="background:rgba(114,191,119,.15);color:#3fa348">
                    {{ strtoupper(substr($withName, 0, 1)) }}
                </div>
                <div>
                    <p class="font-black text-gray-900 text-sm">{{ $withName }}</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} · {{ $order->product->name ?? '' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="{{ $statusClass }} px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $statusLabel }}</span>
                @if($isSellerView && $order->status === 'pending')
                <form action="/web/order/{{ $order->id }}/status" method="POST" class="inline">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="accepted">
                    <button type="submit" class="px-3 py-1.5 rounded-lg text-[10px] font-black text-white transition hover:opacity-80" style="background:#72bf77">Proses</button>
                </form>
                @endif
            </div>
        </div>

        {{-- Order Mini Info --}}
        <div class="flex items-center gap-4 px-4 py-3 bg-gray-50/50 border-b border-gray-50">
            @if($order->product?->image)
            <img src="{{ $order->product->image }}" alt="" class="w-10 h-10 rounded-xl object-cover border border-gray-100 flex-shrink-0">
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-xs font-black text-gray-700 truncate">{{ $order->product->name ?? '—' }}</p>
                <p class="text-[10px] text-gray-400">{{ $order->quantity }}× · Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            </div>
            <span class="text-[10px] font-bold text-gray-400 flex-shrink-0">{{ $order->created_at->format('d M Y') }}</span>
        </div>

        {{-- Messages Area --}}
        <div id="messages"
             class="flex flex-col overflow-y-auto p-4 space-y-3"
             style="height:55vh;min-height:300px;max-height:520px;scroll-behavior:smooth;">

            @forelse($messages as $message)
            @php $isOwn = $message->sender_id === auth()->id(); @endphp
            <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                @if(!$isOwn)
                <div class="w-7 h-7 rounded-xl flex items-center justify-center text-[10px] font-black flex-shrink-0 mr-2 mt-auto mb-0.5"
                     style="background:rgba(114,191,119,.15);color:#3fa348">
                    {{ strtoupper(substr($withName, 0, 1)) }}
                </div>
                @endif
                <div class="max-w-[75%] {{ $isOwn ? 'items-end' : 'items-start' }} flex flex-col gap-0.5">
                    <div class="px-4 py-2.5 rounded-2xl text-sm font-medium leading-relaxed
                        {{ $isOwn
                            ? 'text-white rounded-br-md'
                            : 'bg-white border border-gray-100 text-gray-900 shadow-sm rounded-bl-md' }}"
                         style="{{ $isOwn ? 'background:#3fa348' : '' }}">
                        {{ $message->message }}
                    </div>
                    <span class="text-[9px] font-bold text-gray-400 px-1">{{ $message->created_at->diffForHumans() }}</span>
                </div>
            </div>
            @empty
            <div id="empty-chat-state" class="flex flex-col items-center justify-center h-full text-center py-8">
                <div class="w-16 h-16 rounded-3xl flex items-center justify-center mb-4" style="background:rgba(114,191,119,.1)">
                    <svg class="w-8 h-8" style="color:#72bf77" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <p class="font-black text-gray-900 text-sm">Belum ada pesan</p>
                <p class="text-xs text-gray-400 mt-1">Mulai percakapan dengan {{ $withName }}</p>
            </div>
            @endforelse

            {{-- Typing indicator (hidden) --}}
            <div id="typing-indicator" class="hidden flex justify-start">
                <div class="w-7 h-7 rounded-xl flex items-center justify-center text-[10px] font-black flex-shrink-0 mr-2 mt-auto mb-0.5" style="background:rgba(114,191,119,.15);color:#3fa348">
                    {{ strtoupper(substr($withName, 0, 1)) }}
                </div>
                <div class="px-4 py-2.5 rounded-2xl bg-white border border-gray-100 shadow-sm rounded-bl-md">
                    <div class="flex gap-1 items-center h-4">
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Message Input --}}
        <div class="p-4 border-t border-gray-100" style="background:#fafafa">
            <form id="chat-form" action="{{ route('chat.store', $chat) }}" method="POST">
                @csrf
                <div class="flex gap-2 items-end">
                    <div class="flex-1 relative">
                        <textarea id="chat-message-input"
                                  name="message"
                                  placeholder="Ketik pesan..."
                                  rows="1"
                                  class="w-full bg-white border border-gray-200 rounded-2xl px-4 py-3 text-sm font-medium text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition resize-none overflow-hidden"
                                  style="--tw-ring-color:rgba(114,191,119,.4);max-height:120px"
                                  required></textarea>
                    </div>
                    <button type="submit"
                            id="send-btn"
                            class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0 text-white transition hover:opacity-90 active:scale-95 shadow-md"
                            style="background:#72bf77;box-shadow:0 4px 14px rgba(114,191,119,.35)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
(function () {
    const chatId = Number(@json($chat->id));
    const currentUserId = Number(@json(auth()->id()));
    const csrfToken = @json(csrf_token());
    const withName = @json($withName);
    const broadcastKey = @json(env('REVERB_APP_KEY', env('PUSHER_APP_KEY')));
    const broadcastCluster = @json(env('PUSHER_APP_CLUSTER', 'mt1'));
    const broadcastHost = @json(env('REVERB_HOST', env('PUSHER_HOST', '127.0.0.1')));
    const broadcastPort = Number(@json((int) env('REVERB_PORT', env('PUSHER_PORT', 8080))));
    const broadcastScheme = @json(env('REVERB_SCHEME', env('PUSHER_SCHEME', 'http')));

    const messagesEl = document.getElementById('messages');
    const formEl = document.getElementById('chat-form');
    const inputEl = document.getElementById('chat-message-input');
    const sendBtn = document.getElementById('send-btn');

    // Auto-resize textarea
    inputEl.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // Send on Enter (Shift+Enter = newline)
    inputEl.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            formEl.dispatchEvent(new Event('submit', { cancelable: true }));
        }
    });

    const scrollToBottom = (smooth = true) => {
        messagesEl.scrollTo({ top: messagesEl.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
    };

    const formatTime = (iso) => {
        if (!iso) return 'baru saja';
        try {
            return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }).format(new Date(iso));
        } catch (_) { return 'baru saja'; }
    };

    const escapeHtml = (v) => {
        const d = document.createElement('div');
        d.textContent = v;
        return d.innerHTML;
    };

    const appendMessage = (payload) => {
        if (!payload || Number(payload.chat_id) !== chatId) return;

        const messageId = Number(payload.id);
        if (messageId && messagesEl.querySelector('[data-message-id="' + messageId + '"]')) return;

        const emptyState = document.getElementById('empty-chat-state');
        if (emptyState) emptyState.remove();

        const isOwn = Number(payload.sender_id) === currentUserId;
        const wrapper = document.createElement('div');
        wrapper.className = 'flex ' + (isOwn ? 'justify-end' : 'justify-start');
        wrapper.style.opacity = '0';
        wrapper.style.transform = 'translateY(8px)';
        if (messageId) wrapper.setAttribute('data-message-id', String(messageId));

        const avatarHtml = !isOwn
            ? `<div class="w-7 h-7 rounded-xl flex items-center justify-center text-[10px] font-black flex-shrink-0 mr-2 mt-auto mb-0.5" style="background:rgba(114,191,119,.15);color:#3fa348">${escapeHtml(withName.charAt(0).toUpperCase())}</div>`
            : '';

        wrapper.innerHTML = `
            ${avatarHtml}
            <div class="${isOwn ? 'items-end' : 'items-start'} flex flex-col gap-0.5 max-w-[75%]">
                <div class="px-4 py-2.5 rounded-2xl text-sm font-medium leading-relaxed
                    ${isOwn ? 'text-white rounded-br-md' : 'bg-white border border-gray-100 text-gray-900 shadow-sm rounded-bl-md'}"
                     style="${isOwn ? 'background:#3fa348' : ''}">
                    ${escapeHtml(payload.message || '')}
                </div>
                <span class="text-[9px] font-bold text-gray-400 px-1">${formatTime(payload.created_at)}</span>
            </div>`;

        messagesEl.appendChild(wrapper);
        requestAnimationFrame(() => {
            wrapper.style.transition = 'all 0.2s ease';
            wrapper.style.opacity = '1';
            wrapper.style.transform = 'translateY(0)';
        });
        scrollToBottom();
    };

    scrollToBottom(false);

    // Real-time via Laravel Echo
    if (broadcastKey && typeof window.Pusher !== 'undefined' && typeof window.Echo !== 'undefined') {
        window.Pusher = window.Pusher || Pusher;
        const echoInstance = new window.Echo({
            broadcaster: 'pusher',
            key: broadcastKey,
            cluster: broadcastCluster,
            wsHost: broadcastHost,
            wsPort: broadcastPort,
            wssPort: broadcastPort,
            forceTLS: broadcastScheme === 'https',
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/broadcasting/auth',
            auth: { headers: { 'X-CSRF-TOKEN': csrfToken } },
        });
        echoInstance.private('chat.' + chatId).listen('.MessageSent', appendMessage);
    }

    // Form submit via AJAX
    formEl.addEventListener('submit', async function (event) {
        event.preventDefault();
        const message = inputEl.value.trim();
        if (!message) return;

        sendBtn.disabled = true;
        sendBtn.style.opacity = '0.6';

        try {
            const response = await fetch(formEl.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: new FormData(formEl),
                credentials: 'same-origin',
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Gagal mengirim pesan.');
            }

            if (result.data) appendMessage(result.data);

            inputEl.value = '';
            inputEl.style.height = 'auto';
            inputEl.focus();
        } catch (_) {
            if (typeof window.arradeaPopup !== 'undefined') {
                window.arradeaPopup.error('Pesan gagal dikirim. Silakan coba lagi.');
            }
        } finally {
            sendBtn.disabled = false;
            sendBtn.style.opacity = '1';
        }
    });
})();
</script>
@endsection