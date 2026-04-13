@extends('layouts.dashboard')

@section('title', 'Manajemen Pengguna - Arradea Admin')
@section('page_title', 'Semua Pengguna')

@section('content')
<div class="space-y-6 lg:space-y-12" x-data="{ openEditModal: false, editUser: null }">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-end gap-6 lg:gap-12 bg-white p-6 lg:p-12 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-[4rem] shadow-sm border border-gray-100">
        <div class="max-w-2xl text-center md:text-left">
            <h1 class="text-4xl lg:text-6xl font-black text-gray-900 tracking-tighter leading-tight mb-4">Master <span class="text-blue-600 underline underline-offset-8">Data Pengguna</span>.</h1>
            <p class="text-gray-500 text-lg font-medium">Kelola seluruh pengguna mulai dari Pembeli hingga Penjual. Lakukan pengeditan dan penghapusan data secara langsung.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="p-6 bg-red-50 border border-red-100 rounded-[2rem] text-red-600 font-bold text-sm shadow-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Management Table -->
    <div class="bg-white rounded-2xl lg:rounded-[4rem] p-6 lg:p-12 shadow-sm border border-gray-100 space-y-6 lg:space-y-12">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6 px-2 lg:px-4">
            <div>
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tighter leading-none mb-4">Total <span class="text-primary-600">{{ $users->total() }}</span> Akun.</h2>
            </div>
            
            <!-- Filters -->
            <div class="flex flex-wrap gap-2 lg:gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-xs lg:text-sm transition-all {{ !request('type') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">Semua</a>
                <a href="{{ route('admin.users.index', ['type' => 'buyer']) }}" class="px-5 py-2.5 rounded-xl font-bold text-xs lg:text-sm transition-all {{ request('type') == 'buyer' ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">Buyer</a>
                <a href="{{ route('admin.users.index', ['type' => 'seller']) }}" class="px-5 py-2.5 rounded-xl font-bold text-xs lg:text-sm transition-all {{ request('type') == 'seller' ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">Seller</a>
                <a href="{{ route('admin.users.index', ['type' => 'admin']) }}" class="px-5 py-2.5 rounded-xl font-bold text-xs lg:text-sm transition-all {{ request('type') == 'admin' ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">Admin</a>
            </div>
        </div>
        
        <div class="overflow-x-auto shadow-inner rounded-2xl lg:rounded-[3rem] border border-gray-50">
            <table class="w-full text-left">
                <thead class="text-xs font-black tracking-widest uppercase text-gray-400 border-b border-gray-100 bg-gray-50/50">
                    <tr>
                        <th class="px-5 lg:px-10 py-5 lg:py-10">Data Pengguna</th>
                        <th class="px-5 lg:px-10 py-5 lg:py-10">Tipe Akun</th>
                        <th class="px-5 lg:px-10 py-5 lg:py-10">Tgl Daftar</th>
                        <th class="px-5 lg:px-10 py-5 lg:py-10 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                        <tr class="hover:bg-primary-50/20 transition duration-300">
                            <td class="px-5 lg:px-10 py-5 lg:py-10">
                                <div class="flex items-center gap-6">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 font-black text-xs border border-gray-100 shadow-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-xl text-gray-900 leading-tight">{{ $user->name }}</p>
                                        <p class="text-sm font-medium text-gray-400 italic">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 lg:px-10 py-5 lg:py-10">
                                <span class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest 
                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($user->is_seller ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $user->role === 'admin' ? 'admin' : ($user->is_seller ? 'seller + buyer' : 'buyer') }}
                                </span>
                            </td>
                            <td class="px-5 lg:px-10 py-5 lg:py-10">
                                <p class="text-gray-900 font-bold text-sm">{{ $user->created_at->format('d M Y') }}</p>
                            </td>
                            <td class="px-5 lg:px-10 py-5 lg:py-10 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" @click="openEditModal = true; editUser = {{ json_encode($user) }}" class="px-5 py-3 bg-gray-50 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-200 transition-all">Edit</button>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-5 py-3 bg-red-50 text-red-600 rounded-xl font-bold text-xs hover:bg-red-200 transition-all">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Edit User Modal -->
    <div x-show="openEditModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4" x-cloak>
        <div @click.away="openEditModal = false" class="bg-white rounded-[3rem] w-full max-w-lg p-10 relative shadow-2xl"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-8"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-8">
            
            <button @click="openEditModal = false" class="absolute top-8 right-8 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <h3 class="text-3xl font-black text-gray-900 mb-8">Edit Pengguna</h3>

            <form method="POST" :action="`/admin/users/${editUser.id}`" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-2">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-2">Nama Lengkap</label>
                    <input type="text" name="name" x-model="editUser.name" required class="w-full h-16 bg-gray-50 border-none rounded-2xl px-6 font-bold focus:ring-4 focus:ring-primary-100">
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-2">Alamat Email</label>
                    <input type="email" name="email" x-model="editUser.email" required class="w-full h-16 bg-gray-50 border-none rounded-2xl px-6 font-bold focus:ring-4 focus:ring-primary-100">
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-2">Mode Seller</label>
                    <select name="is_seller" x-model="editUser.is_seller" required class="w-full h-16 bg-gray-50 border-none rounded-2xl px-6 font-bold focus:ring-4 focus:ring-primary-100">
                        <option :value="0">Buyer Saja</option>
                        <option :value="1">Seller + Buyer</option>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-2 italic px-2">*Mode seller hanya memengaruhi akses jual. Semua akun tetap bisa membeli.</p>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full h-16 bg-primary-600 text-white rounded-2xl font-black text-lg shadow-xl shadow-primary-200 hover:bg-primary-700 transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
