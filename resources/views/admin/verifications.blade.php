@extends('layouts.dashboard')

@section('title', 'Verifikasi Pendaftar — Admin Arradea')
@section('page_title', 'Verifikasi Pengguna')

@section('content')
<div class="space-y-6 lg:space-y-12" x-data="{ tab: '{{ $pendingBuyers->total() > 0 ? 'buyers' : 'sellers' }}' }">

    {{-- Header --}}
    <div class="bg-white p-6 lg:p-12 rounded-2xl lg:rounded-[4rem] shadow-sm border border-gray-100">
        <div class="max-w-3xl">
            <h1 class="text-4xl lg:text-6xl font-black text-gray-900 tracking-tighter leading-tight mb-4">
                Verifikasi <span class="text-blue-600 underline underline-offset-8">Pengguna</span>.
            </h1>
            <p class="text-gray-500 text-lg font-medium">
                Dua antrian: pendaftar buyers baru dan buyers yang mau upgrade jadi seller. Tinjau dan putuskan.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-5 bg-green-50 border border-green-100 rounded-3xl text-green-700 font-bold">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-5 bg-red-50 border border-red-100 rounded-3xl text-red-700 font-bold">
            ❌ {{ $errors->first() }}
        </div>
    @endif

    {{-- Tab switcher --}}
    <div class="flex gap-3">
        <button @click="tab = 'buyers'"
            :class="tab === 'buyers' ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50'"
            class="flex items-center gap-3 px-6 py-4 rounded-2xl font-black text-sm transition-all">
            🧑‍🤝‍🧑 Buyer Baru
            @if($pendingBuyers->total() > 0)
                <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-black text-white bg-red-500 rounded-full animate-bounce">
                    {{ $pendingBuyers->total() }}
                </span>
            @endif
        </button>

        <button @click="tab = 'sellers'"
            :class="tab === 'sellers' ? 'bg-orange-500 text-white shadow-lg shadow-orange-200' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50'"
            class="flex items-center gap-3 px-6 py-4 rounded-2xl font-black text-sm transition-all">
            🏪 Calon Seller
            @if($pendingSellers->total() > 0)
                <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-black text-white bg-red-500 rounded-full animate-bounce">
                    {{ $pendingSellers->total() }}
                </span>
            @endif
        </button>
    </div>

    {{-- ─────────── TAB: BUYER BARU ─────────── --}}
    <div x-show="tab === 'buyers'" x-transition>
        <div class="bg-white rounded-2xl lg:rounded-[4rem] p-6 lg:p-12 shadow-sm border border-gray-100 space-y-8">
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tighter">
                Pendaftar Buyer Baru
                <span class="text-primary-600">({{ $pendingBuyers->total() }})</span>
            </h2>

            @if($pendingBuyers->isEmpty())
                <div class="flex flex-col items-center justify-center p-16 text-center bg-gray-50/50 rounded-3xl border border-dashed border-gray-200">
                    <span class="text-5xl mb-4">🎉</span>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Semua Bersih!</h3>
                    <p class="text-gray-500">Tidak ada pendaftar buyer baru yang menunggu persetujuan.</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-2xl lg:rounded-[3rem] border border-gray-100">
                    <table class="w-full text-left">
                        <thead class="text-xs font-black tracking-widest uppercase text-gray-400 border-b border-gray-100 bg-gray-50/50">
                            <tr>
                                <th class="px-6 lg:px-10 py-5">Data Pengguna</th>
                                <th class="px-6 lg:px-10 py-5">No. WhatsApp</th>
                                <th class="px-6 lg:px-10 py-5">Tgl Verifikasi HP</th>
                                <th class="px-6 lg:px-10 py-5">Lokasi</th>
                                <th class="px-6 lg:px-10 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($pendingBuyers as $user)
                                <tr class="hover:bg-blue-50/30 transition duration-200">
                                    <td class="px-6 lg:px-10 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center font-black text-blue-600 text-xl animate-pulse">⏳</div>
                                            <div>
                                                <p class="font-black text-lg text-gray-900">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-400 font-bold mt-0.5">Calon Buyer</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 lg:px-10 py-6">
                                        <span class="px-4 py-2 bg-green-50 text-green-700 rounded-xl text-xs font-black border border-green-100">
                                            📱 {{ $user->phone }}
                                        </span>
                                    </td>
                                    <td class="px-6 lg:px-10 py-6">
                                        <p class="text-gray-700 font-bold text-sm">{{ $user->phone_verified_at->format('d M Y, H:i') }}</p>
                                    </td>
                                    <td class="px-6 lg:px-10 py-6">
                                        @if(!is_null($user->latitude) && !is_null($user->longitude))
                                            <div class="space-y-2">
                                                <p class="text-gray-900 font-black text-xs lg:text-sm leading-tight">{{ number_format((float) $user->latitude, 7) }}, {{ number_format((float) $user->longitude, 7) }}</p>
                                                <button type="button"
                                                    onclick='openVerificationLocationModal(@json(["name" => $user->name, "latitude" => $user->latitude, "longitude" => $user->longitude]))'
                                                    class="px-4 py-2 bg-primary-50 text-primary-700 rounded-xl text-xs font-black hover:bg-primary-100 transition-all">
                                                    Cek Lokasi
                                                </button>
                                            </div>
                                        @else
                                            <p class="text-gray-400 font-bold text-xs">Lokasi tidak tersedia</p>
                                        @endif
                                    </td>
                                    <td class="px-6 lg:px-10 py-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.verifications.approve', $user) }}" onsubmit="return confirmSubmit(event, @js('Setujui akun buyer ini?'))">
                                                @csrf
                                                <button type="submit" class="px-5 py-3 bg-primary-600 text-white rounded-xl font-black text-sm hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    Terima
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.verifications.reject', $user) }}" onsubmit="return confirmSubmit(event, @js('Tolak & hapus akun ini?'))">
                                                @csrf
                                                <button type="submit" class="px-5 py-3 bg-red-50 border border-red-100 text-red-600 rounded-xl font-black text-sm hover:bg-red-100 transition-all">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">{{ $pendingBuyers->links() }}</div>
            @endif
        </div>
    </div>

    {{-- ─────────── TAB: CALON SELLER ─────────── --}}
    <div x-show="tab === 'sellers'" x-transition>
        <div class="bg-white rounded-2xl lg:rounded-[4rem] p-6 lg:p-12 shadow-sm border border-gray-100 space-y-8">
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tighter">
                Pengajuan Upgrade Seller
                <span class="text-orange-500">({{ $pendingSellers->total() }})</span>
            </h2>

            @if($pendingSellers->isEmpty())
                <div class="flex flex-col items-center justify-center p-16 text-center bg-gray-50/50 rounded-3xl border border-dashed border-gray-200">
                    <span class="text-5xl mb-4">🏪</span>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Tidak Ada Pengajuan!</h3>
                    <p class="text-gray-500">Belum ada buyer yang mengajukan upgrade jadi seller.</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-2xl lg:rounded-[3rem] border border-gray-100">
                    <table class="w-full text-left">
                        <thead class="text-xs font-black tracking-widest uppercase text-gray-400 border-b border-gray-100 bg-gray-50/50">
                            <tr>
                                <th class="px-6 lg:px-10 py-5">Data Penjual</th>
                                <th class="px-6 lg:px-10 py-5">Nama Toko</th>
                                <th class="px-6 lg:px-10 py-5">Tgl Pengajuan</th>
                                <th class="px-6 lg:px-10 py-5">Lokasi</th>
                                <th class="px-6 lg:px-10 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($pendingSellers as $user)
                                <tr class="hover:bg-orange-50/30 transition duration-200">
                                    <td class="px-6 lg:px-10 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-xl animate-pulse">🏪</div>
                                            <div>
                                                <p class="font-black text-lg text-gray-900">{{ $user->name }}</p>
                                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-black border border-blue-100">
                                                    Buyer aktif → mau jadi Seller
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 lg:px-10 py-6">
                                        @if($user->store)
                                            <p class="font-black text-gray-900">{{ $user->store->name }}</p>
                                            @if($user->store->address)
                                                <p class="text-xs text-gray-400 mt-1">📍 {{ $user->store->address }}</p>
                                            @endif
                                        @else
                                            <span class="text-gray-400 italic text-sm">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 lg:px-10 py-6">
                                        <p class="text-gray-700 font-bold text-sm">{{ $user->seller_applied_at ? \Carbon\Carbon::parse($user->seller_applied_at)->format('d M Y, H:i') : '—' }}</p>
                                    </td>
                                    <td class="px-6 lg:px-10 py-6">
                                        @if(!is_null($user->latitude) && !is_null($user->longitude))
                                            <div class="space-y-2">
                                                <p class="text-gray-900 font-black text-xs lg:text-sm leading-tight">{{ number_format((float) $user->latitude, 7) }}, {{ number_format((float) $user->longitude, 7) }}</p>
                                                <button type="button"
                                                    onclick='openVerificationLocationModal(@json(["name" => $user->name, "latitude" => $user->latitude, "longitude" => $user->longitude]))'
                                                    class="px-4 py-2 bg-primary-50 text-primary-700 rounded-xl text-xs font-black hover:bg-primary-100 transition-all">
                                                    Cek Lokasi
                                                </button>
                                            </div>
                                        @else
                                            <p class="text-gray-400 font-bold text-xs">Lokasi tidak tersedia</p>
                                        @endif
                                    </td>
                                    <td class="px-6 lg:px-10 py-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.verifications.approve-seller', $user) }}" onsubmit="return confirmSubmit(event, @js('Setujui ' . $user->name . ' jadi Seller?'))">
                                                @csrf
                                                <button type="submit" class="px-5 py-3 bg-orange-500 text-white rounded-xl font-black text-sm hover:bg-orange-600 shadow-lg shadow-orange-200 transition-all flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    Setujui Seller
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.verifications.reject-seller', $user) }}" onsubmit="return confirmSubmit(event, @js('Tolak pengajuan seller ' . $user->name . '?'))">
                                                @csrf
                                                <button type="submit" class="px-5 py-3 bg-red-50 border border-red-100 text-red-600 rounded-xl font-black text-sm hover:bg-red-100 transition-all">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">{{ $pendingSellers->links() }}</div>
            @endif
        </div>
    </div>

