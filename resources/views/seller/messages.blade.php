@extends('layouts.dashboard')

@section('title', 'Messages - Arradea Seller')
@section('page_title', 'Pesan dari Pembeli')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-teal-600 p-8 lg:p-10 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] text-white overflow-hidden relative shadow-2xl">
        <div class="absolute -top-32 -right-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-40"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-20"></div>

        <div class="relative z-10">
            <p class="text-[10px] font-black uppercase tracking-widest text-green-200">Komunikasi</p>
            <h1 class="text-4xl lg:text-6xl font-black tracking-tighter leading-tight lg:leading-none mb-4">Pesan <span class="text-yellow-300 underline underline-offset-4 lg:underline-offset-8">Pembeli</span>.</h1>
            <p class="text-green-100 font-medium text-lg">Kelola percakapan dengan pelanggan Anda.</p>
        </div>
    </div>

    <!-- Chats List -->
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-[3.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-100">
            <h2 class="text-2xl font-black text-gray-900">Daftar Percakapan</h2>
            <p class="text-gray-500 font-medium mt-1">Chat aktif dengan pembeli produk Anda.</p>
        </div>

        <div class="divide-y divide-gray-50">
            @forelse($chats as $chat)
                <a href="{{ route('chat.show', $chat->order_id) }}" class="block p-8 hover:bg-gray-50/50 transition">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-lg font-black">
                            {{ strtoupper(substr($chat->buyer->name ?? 'P', 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-black text-gray-900">{{ $chat->buyer->name ?? 'Pembeli' }}</h3>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    {{ $chat->messages->first() ? $chat->messages->first()->created_at->diffForHumans() : 'Belum ada pesan' }}
                                </span>
                            </div>
                            <p class="text-gray-600 text-sm">
                                {{ $chat->messages->first() ? Str::limit($chat->messages->first()->message, 100) : 'Belum ada pesan' }}
                            </p>
                        </div>
                        @if($chat->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count() > 0)
                            <div class="w-3 h-3 bg-accent rounded-full"></div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="p-12 lg:p-24 text-center">
                    <div class="text-4xl lg:text-6xl mb-4">💬</div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Belum Ada Percakapan</h3>
                    <p class="text-gray-500 font-medium">Percakapan dengan pembeli akan muncul di sini setelah mereka menghubungi Anda.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection