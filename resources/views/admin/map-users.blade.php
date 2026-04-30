@extends('layouts.dashboard')
@section('title', 'Live Map Pengguna — Arradea Admin')
@section('page_title', 'Live Map')

@section('content')
<div class="space-y-5 fade-up">
    <div>
        <h1 class="text-2xl font-black text-gray-900">Live Map <span style="color:#72bf77">Pengguna</span></h1>
        <p class="text-sm text-gray-500 mt-0.5">Visualisasi lokasi seller berdasarkan koordinat GPS.</p>
    </div>

    @if(empty($mapUsers) || count($mapUsers) === 0)
        <div class="flex flex-col items-center justify-center py-16 bg-white rounded-2xl border border-gray-100 text-center">
            <span class="text-4xl mb-3">🗺️</span>
            <p class="font-bold text-gray-700">Belum ada data lokasi</p>
            <p class="text-sm text-gray-400 mt-1">Seller perlu memberikan izin lokasi saat registrasi.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-50 flex items-center gap-4 text-xs font-bold text-gray-600">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Toko Buka</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span> Toko Tutup</span>
                <span class="ml-auto text-gray-400">{{ count($mapUsers) }} lokasi tersedia</span>
            </div>
            <div id="admin-live-map" class="w-full h-[70vh] min-h-[480px]"></div>
        </div>
    @endif
</div>

@if(!empty($mapUsers) && count($mapUsers) > 0)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const users = @json($mapUsers);
    const mapEl = document.getElementById('admin-live-map');
    if(!mapEl || !window.L) return;

    const map = L.map('admin-live-map',{scrollWheelZoom:true}).setView([-2.5489,118.0149],5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'© OpenStreetMap'}).addTo(map);

    const bounds = [];
    users.forEach(u => {
        const lat = parseFloat(u.latitude), lng = parseFloat(u.longitude);
        if(isNaN(lat)||isNaN(lng)) return;
        const isOpen = u.store_status === 'open';
        const color = isOpen ? '#72bf77' : '#9ca3af';
        const marker = L.circleMarker([lat,lng],{radius:9,color,weight:3,fillColor:color,fillOpacity:0.9}).addTo(map);
        const schedule = (u.open_time && u.close_time) ? `${String(u.open_time).slice(0,5)}–${String(u.close_time).slice(0,5)}` : '—';
        marker.bindPopup(`
            <div style="min-width:200px;font-family:sans-serif">
                <p style="font-weight:900;font-size:13px;margin:0 0 6px">${u.store_name||u.name}</p>
                <div style="font-size:12px;color:#374151;line-height:1.7">
                    <div><strong>Status:</strong> ${isOpen?'🟢 Buka':'⚫ Tutup'}</div>
                    <div><strong>Jadwal:</strong> ${schedule}</div>
                    <div><strong>Koordinat:</strong> ${lat.toFixed(5)}, ${lng.toFixed(5)}</div>
                </div>
            </div>
        `);
        bounds.push([lat,lng]);
    });
    if(bounds.length) map.fitBounds(bounds,{padding:[40,40]});
});
</script>
@endif
@endsection
