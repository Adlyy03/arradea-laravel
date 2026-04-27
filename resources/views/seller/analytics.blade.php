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

<div class="space-y-6 lg:space-y-12">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-8 lg:p-10 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] text-white overflow-hidden relative shadow-2xl">
        <div class="absolute -top-32 -right-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-40"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-20"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-blue-200">Performa Bisnis</p>
                <h1 class="text-4xl lg:text-6xl font-black tracking-tighter leading-tight lg:leading-none mb-4">Analitik <span class="text-yellow-300 underline underline-offset-4 lg:underline-offset-8">Toko</span>.</h1>
                <p class="text-blue-100 font-medium text-lg">Pantau performa penjualan dan pertumbuhan bisnis Anda.</p>
                <p class="text-blue-200 text-sm font-bold mt-2">Periode aktif: {{ $analytics['period_label'] ?? '7 Hari Terakhir' }}</p>
            </div>
            <a href="{{ route('seller.analytics.export', $analyticsQuery) }}" class="px-6 py-4 bg-white text-blue-600 rounded-2xl font-black shadow-lg hover:bg-gray-50 hover:-translate-y-1 transition-all flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel Laporan
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('seller.analytics') }}" class="bg-white p-5 lg:p-8 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] border border-gray-100 shadow-sm">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 lg:gap-6 items-end">
            <div class="lg:col-span-2">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Rentang Waktu</label>
                <select name="range" id="analytics-range" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-bold focus:border-primary-600 focus:outline-none">
                    <option value="7d" @selected(($analytics['range'] ?? '7d') === '7d')>7 Hari Terakhir</option>
                    <option value="30d" @selected(($analytics['range'] ?? '7d') === '30d')>30 Hari Terakhir</option>
                    <option value="custom" @selected(($analytics['range'] ?? '7d') === 'custom')>Custom</option>
                </select>
            </div>

            <div id="analytics-start-date-group" class="{{ ($analytics['range'] ?? '7d') === 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $analytics['start_date'] ?? '' }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-bold focus:border-primary-600 focus:outline-none">
            </div>

            <div id="analytics-end-date-group" class="{{ ($analytics['range'] ?? '7d') === 'custom' ? '' : 'hidden' }}">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $analytics['end_date'] ?? '' }}" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-bold focus:border-primary-600 focus:outline-none">
            </div>

            <div class="flex gap-3">
                <button type="submit" class="w-full rounded-2xl bg-primary-700 text-white px-5 py-3 font-black hover:bg-primary-800 transition">Terapkan</button>
            </div>
        </div>
    </form>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <div class="bg-white p-8 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100 space-y-4">
            <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-2xl">📦</div>
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Total Produk</p>
                <h4 class="text-3xl font-black text-gray-900 leading-none">{{ $analytics['total_products'] }}</h4>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100 space-y-4">
            <div class="w-14 h-14 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center text-2xl">💰</div>
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Total Pendapatan</p>
                <h4 class="text-3xl font-black text-gray-900 leading-none">Rp {{ number_format($analytics['total_revenue'], 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100 space-y-4">
            <div class="w-14 h-14 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center text-2xl">🛒</div>
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Total Pesanan</p>
                <h4 class="text-3xl font-black text-gray-900 leading-none">{{ $analytics['total_orders'] }}</h4>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100 space-y-4">
            <div class="w-14 h-14 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center text-2xl">📈</div>
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Pesanan di Rentang</p>
                <h4 class="text-3xl font-black text-gray-900 leading-none">{{ $analytics['orders_in_period'] }}</h4>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100">
            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Growth Pendapatan Bulanan</p>
            <h4 class="text-4xl font-black {{ $analytics['growth_percent'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $analytics['growth_percent'] >= 0 ? '+' : '' }}{{ number_format($analytics['growth_percent'], 1, ',', '.') }}%
            </h4>
            <p class="text-sm font-bold text-gray-500 mt-2">
                Periode ini: Rp {{ number_format($analytics['this_period_revenue'], 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Periode sebelumnya: Rp {{ number_format($analytics['previous_period_revenue'], 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white p-8 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100">
            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Conversion Selesai</p>
            <h4 class="text-4xl font-black text-sky-600">{{ number_format($analytics['conversion_rate'], 1, ',', '.') }}%</h4>
            <p class="text-sm font-bold text-gray-500 mt-2">
                {{ $analytics['completed_orders'] }} dari {{ $analytics['total_orders'] }} pesanan selesai
            </p>
        </div>

        <div class="bg-white p-8 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100">
            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Average Order Value</p>
            <h4 class="text-4xl font-black text-orange-600">Rp {{ number_format($analytics['average_order_value'], 0, ',', '.') }}</h4>
            <p class="text-sm font-bold text-gray-500 mt-2">Rata-rata nilai order yang selesai</p>
        </div>
    </div>

    <!-- Order Status Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-5 lg:p-10 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100">
            <h3 class="text-2xl font-black text-gray-900 mb-8">Status Pesanan</h3>
            <div class="space-y-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 bg-amber-400 rounded-full"></div>
                        <span class="font-bold text-gray-700">Menunggu</span>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ $analytics['pending_orders'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                        <span class="font-bold text-gray-700">Diproses</span>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ $analytics['accepted_orders'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 bg-green-400 rounded-full"></div>
                        <span class="font-bold text-gray-700">Selesai</span>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ $analytics['completed_orders'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 bg-red-500 rounded-full"></div>
                        <span class="font-bold text-gray-700">Ditolak</span>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ $analytics['rejected_orders'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-4 h-4 bg-gray-400 rounded-full"></div>
                        <span class="font-bold text-gray-700">Dibatalkan</span>
                    </div>
                    <span class="text-2xl font-black text-gray-900">{{ $analytics['cancelled_orders'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 lg:p-10 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100">
            <h3 class="text-2xl font-black text-gray-900 mb-8">Tips Optimasi</h3>
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100">
                    <p class="text-sm font-bold text-blue-800">Tambah Produk Baru</p>
                    <p class="text-xs text-blue-600 mt-1">Produk yang beragam menarik lebih banyak pembeli.</p>
                </div>
                <div class="p-4 bg-green-50 rounded-2xl border border-green-100">
                    <p class="text-sm font-bold text-green-800">Responsif terhadap Pesanan</p>
                    <p class="text-xs text-green-600 mt-1">Proses pesanan dengan cepat untuk ulasan positif.</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-2xl border border-purple-100">
                    <p class="text-sm font-bold text-purple-800">Promosikan Produk</p>
                    <p class="text-xs text-purple-600 mt-1">Gunakan diskon untuk meningkatkan penjualan.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white p-5 lg:p-10 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100">
            <h3 class="text-2xl font-black text-gray-900 mb-6">Tren Pendapatan ({{ $analytics['period_label'] ?? '7 Hari Terakhir' }})</h3>
            <div class="h-72">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-5 lg:p-10 rounded-2xl lg:rounded-3xl lg:rounded-[3rem] shadow-sm border border-gray-100">
            <h3 class="text-2xl font-black text-gray-900 mb-6">Produk Terlaris</h3>
            @if(($analytics['top_products'] ?? collect())->isEmpty())
                <p class="text-sm font-bold text-gray-500">Belum ada data penjualan selesai untuk menampilkan produk terlaris.</p>
            @else
                <div class="space-y-4">
                    @foreach($analytics['top_products'] as $rank => $item)
                        <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-black text-gray-900">#{{ $rank + 1 }} {{ $item->product->name ?? 'Produk Dihapus' }}</p>
                                <span class="text-xs font-black text-primary-700 bg-primary-100 px-3 py-1 rounded-full">{{ (int) $item->total_qty }} item</span>
                            </div>
                            <p class="text-xs font-bold text-gray-500 mt-2">{{ (int) $item->total_orders }} order · Rp {{ number_format((float) $item->total_revenue, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        const canvas = document.getElementById('revenueTrendChart');
        if (!canvas || typeof window.Chart === 'undefined') {
            return;
        }

        const labels = @json($analytics['revenue_trend_labels'] ?? []);
        const values = @json($analytics['revenue_trend_values'] ?? []);

        new window.Chart(canvas, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: values,
                    borderColor: '#0284c7',
                    backgroundColor: 'rgba(2, 132, 199, 0.15)',
                    borderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.35,
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                const value = Number(context.raw || 0);
                                return ` Rp ${value.toLocaleString('id-ID')}`;
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback(value) {
                                return 'Rp ' + Number(value).toLocaleString('id-ID');
                            },
                        },
                        grid: { color: 'rgba(148, 163, 184, 0.2)' },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });
    });
</script>
@endsection