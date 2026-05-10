@extends('layouts.dashboard')
@section('title', 'Keluhan User')
@section('page_title', 'Keluhan User')

@section('content')
<div class="space-y-4">
    {{-- Header with Export --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-black text-gray-900">Keluhan Hari Ini</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.complaints.export', ['days' => 1]) }}" class="px-4 py-2 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">
                Export 1 Hari
            </a>
            <a href="{{ route('admin.complaints.export', ['days' => 7]) }}" class="px-4 py-2 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">
                Export 7 Hari
            </a>
            <a href="{{ route('admin.complaints.export', ['days' => 30]) }}" class="px-4 py-2 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">
                Export 30 Hari
            </a>
        </div>
    </div>

    {{-- List --}}
    @forelse($complaints as $complaint)
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs text-gray-400">{{ $complaint->created_at->format('d M Y, H:i') }}</span>
                </div>
                <p class="text-sm font-bold text-gray-900 mb-1">
                    Dari: {{ $complaint->user->name }} 
                    <span class="text-xs font-normal text-gray-400">({{ $complaint->user->is_seller ? 'Seller' : 'Buyer' }})</span>
                </p>
                <p class="text-xs text-gray-500 mb-2">{{ $complaint->subject }}</p>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-3">
            <p class="text-sm text-gray-700">{{ $complaint->message }}</p>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-gray-100 p-12 text-center">
        <p class="text-gray-400">Belum ada keluhan hari ini</p>
    </div>
    @endforelse

    @if($complaints->hasPages())
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        {{ $complaints->links() }}
    </div>
    @endif
</div>
@endsection
