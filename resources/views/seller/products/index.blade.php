@extends('layouts.dashboard')

@section('title', 'Manajemen Produk - Arradea')
@section('page_title', 'Daftar Produk Toko')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-center lg:items-end gap-5 lg:gap-10 bg-white p-8 lg:p-6 lg:p-12 rounded-2xl lg:rounded-3xl lg:rounded-[3.5rem] shadow-sm border border-gray-100">
        <div class="max-w-2xl text-center lg:text-left">
            <h1 class="text-4xl lg:text-5xl font-black text-gray-900 tracking-tighter leading-tight mb-4">Kelola <span class="text-primary-600">Produk</span> Anda.</h1>
            <p class="text-gray-500 font-medium leading-relaxed">Tambahkan, edit, atau hapus produk jualan Anda dengan mudah. Pastikan stok dan deskripsi produk selalu diperbarui.</p>
        </div>
        <div class="flex-shrink-0 w-full lg:w-auto">
            <a href="/{{ Auth::user()->role }}/products/create" class="w-full lg:w-auto px-8 lg:px-5 lg:px-10 py-5 bg-primary-600 text-white rounded-2xl lg:rounded-2xl lg:rounded-3xl font-black text-lg shadow-xl shadow-primary-200 hover:scale-105 active:scale-95 transition-all flex items-center justify-center hover:bg-primary-700">
                + Produk Baru
            </a>
        </div>
    </div>

    <!-- Stats Row for Products -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
        <div class="bg-primary-900 p-6 lg:p-8 rounded-2xl lg:rounded-[2.5rem] shadow-xl text-white">
            <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-1 lg:mb-2">Total</p>
            <h3 class="text-2xl lg:text-4xl font-black">{{ $products->count() }} Item</h3>
        </div>
        <div class="bg-white p-6 lg:p-8 rounded-2xl lg:rounded-[2.5rem] shadow-sm border border-gray-100">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1 lg:mb-2">Stok Menipis</p>
            <h3 class="text-2xl lg:text-4xl font-black text-accent">0 Item</h3>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 text-[10px] font-black tracking-widest uppercase text-gray-400">
                <tr>
                    <th class="px-5 lg:px-10 py-8">Detail Produk</th>
                    <th class="px-5 lg:px-10 py-8">Kategori / Kode</th>
                    <th class="px-5 lg:px-10 py-8">Harga Jual</th>
                    <th class="px-5 lg:px-10 py-8">Status Stok</th>
                    <th class="px-5 lg:px-10 py-8">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                    <tr class="hover:bg-primary-50/30 transition duration-300">
                        <td class="px-5 lg:px-10 py-8">
                            <div class="flex items-center gap-6">
                                <div class="w-20 h-20 rounded-[1.5rem] bg-gray-100 overflow-hidden flex-shrink-0 shadow-inner border border-gray-100">
                                    <img src="{{ $product->image ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&h=200' }}" alt="{{ $product->name }}" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=200&h=200'">
                                </div>
                                <div class="space-y-1">
                                    <p class="font-black text-xl text-gray-900 leading-tight">{{ $product->name }}</p>
                                    <p class="text-sm font-medium text-gray-400 line-clamp-1">{{ $product->description ?? 'Tidak ada deskripsi' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            <div class="flex flex-col gap-1">
                                <span class="px-4 py-2 bg-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-400 w-max">Lifestyle</span>
                                <span class="text-xs font-bold text-gray-300 tracking-widest uppercase">ID-{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            <p class="text-2xl font-black text-gray-900 tracking-tight">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            <div class="flex flex-col gap-2">
                                <span class="{{ $product->stock > 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest w-max">
                                    {{ $product->stock }} Tersedia
                                </span>
                                @if($product->stock <= 5)
                                    <p class="text-[10px] font-bold text-red-400 animate-pulse uppercase">Segera Restock!</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 lg:px-10 py-8">
                            <div class="flex items-center gap-4">
                                <a href="/{{ Auth::user()->role }}/products/{{ $product->id }}/edit" class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 hover:bg-primary-600 hover:text-white hover:scale-110 active:scale-95 transition-all shadow-sm border border-gray-100">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <form action="/web/product/{{ $product->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 hover:bg-red-500 hover:text-white hover:scale-110 active:scale-95 transition-all shadow-sm border border-gray-100">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-10 lg:px-20 py-16 lg:py-32 text-center text-gray-400 font-bold space-y-4">
                            <div class="text-4xl lg:text-6xl mb-6">📦</div>
                            <p class="text-2xl text-gray-900 font-black">Belum Ada Produk.</p>
                            <p class="text-sm font-medium">Mulailah dengan menambahkan produk pertama toko Anda.</p>
                            <div class="pt-8">
                                <a href="/{{ Auth::user()->role }}/products/create" class="px-5 lg:px-10 py-5 bg-primary-600 text-white rounded-2xl lg:rounded-3xl font-black text-lg">Tambah Sekarang</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
