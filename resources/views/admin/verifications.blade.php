@extends('layouts.dashboard')
@section('title', 'Verifikasi Pendaftar — Admin Arradea')
@section('page_title', 'Verifikasi Pendaftar')

@section('content')
<div class="space-y-5 fade-up" x-data="{ tab: '{{ $pendingBuyers->total() > 0 ? 'buyers' : 'sellers' }}' }">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-black text-gray-900">Verifikasi <span style="color:#72bf77">Pendaftar</span></h1>
        <p class="text-sm text-gray-500 mt-0.5">Tinjau dan setujui pendaftar buyer baru & upgrade seller.</p>
    </div>

    {{-- Tab Switcher --}}
    <div class="flex gap-2">
        <button @click="tab='buyers'" :class="tab==='buyers' ? 'text-white shadow-lg' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300'"
            class="flex items-center gap-2.5 px-5 py-2.5 rounded-xl font-bold text-sm transition-all"
            :style="tab==='buyers' ? 'background:#72bf77' : ''">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Buyer Baru
            @if($pendingBuyers->total() > 0)
                <span class="w-5 h-5 rounded-full text-[9px] font-black flex items-center justify-center bg-red-500 text-white">{{ $pendingBuyers->total() }}</span>
            @endif
        </button>
        <button @click="tab='sellers'" :class="tab==='sellers' ? 'text-white shadow-lg' : 'bg-white border border-gray-200 text-gray-500 hover:border-gray-300'"
            class="flex items-center gap-2.5 px-5 py-2.5 rounded-xl font-bold text-sm transition-all"
            :style="tab==='sellers' ? 'background:#f59e0b' : ''">
            🏪 Calon Seller
            @if($pendingSellers->total() > 0)
                <span class="w-5 h-5 rounded-full text-[9px] font-black flex items-center justify-center bg-red-500 text-white">{{ $pendingSellers->total() }}</span>
            @endif
        </button>
    </div>

    {{-- TAB: BUYER BARU --}}
    <div x-show="tab==='buyers'" x-transition>
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest">Pendaftar Buyer Baru ({{ $pendingBuyers->total() }})</h2>
            </div>
            @if($pendingBuyers->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <span class="text-4xl mb-3">🎉</span>
                    <p class="font-bold text-gray-700">Semua bersih!</p>
                    <p class="text-sm text-gray-400 mt-1">Tidak ada pendaftar buyer yang menunggu.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/70">
                                <th class="text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Pengguna</th>
                                <th class="text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400 hidden sm:table-cell">Lokasi</th>
                                <th class="text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400 hidden md:table-cell">Verif HP</th>
                                <th class="text-right px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($pendingBuyers as $user)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0" style="background:rgba(114,191,119,.12);color:#3fa348">{{ strtoupper(substr($user->name,0,1)) }}</div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-400">📱 {{ $user->phone }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 hidden sm:table-cell">
                                    @if($user->latitude && $user->longitude)
                                        <button type="button"
                                            onclick='openVerificationLocationModal(@json(["name"=>$user->name,"latitude"=>$user->latitude,"longitude"=>$user->longitude]))'
                                            class="px-3 py-1.5 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-600 hover:bg-blue-100 transition">📍 Cek Map</button>
                                    @else
                                        <span class="text-xs text-gray-400">Tidak tersedia</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500 hidden md:table-cell">{{ $user->phone_verified_at?->format('d M Y') }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form method="POST" action="{{ route('admin.verifications.approve', $user) }}" onsubmit="return confirmSubmit(event, @js('Setujui akun buyer ini?'))">
                                            @csrf
                                            <button class="px-3 py-1.5 rounded-lg text-[10px] font-black text-white" style="background:#72bf77">Terima</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.verifications.reject', $user) }}" onsubmit="return confirmSubmit(event, @js('Tolak & hapus akun ini?'))">
                                            @csrf
                                            <button class="px-3 py-1.5 rounded-lg text-[10px] font-black bg-red-50 text-red-500 hover:bg-red-100 transition">Tolak</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4 border-t border-gray-50">{{ $pendingBuyers->links() }}</div>
            @endif
        </div>
    </div>

    {{-- TAB: CALON SELLER --}}
    <div x-show="tab==='sellers'" x-transition>
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                <h2 class="text-sm font-black text-gray-700 uppercase tracking-widest">Pengajuan Upgrade Seller ({{ $pendingSellers->total() }})</h2>
            </div>
            @if($pendingSellers->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <span class="text-4xl mb-3">🏪</span>
                    <p class="font-bold text-gray-700">Tidak ada pengajuan seller.</p>
                    <p class="text-sm text-gray-400 mt-1">Belum ada buyer yang mengajukan upgrade jadi seller.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/70">
                                <th class="text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Penjual</th>
                                <th class="text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400 hidden sm:table-cell">Nama Toko</th>
                                <th class="text-left px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400 hidden md:table-cell">Tgl Ajukan</th>
                                <th class="text-right px-5 py-3 text-[10px] font-black uppercase tracking-widest text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($pendingSellers as $user)
                            <tr class="hover:bg-amber-50/30 transition">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0 text-amber-700" style="background:rgba(245,158,11,.12)">{{ strtoupper(substr($user->name,0,1)) }}</div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $user->phone }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 hidden sm:table-cell">
                                    <p class="font-medium text-gray-700">{{ $user->store->name ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->store->address ?? '' }}</p>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500 hidden md:table-cell">{{ $user->seller_applied_at ? \Carbon\Carbon::parse($user->seller_applied_at)->format('d M Y') : '—' }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form method="POST" action="{{ route('admin.verifications.approve-seller', $user) }}" onsubmit="return confirmSubmit(event, @js('Setujui '.$user->name.' jadi Seller?'))">
                                            @csrf
                                            <button class="px-3 py-1.5 rounded-lg text-[10px] font-black text-white" style="background:#72bf77">Setujui</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.verifications.reject-seller', $user) }}" onsubmit="return confirmSubmit(event, @js('Tolak pengajuan '.$user->name.'?'))">
                                            @csrf
                                            <button class="px-3 py-1.5 rounded-lg text-[10px] font-black bg-red-50 text-red-500 hover:bg-red-100 transition">Tolak</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4 border-t border-gray-50">{{ $pendingSellers->links() }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Location Modal --}}
<div id="verification-location-modal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white rounded-3xl w-full max-w-xl p-6 relative shadow-2xl">
        <button type="button" onclick="closeVerificationLocationModal()" class="absolute top-4 right-4 w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-400 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <h3 class="text-xl font-black text-gray-900 mb-1">Cek Lokasi</h3>
        <p id="verification-location-name" class="text-sm text-gray-400 mb-4"></p>
        <div id="verification-location-content"></div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
    window.renderVerificationUserMap = function(user) {
        const contentEl = document.getElementById('verification-location-content');
        if (!contentEl || !user) return;
        const hasLoc = user.latitude && user.longitude;
        if (!hasLoc) { contentEl.innerHTML = '<div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-amber-700 text-sm font-medium">Lokasi tidak tersedia</div>'; return; }
        contentEl.innerHTML = '<div id="verification-user-map" class="w-full h-64 rounded-2xl border border-gray-200"></div><p class="text-xs text-gray-400 mt-2" id="verification-location-coordinates"></p>';
        const lat = parseFloat(user.latitude), lng = parseFloat(user.longitude);
        document.getElementById('verification-location-coordinates').textContent = `📍 ${lat.toFixed(7)}, ${lng.toFixed(7)}`;
        if (!window.L) return;
        const map = L.map('verification-user-map', {scrollWheelZoom:false}).setView([lat,lng],16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'© OpenStreetMap'}).addTo(map);
        L.marker([lat,lng]).addTo(map).bindPopup(user.name||'Lokasi').openPopup();
        setTimeout(() => map.invalidateSize(), 150);
    };
    window.openVerificationLocationModal = function(user) {
        const modal = document.getElementById('verification-location-modal');
        document.getElementById('verification-location-name').textContent = user?.name || '';
        modal.classList.remove('hidden'); modal.classList.add('flex');
        window.renderVerificationUserMap(user);
    };
    window.closeVerificationLocationModal = function() {
        const modal = document.getElementById('verification-location-modal');
        modal.classList.add('hidden'); modal.classList.remove('flex');
        document.getElementById('verification-location-content').innerHTML = '';
    };
</script>
@endsection
