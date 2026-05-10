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
        <div class="flex items-start justify-between mb-2">
            <div class="flex-1">
                <h3 class="font-bold text-gray-900">{{ $complaint->subject }}</h3>
                <p class="text-xs text-gray-400 mt-1">{{ $complaint->created_at->format('d M Y, H:i') }}</p>
            </div>
            <span class="px-3 py-1 rounded-lg text-xs font-bold
                @if($complaint->status === 'pending') bg-amber-100 text-amber-700
                @elseif($complaint->status === 'in_progress') bg-blue-100 text-blue-700
                @elseif($complaint->status === 'resolved') bg-green-100 text-green-700
                @else bg-gray-100 text-gray-700
                @endif">
                @if($complaint->status === 'pending') Menunggu
                @elseif($complaint->status === 'in_progress') Diproses
                @elseif($complaint->status === 'resolved') Selesai
                @else Ditutup
                @endif
            </span>
        </div>
        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $complaint->message }}</p>
        
        @if($complaint->admin_response)
        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
            <p class="text-xs font-bold text-green-700 mb-1">Respon Admin:</p>
            <p class="text-sm text-green-800">{{ $complaint->admin_response }}</p>
            <p class="text-xs text-green-600 mt-1">{{ $complaint->responded_at->format('d M Y, H:i') }}</p>
        </div>
        @endif
        
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
