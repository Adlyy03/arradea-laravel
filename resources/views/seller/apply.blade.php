@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 lg:py-16 px-4">
    <div class="bg-white rounded-2xl lg:rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-5 lg:p-10 grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-10">
            <div class="lg:col-span-2 space-y-8">
                <div>
                    <h1 class="text-4xl font-black text-gray-900">Daftar Jadi Seller</h1>
                    <p class="mt-3 text-gray-500">Lengkapi data toko untuk mengaktifkan mode seller. Akun tetap bisa dipakai untuk belanja seperti biasa.</p>
                </div>

                @if(session('success'))
                    <div class="rounded-2xl lg:rounded-3xl border border-green-100 bg-green-50 p-6 text-green-700 font-bold">{{ session('success') }}</div>
                @endif

                @if($user->is_seller)
                    <div class="rounded-2xl lg:rounded-3xl border border-green-100 bg-green-50 p-8">
                        <h2 class="text-2xl font-black text-gray-900">Akun Seller Aktif</h2>
                        <p class="mt-4 text-gray-600">Mode seller sudah aktif. Anda bisa jualan dan tetap bisa checkout sebagai pembeli.</p>
                        <a href="{{ route('seller.dashboard') }}" class="inline-flex mt-6 items-center justify-center rounded-2xl lg:rounded-3xl bg-primary-700 text-white px-6 py-4 font-black hover:bg-primary-800 transition">Buka Dashboard Seller</a>
                    </div>
                @else
                    <form action="{{ route('seller.apply.store') }}" method="POST" class="space-y-8">
                        @csrf

                        <div class="rounded-2xl lg:rounded-3xl border border-gray-100 p-8 bg-gray-50">
                            <h2 class="text-xl font-black text-gray-900 mb-4">Informasi Toko</h2>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-black uppercase tracking-widest text-gray-400 mb-3">Nama Toko</label>
                                    <input type="text" name="store_name" value="{{ old('store_name', $user->store->name ?? '') }}" required class="w-full rounded-2xl border border-gray-200 bg-white px-6 py-4 text-lg font-bold focus:border-primary-600 focus:outline-none" placeholder="Contoh: Toko Gadget Jakarta">
                                </div>

                                <div>
                                    <label class="block text-sm font-black uppercase tracking-widest text-gray-400 mb-3">Deskripsi Toko</label>
                                    <textarea name="store_description" rows="5" class="w-full rounded-2xl border border-gray-200 bg-white px-6 py-4 text-lg font-bold focus:border-primary-600 focus:outline-none" placeholder="Jelaskan singkat tentang toko dan produk Anda.">{{ old('store_description', $user->store->description ?? '') }}</textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-black uppercase tracking-widest text-gray-400 mb-3">Alamat Toko</label>
                                    <input
                                        type="text"
                                        id="store_address"
                                        name="store_address"
                                        value="{{ old('store_address', $user->store->address ?? '') }}"
                                        class="w-full rounded-2xl border border-gray-200 bg-white px-6 py-4 text-lg font-bold focus:border-primary-600 focus:outline-none"
                                        placeholder="Contoh: Jalan Merdeka No. 22, Jakarta"
                                    >
                                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $user->latitude) }}">
                                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $user->longitude) }}">

                                    <div class="mt-4 space-y-3">
                                        <button
                                            type="button"
                                            id="detect-location-btn"
                                            class="inline-flex items-center gap-2 rounded-xl bg-primary-700 text-white px-4 py-2 font-black hover:bg-primary-800 transition"
                                        >
                                            Ambil Lokasi dari Browser
                                        </button>
                                        <p id="location-status" class="text-xs text-gray-500 font-bold">
                                            Tekan tombol untuk ambil koordinat dan isi alamat otomatis.
                                        </p>

                                        <div id="location-map-wrapper" class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white">
                                            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                                                <p class="text-xs font-black uppercase tracking-wider text-gray-500">Pratinjau Titik Lokasi</p>
                                                <p id="location-map-coordinates" class="text-sm font-bold text-gray-700 mt-1">-</p>
                                            </div>
                                            <div id="seller-location-map" class="h-64 w-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-4">
                            <button type="submit" class="w-full rounded-2xl lg:rounded-3xl bg-primary-700 text-white px-8 py-5 font-black text-lg hover:bg-primary-800 transition">Aktifkan Seller Sekarang</button>
                            <p class="text-sm text-gray-500">Setelah diaktifkan, akun Anda langsung mendapat akses jual tanpa kehilangan akses beli.</p>
                        </div>
                    </form>
                @endif
            </div>

            <div class="rounded-2xl lg:rounded-3xl border border-gray-100 p-8 bg-primary-50">
                <h3 class="text-xl font-black text-gray-900 mb-4">Langkah Jadi Seller</h3>
                <ol class="space-y-4 text-gray-600">
                    <li class="font-bold">1. Masuk / daftar sebagai pembeli terlebih dahulu.</li>
                    <li class="font-bold">2. Isi data toko dan aktifkan mode seller.</li>
                    <li class="font-bold">3. Langsung akses dashboard seller dan tambah produk.</li>
                    <li class="font-bold">4. Tetap bisa belanja produk seller lain dari akun yang sama.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const detectButton = document.getElementById('detect-location-btn');
        const statusElement = document.getElementById('location-status');
        const addressInput = document.getElementById('store_address');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const mapWrapper = document.getElementById('location-map-wrapper');
        const mapCoordinates = document.getElementById('location-map-coordinates');

        let mapInstance = null;
        let mapMarker = null;

        if (!detectButton || !statusElement || !addressInput || !latInput || !lngInput || !mapWrapper || !mapCoordinates) {
            return;
        }

        const renderLocationPreview = (latitude, longitude) => {
            const lat = Number(latitude);
            const lng = Number(longitude);

            if (Number.isNaN(lat) || Number.isNaN(lng)) {
                return;
            }

            mapWrapper.classList.remove('hidden');
            mapCoordinates.textContent = `📍 ${lat.toFixed(7)}, ${lng.toFixed(7)}`;

            if (typeof window.L === 'undefined') {
                return;
            }

            if (!mapInstance) {
                mapInstance = window.L.map('seller-location-map', {
                    zoomControl: true,
                }).setView([lat, lng], 16);

                window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(mapInstance);
            }

            if (!mapMarker) {
                mapMarker = window.L.marker([lat, lng]).addTo(mapInstance);
            } else {
                mapMarker.setLatLng([lat, lng]);
            }

            mapMarker.bindPopup('Lokasi toko Anda').openPopup();
            mapInstance.setView([lat, lng], 16);
            setTimeout(() => mapInstance.invalidateSize(), 100);
        };

        const setStatus = (message, isError = false) => {
            statusElement.textContent = message;
            statusElement.classList.toggle('text-red-600', isError);
            statusElement.classList.toggle('text-gray-500', !isError);
        };

        if (latInput.value && lngInput.value) {
            renderLocationPreview(latInput.value, lngInput.value);
            setStatus('Lokasi sudah tersedia. Anda bisa ambil ulang jika titik berubah.');
        }

        detectButton.addEventListener('click', function () {
            if (!navigator.geolocation) {
                setStatus('Browser tidak mendukung Geolocation.', true);
                if (window.arradeaPopup) {
                    window.arradeaPopup.error('Browser tidak mendukung fitur lokasi.');
                }
                return;
            }

            detectButton.disabled = true;
            detectButton.classList.add('opacity-60', 'cursor-not-allowed');
            setStatus('Mengambil koordinat lokasi...');

            navigator.geolocation.getCurrentPosition(
                async function (position) {
                    const latitude = Number(position.coords.latitude).toFixed(7);
                    const longitude = Number(position.coords.longitude).toFixed(7);

                    latInput.value = latitude;
                    lngInput.value = longitude;
                    renderLocationPreview(latitude, longitude);

                    setStatus(`Koordinat didapatkan: ${latitude}, ${longitude}. Mencari alamat...`);

                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}&accept-language=id`,
                            {
                                headers: {
                                    'Accept': 'application/json',
                                },
                            }
                        );

                        if (!response.ok) {
                            throw new Error('Gagal mengambil alamat dari koordinat.');
                        }

                        const result = await response.json();
                        const resolvedAddress = result.display_name || '';

                        if (resolvedAddress) {
                            addressInput.value = resolvedAddress;
                            setStatus('Lokasi dan alamat toko berhasil diisi otomatis.');
                            if (window.arradeaPopup) {
                                window.arradeaPopup.success('Alamat toko berhasil diisi dari lokasi browser.');
                            }
                        } else {
                            setStatus('Koordinat berhasil didapatkan, tapi alamat tidak ditemukan. Isi alamat manual.', true);
                        }
                    } catch (error) {
                        setStatus('Koordinat berhasil didapatkan, tapi gagal mengisi alamat otomatis. Isi alamat manual.', true);
                    } finally {
                        detectButton.disabled = false;
                        detectButton.classList.remove('opacity-60', 'cursor-not-allowed');
                    }
                },
                function () {
                    setStatus('Izin lokasi ditolak atau lokasi tidak tersedia.', true);
                    detectButton.disabled = false;
                    detectButton.classList.remove('opacity-60', 'cursor-not-allowed');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 300000,
                }
            );
        });
    });
</script>
@endsection
