@extends('layouts.dashboard')
@section('title', 'Detail Keluhan')
@section('page_title', 'Detail Keluhan')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    {{-- Back Button --}}
    <a href="{{ route('complaints.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-900">
        ← Kembali
    </a>

    {{-- Complaint Card --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-xl font-black text-gray-900">{{ $complaint->subject }}</h2>
                <p class="text-sm text-gray-400 mt-1">Dikirim: {{ $complaint->created_at->format('d M Y, H:i') }}</p>
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

        <div class="bg-gray-50 rounded-xl p-4 mb-4">
            <p class="text-sm font-bold text-gray-700 mb-2">Pesan Keluhan:</p>
            <p class="text-gray-800 whitespace-pre-wrap">{{ $complaint->message }}</p>
        </div>

        @if($complaint->admin_response)
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-sm font-bold text-green-700 mb-2">Respon dari Admin:</p>
            <p class="text-green-800 whitespace-pre-wrap">{{ $complaint->admin_response }}</p>
            <p class="text-xs text-green-600 mt-3">Direspon: {{ $complaint->responded_at->format('d M Y, H:i') }}</p>
        </div>
        @else
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center">
            <p class="text-sm text-amber-700">⏳ Menunggu respon dari admin</p>
        </div>
        @endif
    </div>
</div>
@endsection