</div>

<div id="verification-location-modal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white rounded-[3rem] w-full max-w-3xl p-8 lg:p-10 relative shadow-2xl"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-8">

        <button type="button" onclick="closeVerificationLocationModal()" class="absolute top-8 right-8 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <h3 class="text-3xl font-black text-gray-900 mb-3">Cek Lokasi</h3>
        <p id="verification-location-name" class="text-sm font-medium text-gray-400 mb-6"></p>

        <div id="verification-location-content"></div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
    window.renderVerificationUserMap = function (user) {
        const contentElement = document.getElementById('verification-location-content');
        if (!contentElement || !user) {
            return;
        }

        const hasLocation = user.latitude !== null && user.latitude !== '' && user.longitude !== null && user.longitude !== '';
        if (!hasLocation) {
            contentElement.innerHTML = '<div class="p-6 bg-amber-50 border border-amber-100 rounded-2xl text-amber-800 font-bold text-sm">Lokasi tidak tersedia</div>';
            return;
        }

        contentElement.innerHTML = '<div class="space-y-4"><div class="rounded-3xl overflow-hidden border border-gray-100 shadow-sm"><div id="verification-user-map" class="w-full h-80 bg-gray-100"></div></div><p class="text-sm font-bold text-gray-700" id="verification-location-coordinates"></p></div>';

        const mapElement = document.getElementById('verification-user-map');
        const coordinatesElement = document.getElementById('verification-location-coordinates');

        const latitude = parseFloat(user.latitude);
        const longitude = parseFloat(user.longitude);

        if (Number.isNaN(latitude) || Number.isNaN(longitude)) {
            contentElement.innerHTML = '<div class="p-6 bg-amber-50 border border-amber-100 rounded-2xl text-amber-800 font-bold text-sm">Lokasi tidak tersedia</div>';
            return;
        }

        if (coordinatesElement) {
            coordinatesElement.textContent = `📍 ${latitude.toFixed(7)}, ${longitude.toFixed(7)}`;
        }

        if (!window.L) {
            contentElement.innerHTML = '<div class="p-6 bg-amber-50 border border-amber-100 rounded-2xl text-amber-800 font-bold text-sm">Peta belum bisa dimuat.</div>';
            return;
        }

        contentElement.innerHTML = '<div class="space-y-4"><div class="rounded-3xl overflow-hidden border border-gray-100 shadow-sm"><div id="verification-user-map" class="w-full h-80 bg-gray-100"></div></div><p class="text-sm font-bold text-gray-700" id="verification-location-coordinates"></p></div>';

        const renderMap = function () {
            const map = L.map('verification-user-map', {
                scrollWheelZoom: false,
            }).setView([latitude, longitude], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(map);

            L.marker([latitude, longitude]).addTo(map).bindPopup(user.name || 'Lokasi User').openPopup();

            setTimeout(function () {
                map.invalidateSize();
            }, 150);
        };

        renderMap();
    };

    window.openVerificationLocationModal = function (user) {
        const modal = document.getElementById('verification-location-modal');
        const nameElement = document.getElementById('verification-location-name');

        if (!modal || !nameElement) {
            return;
        }

        nameElement.textContent = user && user.name ? user.name : '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        window.renderVerificationUserMap(user);
    };

    window.closeVerificationLocationModal = function () {
        const modal = document.getElementById('verification-location-modal');
        const contentElement = document.getElementById('verification-location-content');

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');

        if (contentElement) {
            contentElement.innerHTML = '';
        }
    };
</script>
@endsection
