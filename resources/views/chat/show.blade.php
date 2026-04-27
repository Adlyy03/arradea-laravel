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
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                        <div class="max-w-[85%] lg:max-w-xs px-5 py-3 rounded-2xl {{ $message->sender_id === auth()->id() ? 'bg-primary-600 text-white shadow-lg shadow-primary-100' : 'bg-white text-gray-900 border border-gray-100 shadow-sm' }}">
                            <p class="text-sm font-medium leading-relaxed">{{ $message->message }}</p>
                            <p class="text-[9px] opacity-70 mt-1 font-bold text-right">{{ $message->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div id="empty-chat-state" class="text-center text-gray-400 py-6 lg:py-12">
                        <div class="text-4xl mb-2">💬</div>
                        <p class="text-xs font-black uppercase tracking-widest">Belum ada pesan</p>
                    </div>
                @endforelse
            </div>

            <form id="chat-form" action="{{ route('chat.store', $chat) }}" method="POST" class="flex gap-3">
                @csrf
                <input id="chat-message-input" type="text" name="message" placeholder="Ketik pesan..." class="flex-1 h-14 bg-gray-50 border-none rounded-2xl px-6 focus:ring-2 focus:ring-primary-600 font-bold text-sm lg:text-base transition-all" required>
                <button type="submit" class="w-14 h-14 lg:w-auto lg:px-8 bg-primary-600 text-white rounded-2xl hover:bg-primary-700 shadow-lg shadow-primary-100 transition flex items-center justify-center">
                    <svg class="w-5 h-5 lg:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="hidden lg:inline font-black uppercase tracking-widest text-xs">Kirim</span>
                </button>
            </form>
        </div>

        <div class="mt-6">
            <a href="{{ $order->user_id === auth()->id() ? route('buyer.orders') : '/seller/orders' }}" class="text-primary-600 hover:text-primary-700">← Kembali ke Orders</a>
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
        const broadcastKey = @json(env('REVERB_APP_KEY', env('PUSHER_APP_KEY')));
        const broadcastCluster = @json(env('PUSHER_APP_CLUSTER', 'mt1'));
        const broadcastHost = @json(env('REVERB_HOST', env('PUSHER_HOST', '127.0.0.1')));
        const broadcastPort = Number(@json((int) env('REVERB_PORT', env('PUSHER_PORT', 8080))));
        const broadcastScheme = @json(env('REVERB_SCHEME', env('PUSHER_SCHEME', 'http')));

        const messagesEl = document.getElementById('messages');
        const formEl = document.getElementById('chat-form');
        const inputEl = document.getElementById('chat-message-input');

        if (!messagesEl || !formEl || !inputEl) {
            return;
        }

        const scrollToBottom = () => {
            messagesEl.scrollTop = messagesEl.scrollHeight;
        };

        const formatTime = (iso) => {
            if (!iso) {
                return 'baru saja';
            }

            try {
                return new Intl.DateTimeFormat('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    hour: '2-digit',
                    minute: '2-digit',
                }).format(new Date(iso));
            } catch (_error) {
                return 'baru saja';
            }
        };

        const escapeHtml = (value) => {
            const div = document.createElement('div');
            div.textContent = value;
            return div.innerHTML;
        };

        const appendMessage = (payload) => {
            if (!payload || Number(payload.chat_id) !== chatId) {
                return;
            }

            const messageId = Number(payload.id);
            if (messageId && messagesEl.querySelector('[data-message-id="' + messageId + '"]')) {
                return;
            }

            const emptyState = document.getElementById('empty-chat-state');
            if (emptyState) {
                emptyState.remove();
            }

            const isOwn = Number(payload.sender_id) === currentUserId;
            const wrapper = document.createElement('div');
            wrapper.className = 'flex ' + (isOwn ? 'justify-end' : 'justify-start');
            if (messageId) {
                wrapper.setAttribute('data-message-id', String(messageId));
            }

            wrapper.innerHTML = `
                <div class="max-w-[85%] lg:max-w-xs px-5 py-3 rounded-2xl ${isOwn ? 'bg-primary-600 text-white shadow-lg shadow-primary-100' : 'bg-white text-gray-900 border border-gray-100 shadow-sm'}">
                    <p class="text-sm font-medium leading-relaxed">${escapeHtml(payload.message || '')}</p>
                    <p class="text-[9px] opacity-70 mt-1 font-bold text-right">${formatTime(payload.created_at)}</p>
                </div>
            `;

            messagesEl.appendChild(wrapper);
            scrollToBottom();
        };

        scrollToBottom();

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
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                },
            });

            echoInstance.private('chat.' + chatId)
                .listen('.MessageSent', appendMessage);
        }

        formEl.addEventListener('submit', async function (event) {
            event.preventDefault();

            const message = inputEl.value.trim();
            if (!message) {
                return;
            }

            const submitButton = formEl.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
            }

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

                if (result.data) {
                    appendMessage(result.data);
                }

                inputEl.value = '';
                inputEl.focus();
            } catch (_error) {
                alert('Pesan gagal dikirim. Silakan coba lagi.');
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                }
            }
        });
    })();
</script>
@endsection