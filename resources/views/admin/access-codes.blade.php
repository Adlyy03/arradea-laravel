@extends('layouts.dashboard')
@section('title', 'Kode Akses — Arradea Admin')
@section('page_title', 'Kode Akses')

@section('content')
<div class="space-y-5 fade-up" x-data="{ openCreate: false, newCode: '' }">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Kode <span style="color:#72bf77">Akses</span></h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola kode akses untuk verifikasi pendaftar baru.</p>
        </div>
        <button @click="openCreate=true" class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition hover:opacity-90" style="background:#72bf77">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Buat Kode Baru
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400">Kode</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hidden sm:table-cell">Dipakai</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hidden md:table-cell">Dibuat</th>
                        <th class="text-right px-5 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($codes as $code)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <code class="font-black text-gray-900 bg-gray-100 px-3 py-1.5 rounded-lg text-sm tracking-widest">{{ $code->code }}</code>
                                <button onclick="navigator.clipboard.writeText('{{ $code->code }}').then(()=>window.arradeaPopup.success('Kode disalin!'))"
                                    class="text-gray-400 hover:text-gray-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase {{ $code->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $code->is_active ? '✓ Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 hidden sm:table-cell">
                            <span class="font-bold text-gray-900">{{ $code->users_count }}</span>
                            <span class="text-xs text-gray-400 ml-1">user</span>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500 hidden md:table-cell">{{ $code->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <form method="POST" action="{{ route('admin.access-codes.toggle', $code) }}">
                                    @csrf @method('PATCH')
                                    <button class="px-3 py-1.5 rounded-lg text-[10px] font-bold transition {{ $code->is_active ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'text-white hover:opacity-80' }}" style="{{ $code->is_active ? '' : 'background:#72bf77' }}">
                                        {{ $code->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                @if($code->users_count === 0)
                                <form method="POST" action="{{ route('admin.access-codes.destroy', $code) }}" onsubmit="return confirmSubmit(event, 'Hapus kode akses ini?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-lg text-[10px] font-bold bg-red-50 text-red-500 hover:bg-red-100 transition">Hapus</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-50">{{ $codes->links() }}</div>
    </div>

    {{-- Create Modal --}}
    <div x-show="openCreate" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
        <div @click.away="openCreate=false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="bg-white rounded-3xl w-full max-w-sm p-7 shadow-2xl">
            <h3 class="text-xl font-black text-gray-900 mb-5">Buat Kode Akses Baru</h3>
            <form method="POST" action="{{ route('admin.access-codes.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Kode Akses</label>
                    <input type="text" name="code" x-model="newCode" required maxlength="100"
                        class="w-full h-12 bg-gray-50 border border-gray-200 rounded-xl px-4 text-sm font-mono font-bold tracking-widest focus:outline-none focus:ring-2 transition uppercase"
                        placeholder="ARRADEA2026" style="--tw-ring-color:rgba(114,191,119,.4)">
                    <p class="text-[11px] text-gray-400 mt-1.5">Kode akan otomatis diubah ke UPPERCASE.</p>
                </div>
                <div class="flex gap-2 pt-1">
                    <button type="button" @click="openCreate=false" class="flex-1 h-11 rounded-xl font-bold text-sm bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Batal</button>
                    <button type="submit" class="flex-1 h-11 rounded-xl font-bold text-sm text-white transition hover:opacity-90" style="background:#72bf77">Buat Kode</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
