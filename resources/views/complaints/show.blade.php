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
        <div class="mb-4">
            <h2 class="text-xl font-black text-gray-900">{{ $complaint->subject }}</h2>
            <p class="text-sm text-gray-400 mt-1">Dikirim: {{ $complaint->created_at->format('d M Y, H:i') }}</p>
        </div>

        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-sm font-bold text-gray-700 mb-2">Pesan Keluhan:</p>
            <p class="text-gray-800 whitespace-pre-wrap">{{ $complaint->message }}</p>
        </div>
    </div>
</div>
@endsection
