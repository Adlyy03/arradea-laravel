@extends('layouts.dashboard')
@section('title', 'Mode Switcher Demo — Arradea')
@section('page_title', 'Mode Switcher Demo')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 p-4">
    <!-- Demo Header -->
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Mode Switcher Demo</h1>
        <p class="text-gray-600">Klik tombol di bawah untuk membuka bottom sheet mode switcher (DANA Style)</p>
    </div>

    <!-- Current Mode Info -->
    <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-600 mb-1">Mode Aktif Saat Ini:</p>
                <div class="flex items-center gap-3">
                    <x-mode-badge :mode="auth()->user()->getActiveMode()" />
                    <span class="text-2xl">
                        @if(auth()->user()->getActiveMode() === 'buyer')
                            🛒
                        @else
                            🏪
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Mode Switcher Component -->
    <div class="bg-white rounded-2xl p-8 shadow-lg">
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-2">Ganti Mode Akun</h2>
            <p class="text-sm text-gray-600">Klik tombol di bawah untuk membuka bottom sheet</p>
        </div>
        
        <div class="flex justify-center">
            <x-bottom-sheet-switcher :user="auth()->user()" />
        </div>
    </div>

    <!-- Features Info -->
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <h3 class="text-lg font-bold text-gray-900 mb-4">✨ Fitur Bottom Sheet:</h3>
        <ul class="space-y-3 text-sm text-gray-700">
            <li class="flex items-start gap-3">
                <span class="text-green-500 font-bold">✓</span>
                <span><strong>Swipe to Close:</strong> Geser ke bawah untuk menutup</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-green-500 font-bold">✓</span>
                <span><strong>Smooth Animation:</strong> Animasi halus seperti DANA</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-green-500 font-bold">✓</span>
                <span><strong>Mobile Optimized:</strong> Dioptimalkan untuk tampilan mobile</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-green-500 font-bold">✓</span>
                <span><strong>Active State:</strong> Menampilkan mode yang sedang aktif</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-green-500 font-bold">✓</span>
                <span><strong>Disabled State:</strong> Mode seller disabled jika belum disetujui</span>
            </li>
        </ul>
    </div>

    <!-- User Info -->
    <div class="bg-gray-50 rounded-2xl p-6 shadow-lg">
        <h3 class="text-lg font-bold text-gray-900 mb-4">👤 Info Akun:</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Nama:</span>
                <span class="font-semibold text-gray-900">{{ auth()->user()->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Is Seller:</span>
                <span class="font-semibold text-gray-900">{{ auth()->user()->is_seller ? 'Ya' : 'Tidak' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Can Switch to Seller:</span>
                <span class="font-semibold text-gray-900">{{ auth()->user()->canSwitchToSellerMode() ? 'Ya' : 'Tidak' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Active Mode:</span>
                <span class="font-semibold text-gray-900">{{ ucfirst(auth()->user()->getActiveMode()) }}</span>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="text-center">
        <a href="{{ route('profile') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 text-white rounded-xl font-semibold hover:bg-gray-800 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Profile
        </a>
    </div>
</div>
@endsection
