@extends('layouts.dashboard')

@section('title', 'Analytics - Arradea Seller')
@section('page_title', 'Analitik Toko')

@section('content')
@php
    $analyticsQuery = [
        'range' => $analytics['range'] ?? '7d',
    ];

    if (($analytics['range'] ?? '7d') === 'custom') {
        $analyticsQuery['start_date'] = $analytics['start_date'] ?? null;
        $analyticsQuery['end_date'] = $analytics['end_date'] ?? null;
    }
@endphp

<div class="space-y-5">
    <div class="bg-white rounded-2xl border border-gray-100 p-5 lg:p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Ringkasan Penjualan</p>
                <h1 class="text-2xl lg:text-3xl font-black text-gray-900">Analitik Toko</h1>
                <p class="text-sm text-gray-500 mt-1">Periode: {{ $analytics['period_label'] ?? '7 Hari Terakhir' }}</p>
            </div>
            <a href="{{ route('seller.analytics.export', $analyticsQuery) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white" style="background:#1e5128;">
                Export Laporan
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('seller.analytics') }}" class="bg-white rounded-2xl border border-gray-100 p-4 lg:p-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Rentang</label>
                <select name="range" id="analytics-range" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2" style="--tw-ring-color:rgba(114,191,119,.35)">
                    <option value="7d" @selected(($analytics['range'] ?? '7d') === '7d')>7 Hari</option>
                    <option value="30d" @selected(($analytics['range'] ?? '7d') === '30d')>30 Hari</option>
                    <option value="custom" @selected(($analytics['range'] ?? '7d') === 'custom')>Custom</option>
                </select>
            </div>

            <div id="analytics-start-date-group" class="{{ ($analytics['range'] ?? '7d') === 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Mulai</label>
                <input type="date" name="start_date" value="{{ $analytics['start_date'] ?? '' }}" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2" style="--tw-ring-color:rgba(114,191,119,.35)">
            </div>

            <div id="analytics-end-date-group" class="{{ ($analytics['range'] ?? '7d') === 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Sampai</label>
                <input type="date" name="end_date" value="{{ $analytics['end_date'] ?? '' }}" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2" style="--tw-ring-color:rgba(114,191,119,.35)">
            </div>

            <button type="submit" class="h-[42px] rounded-xl px-4 text-sm font-black text-white" style="background:#72bf77;">Terapkan</button>
        </div>
    </form>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pendapatan</p>
            <p class="text-xl lg:text-2xl font-black text-gray-900 mt-1">Rp {{ number_format($analytics['total_revenue'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Pesanan</p>
            <p class="text-xl lg:text-2xl font-black text-gray-900 mt-1">{{ $analytics['total_orders'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pesanan Selesai</p>
            <p class="text-xl lg:text-2xl font-black text-gray-900 mt-1">{{ $analytics['completed_orders'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Produk Aktif</p>
            <p class="text-xl lg:text-2xl font-black text-gray-900 mt-1">{{ $analytics['total_products'] ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-4 lg:p-5">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4">Status Pesanan</h3>
            <div class="space-y-2.5">
                <div class="flex items-center justify-between text-sm"><span class="font-semibold text-gray-600">Menunggu</span><span class="font-black text-gray-900">{{ $analytics['pending_orders'] ?? 0 }}</span></div>
                <div class="flex items-center justify-between text-sm"><span class="font-semibold text-gray-600">Diproses</span><span class="font-black text-gray-900">{{ $analytics['accepted_orders'] ?? 0 }}</span></div>
                <div class="flex items-center justify-between text-sm"><span class="font-semibold text-gray-600">Selesai</span><span class="font-black text-gray-900">{{ $analytics['completed_orders'] ?? 0 }}</span></div>
                <div class="flex items-center justify-between text-sm"><span class="font-semibold text-gray-600">Dibatalkan</span><span class="font-black text-gray-900">{{ $analytics['cancelled_orders'] ?? 0 }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-4 lg:p-5">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider mb-4">Produk Terlaris</h3>
            @if(($analytics['top_products'] ?? collect())->isEmpty())
                <p class="text-sm text-gray-500">Belum ada data produk terlaris di periode ini.</p>
            @else
                <div class="space-y-3">
                    @foreach($analytics['top_products'] as $rank => $item)
                        <div class="rounded-xl border border-gray-100 p-3">
                            <p class="text-sm font-black text-gray-900">#{{ $rank + 1 }} {{ $item->product->name ?? 'Produk Dihapus' }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ (int) $item->total_orders }} order | {{ (int) $item->total_qty }} item | Rp {{ number_format((float) $item->total_revenue, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rangeSelect = document.getElementById('analytics-range');
        const startDateGroup = document.getElementById('analytics-start-date-group');
        const endDateGroup = document.getElementById('analytics-end-date-group');

        if (rangeSelect && startDateGroup && endDateGroup) {
            const toggleCustomRangeFields = () => {
                const isCustom = rangeSelect.value === 'custom';
                startDateGroup.classList.toggle('hidden', !isCustom);
                endDateGroup.classList.toggle('hidden', !isCustom);
            };

            toggleCustomRangeFields();
            rangeSelect.addEventListener('change', toggleCustomRangeFields);
        }
    });
</script>
@endsection