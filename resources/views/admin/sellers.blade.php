@extends('layouts.dashboard')
@section('title', 'Data Seller — Arradea Admin')
@section('page_title', 'Data Seller')

@section('content')
@php
    $sellers = \App\Models\User::whereIn('seller_status',['pending','approved','rejected'])->orWhere('is_seller',true)
        ->with('store')->orderBy('seller_status')->latest()->get();
    $pendingCount = $sellers->where('seller_status','pending')->count();
    $approvedCount = $sellers->where('seller_status','approved')->count();
@endphp

<div class="space-y-5 fade-up">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Data <span style="color:#72bf77">Seller</span></h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola seluruh penjual di marketplace Arradea.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 rounded-xl bg-amber-50 border border-amber-200">
                <p class="text-xs text-amber-600 font-bold">{{ $pendingCount }} Pending</p>
            </div>
            <div class="px-4 py-2 rounded-xl bg-green-50 border border-green-200">
                <p class="text-xs font-bold" style="color:#3fa348">{{ $approvedCount }} Aktif</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400">Seller</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hidden sm:table-cell">Toko</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hidden md:table-cell">Produk</th>
                        <th class="text-right px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($sellers as $s)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0" style="background:rgba(114,191,119,.12);color:#3fa348">{{ strtoupper(substr($s->name,0,1)) }}</div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $s->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $s->phone }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden sm:table-cell">
                            <p class="font-medium text-gray-700">{{ $s->store->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $s->store->address ?? '' }}</p>
                        </td>
                        <td class="px-5 py-4">
                            @if($s->seller_status === 'approved')
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase bg-green-100 text-green-700">✓ Approved</span>
                            @elseif($s->seller_status === 'pending')
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase bg-amber-100 text-amber-700">⏳ Pending</span>
                            @elseif($s->seller_status === 'rejected')
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase bg-red-100 text-red-600">✕ Rejected</span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase bg-gray-100 text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="font-bold text-gray-900">{{ $s->store ? $s->store->products()->count() : 0 }}</span>
                            <span class="text-xs text-gray-400 ml-1">item</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($s->seller_status === 'pending')
                                    <form method="POST" action="{{ route('admin.sellers.approve', $s) }}">
                                        @csrf
                                        <button class="px-3 py-1.5 rounded-lg text-[10px] font-black text-white" style="background:#72bf77">Setujui</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.sellers.reject', $s) }}">
                                        @csrf
                                        <button class="px-3 py-1.5 rounded-lg text-[10px] font-black bg-red-50 text-red-500 hover:bg-red-100 transition">Tolak</button>
                                    </form>
                                @elseif($s->seller_status === 'approved')
                                    <span class="px-3 py-1.5 rounded-lg text-[10px] font-black bg-green-50 text-green-600">Aktif</span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400 font-medium">Belum ada data seller.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
