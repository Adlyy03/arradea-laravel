@extends('layouts.dashboard')

@section('title', 'Live Map Semua User - Arradea Admin')
@section('page_title', 'Live Map User')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <div class="bg-white p-6 lg:p-12 rounded-2xl lg:rounded-[4rem] shadow-sm border border-gray-100">
        <div class="max-w-3xl">
            <h1 class="text-4xl lg:text-6xl font-black text-gray-900 tracking-tighter leading-tight mb-4">
                Live <span class="text-blue-600 underline underline-offset-8">Map Seller</span>.
            </h1>
            <p class="text-gray-500 text-lg font-medium">
                Peta lokasi toko seller yang memiliki koordinat.
            </p>
        </div>
    </div>

    @if(empty($mapUsers) || count($mapUsers) === 0)
            <div class="p-6 bg-amber-50 border border-amber-100 rounded-[2rem] text-amber-800 font-bold text-sm shadow-sm">
            Belum ada data lokasi toko
        </div>
    @else
        <div class="bg-white rounded-2xl lg:rounded-[4rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center gap-4 text-sm font-bold text-gray-700">
                <span class="inline-flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-600"></span> Toko Buka</span>
                <span class="inline-flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-gray-400"></span> Toko Tutup</span>
            </div>
            <div id="admin-live-map" class="w-full h-[75vh] min-h-[560px]"></div>
        </div>
    @endif
</div>

@if(!empty($mapUsers) && count($mapUsers) > 0)
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const users = @json($mapUsers);
            const mapElement = document.getElementById('admin-live-map');

            if (!mapElement || !window.L) {
                return;
            }

            const map = L.map('admin-live-map', {
                scrollWheelZoom: true,
            }).setView([-2.5489, 118.0149], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(map);

            const bounds = [];

            users.forEach(function (user) {
                const lat = parseFloat(user.latitude);
                const lng = parseFloat(user.longitude);

                if (Number.isNaN(lat) || Number.isNaN(lng)) {
                    return;
                }

                const isOpen = user.store_status === 'open';
                const color = isOpen ? '#16a34a' : '#9ca3af';

                const marker = L.circleMarker([lat, lng], {
                    radius: 9,
                    color,
                    weight: 3,
                    fillColor: color,
                    fillOpacity: 0.9,
                }).addTo(map);

                const statusLabel = isOpen ? 'Buka' : 'Tutup';
                const storeName = user.store_name || user.name || 'Toko';
                const scheduleText = (user.open_time && user.close_time)
                    ? `${String(user.open_time).slice(0, 5)} - ${String(user.close_time).slice(0, 5)}`
                    : 'Belum diatur';
                const autoScheduleLabel = user.auto_schedule ? 'Aktif' : 'Nonaktif';

                marker.bindPopup(`
                    <div style="min-width:220px;max-width:280px">
                        <div style="font-weight:800;font-size:14px;margin-bottom:6px;">${storeName}</div>
                        <div style="font-size:12px;line-height:1.6;color:#374151;">
                            <div><strong>Nama Toko:</strong> ${storeName}</div>
                            <div><strong>Status:</strong> ${statusLabel}</div>
                            <div><strong>Jadwal:</strong> ${scheduleText}</div>
                            <div><strong>Auto Schedule:</strong> ${autoScheduleLabel}</div>
                            <div><strong>Koordinat:</strong> ${lat.toFixed(7)}, ${lng.toFixed(7)}</div>
                        </div>
                    </div>
                `);

                bounds.push([lat, lng]);
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [40, 40] });
            }
        });
    </script>
@endif
@endsection
