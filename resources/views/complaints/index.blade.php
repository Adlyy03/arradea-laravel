@extends('layouts.dashboard')
@section('title', 'Keluhan Saya')
@section('page_title', 'Keluhan Saya')

@section('content')
<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-black text-gray-900">Daftar Keluhan</h2>
        <a href="{{ route('complaints.create') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">
            + Buat Keluhan Baru
        </a>
    </div>

    {{-- List --}}
    @forelse($complaints as $complaint)
    <div class="bg-white rounded-xl border border-gray-100 p-4 hover:shadow-md transition">
        <div class="mb-2">
            <h3 class="font-bold text-gray-900">{{ $complaint->subject }}</h3>
            <p class="text-xs text-gray-400 mt-1">{{ $complaint->created_at->format('d M Y, H:i') }}</p>
        </div>
        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $complaint->message }}</p>
        
        <a href="{{ route('complaints.show', $complaint) }}" class="text-sm font-bold" style="color:#72bf77">Lihat Detail →</a>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-gray-100 p-12 text-center">
        <p class="text-gray-400 mb-4">Belum ada keluhan</p>
        <a href="{{ route('complaints.create') }}" class="inline-block px-4 py-2 rounded-xl text-sm font-bold text-white" style="background:#72bf77">
            Buat Keluhan Pertama
        </a>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($complaints->hasPages())
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        {{ $complaints->links() }}
    </div>
    @endif
</div>
@endsection
