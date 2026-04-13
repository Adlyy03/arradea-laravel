@extends('layouts.dashboard')

@section('title', 'Manajemen Kode Akses - Arradea Admin')
@section('page_title', 'Kelola Kode Akses Komplek')

@section('content')
<div class="space-y-6 lg:space-y-10">
    <div class="bg-white p-6 lg:p-10 rounded-3xl border border-gray-100 shadow-sm">
        <h1 class="text-3xl lg:text-5xl font-black text-gray-900 tracking-tight">Manajemen <span class="text-primary-600">Access Code</span></h1>
        <p class="mt-3 text-gray-500 font-medium">Gunakan halaman ini untuk membuat, menonaktifkan, atau menghapus kode akses warga Komplek Arradea.</p>
    </div>

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-100 rounded-2xl text-red-700 font-bold text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-100 rounded-2xl text-green-700 font-bold text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 lg:p-8 rounded-3xl border border-gray-100 shadow-sm">
        <h2 class="text-xl font-black text-gray-900 mb-4">Buat Kode Akses Baru</h2>
        <form method="POST" action="{{ route('admin.access-codes.store') }}" class="flex flex-col md:flex-row gap-3">
            @csrf
            <input
                type="text"
                name="code"
                required
                value="{{ old('code') }}"
                placeholder="Contoh: ARRADEA2027"
                class="flex-1 h-12 rounded-xl border border-gray-200 bg-gray-50 px-4 font-bold focus:outline-none focus:ring-2 focus:ring-primary-500"
            >
            <button type="submit" class="h-12 px-6 rounded-xl bg-primary-600 text-white font-black hover:bg-primary-700 transition">
                Tambah Kode
            </button>
        </form>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[11px] uppercase tracking-widest text-gray-400 font-black">
                    <tr>
                        <th class="px-6 py-4">Kode</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Dipakai User</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($codes as $code)
                        <tr>
                            <td class="px-6 py-4 font-black text-gray-900">{{ $code->code }}</td>
                            <td class="px-6 py-4">
                                @if($code->is_active)
                                    <span class="px-3 py-1 rounded-lg bg-green-100 text-green-700 text-[10px] font-black uppercase">Aktif</span>
                                @else
                                    <span class="px-3 py-1 rounded-lg bg-gray-100 text-gray-600 text-[10px] font-black uppercase">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-700">{{ $code->users_count }} akun</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.access-codes.toggle', $code) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-4 py-2 rounded-lg bg-amber-50 text-amber-700 text-xs font-black hover:bg-amber-100 transition">
                                            {{ $code->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.access-codes.destroy', $code) }}" onsubmit="return confirm('Yakin hapus kode ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 rounded-lg bg-red-50 text-red-700 text-xs font-black hover:bg-red-100 transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 font-bold">Belum ada kode akses.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $codes->links() }}
        </div>
    </div>
</div>
@endsection
