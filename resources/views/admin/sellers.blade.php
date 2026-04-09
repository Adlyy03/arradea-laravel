@extends('layouts.dashboard')

@section('title', 'Data Seller - Arradea Admin')
@section('page_title', 'Master Data Penjual')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-end gap-6 lg:gap-12 bg-white p-6 lg:p-12 lg:p-10 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-[4rem] shadow-sm border border-gray-100">
        <div class="max-w-2xl text-center md:text-left">
            <h1 class="text-4xl lg:text-6xl font-black text-gray-900 tracking-tighter leading-tight mb-4">Master <span class="text-accent underline underline-offset-8">Data Seller</span> Arradea.</h1>
            <p class="text-gray-500 text-lg font-medium">Monitoring performa marketplace Arradea, kelola data seller, dan atur strategi bisnis Anda di sini.</p>
        </div>
        <div class="flex gap-4">
            <button class="px-5 lg:px-10 py-5 bg-black text-white rounded-2xl lg:rounded-3xl font-black text-lg shadow-2xl hover:scale-105 active:scale-95 transition-all">Lacak Performa</button>
            <button class="px-5 lg:px-10 py-5 bg-gray-50 text-gray-400 rounded-2xl lg:rounded-3xl font-black text-lg hover:bg-gray-100 transition-all">Export (.csv)</button>
        </div>
    </div>

    <!-- Management Table -->
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-[4rem] p-6 lg:p-12 shadow-sm border border-gray-100 space-y-6 lg:space-y-12">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-4 px-4">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tighter leading-none mb-4">Seluruh <span class="text-primary-600">Penjual</span>.</h2>
                <p class="text-sm text-gray-500">Menampilkan seller yang sudah aktif, sedang menunggu persetujuan, dan aplikasi yang ditolak.</p>
            </div>
            <div class="text-gray-400 font-bold uppercase tracking-widest text-xs">
                Total aplikasi: {{ \App\Models\User::whereIn('seller_status', ['pending','approved','rejected'])->count() }}
                <br>
                Pending: {{ \App\Models\User::where('seller_status', 'pending')->count() }}
            </div>
        </div>
        
        <div class="overflow-x-auto shadow-inner rounded-2xl lg:rounded-3xl lg:rounded-[3rem] border border-gray-50">
            <table class="w-full text-left">
                <thead class="text-xs font-black tracking-widest uppercase text-gray-400 border-b border-gray-100 bg-gray-50/50">
                    <tr>
                        <th class="px-5 lg:px-10 py-5 lg:py-10">Nama / Email Seller</th>
                        <th class="px-5 lg:px-10 py-5 lg:py-10">Nama Toko</th>
                        <th class="px-5 lg:px-10 py-5 lg:py-10">Status Akun</th>
                        <th class="px-5 lg:px-10 py-5 lg:py-10">Total Produk</th>
                        <th class="px-5 lg:px-10 py-5 lg:py-10">Aksi Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach(\App\Models\User::whereIn('seller_status', ['pending','approved','rejected'])->with('store')->latest()->get() as $seller)
                        <tr class="hover:bg-primary-50/20 transition duration-300">
                            <td class="px-5 lg:px-10 py-5 lg:py-10">
                                <div class="flex items-center gap-6">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 font-black text-xs border border-gray-100 shadow-sm">
                                        {{ strtoupper(substr($seller->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-xl text-gray-900 leading-tight">{{ $seller->name }}</p>
                                        <p class="text-sm font-medium text-gray-400 italic">{{ $seller->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 lg:px-10 py-5 lg:py-10">
                                <p class="text-gray-700 font-bold group-hover:text-primary-600 transition tracking-tight">{{ $seller->store->name ?? 'Belum ada Toko' }}</p>
                            </td>
                            <td class="px-5 lg:px-10 py-5 lg:py-10">
                                @if($seller->seller_status === 'approved')
                                    <span class="px-5 py-2.5 bg-green-100 text-green-700 rounded-2xl text-[10px] font-black uppercase tracking-widest">Aktif Verifikasi</span>
                                @elseif($seller->seller_status === 'pending')
                                    <span class="px-5 py-2.5 bg-amber-100 text-amber-700 rounded-2xl text-[10px] font-black uppercase tracking-widest">Menunggu Persetujuan</span>
                                @elseif($seller->seller_status === 'rejected')
                                    <span class="px-5 py-2.5 bg-red-100 text-red-700 rounded-2xl text-[10px] font-black uppercase tracking-widest">Permohonan Ditolak</span>
                                @else
                                    <span class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-2xl text-[10px] font-black uppercase tracking-widest">Belum Ajukan</span>
                                @endif
                            </td>
                            <td class="px-5 lg:px-10 py-5 lg:py-10">
                                <p class="text-gray-900 font-black text-lg">{{ $seller->store ? $seller->store->products()->count() : 0 }} Item</p>
                            </td>
                            <td class="px-5 lg:px-10 py-5 lg:py-10">
                                <div class="flex flex-wrap gap-3">
                                    @if($seller->seller_status === 'pending')
                                        <form method="POST" action="{{ route('admin.sellers.approve', $seller) }}">
                                            @csrf
                                            <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-green-100 hover:scale-[1.05] transition-all">Setujui</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.sellers.reject', $seller) }}">
                                            @csrf
                                            <button type="submit" class="px-6 py-3 bg-red-100 text-red-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-red-200 transition-all">Tolak</button>
                                        </form>
                                    @elseif($seller->seller_status === 'approved')
                                        <span class="px-6 py-3 bg-green-50 text-green-700 rounded-2xl text-[10px] font-black uppercase tracking-widest">Approved</span>
                                    @elseif($seller->seller_status === 'rejected')
                                        <span class="px-6 py-3 bg-red-50 text-red-700 rounded-2xl text-[10px] font-black uppercase tracking-widest">Rejected</span>
                                    @else
                                        <span class="px-6 py-3 bg-gray-50 text-gray-500 rounded-2xl text-[10px] font-black uppercase tracking-widest">No Action</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
