@extends('layouts.dashboard')

@section('title', 'Verifikasi Seller - Arradea Admin')
@section('page_title', 'Verifikasi Seller')

@section('content')
<div class="space-y-6 lg:space-y-12">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-end gap-6 lg:gap-12 bg-white p-6 lg:p-12 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-[4rem] shadow-sm border border-gray-100">
        <div class="max-w-2xl text-center md:text-left">
            <h1 class="text-4xl lg:text-6xl font-black text-gray-900 tracking-tighter leading-tight mb-4">Verifikasi <span class="text-blue-600 underline underline-offset-8">Seller</span> Baru.</h1>
            <p class="text-gray-500 text-lg font-medium">Review dan setujui atau tolak permohonan pendaftaran seller yang masuk.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-6 bg-green-50 border border-green-100 rounded-[2rem] text-green-600 font-bold text-sm shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-6 bg-red-50 border border-red-100 rounded-[2rem] text-red-600 font-bold text-sm shadow-sm">
            ❌ {{ $errors->first() }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl lg:rounded-[4rem] p-6 lg:p-12 shadow-sm border border-gray-100 space-y-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" placeholder="Cari nama atau nomor HP..." value="{{ request('search') }}" 
                           class="flex-1 h-12 px-4 rounded-xl bg-gray-50 border-none focus:ring-4 focus:ring-primary-100 font-bold">
                    <button type="submit" class="px-6 h-12 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 transition-all">
                        Cari
                    </button>
                </form>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.users.verification') }}" 
                   class="px-5 py-3 rounded-xl font-bold text-xs lg:text-sm transition-all {{ !request('status') ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                    Semua
                </a>
                <a href="{{ route('admin.users.verification', ['status' => 'pending']) }}" 
                   class="px-5 py-3 rounded-xl font-bold text-xs lg:text-sm transition-all {{ request('status') == 'pending' ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                    Pending
                </a>
                <a href="{{ route('admin.users.verification', ['status' => 'approved']) }}" 
                   class="px-5 py-3 rounded-xl font-bold text-xs lg:text-sm transition-all {{ request('status') == 'approved' ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                    Approved
                </a>
                <a href="{{ route('admin.users.verification', ['status' => 'rejected']) }}" 
                   class="px-5 py-3 rounded-xl font-bold text-xs lg:text-sm transition-all {{ request('status') == 'rejected' ? 'bg-primary-600 text-white shadow-xl shadow-primary-200' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">
                    Rejected
                </a>
            </div>
        </div>
    </div>

    <!-- User Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($users as $user)
            <div class="bg-white rounded-2xl lg:rounded-3xl p-6 lg:p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-all" 
                 @click="openDetailModal = true; selectedUser = {{ json_encode(['id' => $user->id, 'name' => $user->name, 'phone' => $user->phone, 'wilayah' => $user->wilayah, 'seller_status' => $user->seller_status, 'seller_applied_at' => $user->seller_applied_at?->format('d M Y H:i'), 'seller_rejected_at' => $user->seller_rejected_at?->format('d M Y H:i'), 'seller_rejection_reason' => $user->seller_rejection_reason, 'latitude' => $user->latitude ?? -6.1753, 'longitude' => $user->longitude ?? 106.8249]) }}" 
                 class="cursor-pointer" x-data>

                <!-- Avatar & Status -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-400 flex items-center justify-center text-white font-black text-lg">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-black text-lg text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-400">{{ $user->phone }}</p>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest 
                        @if($user->seller_status === 'pending')
                            bg-yellow-100 text-yellow-700
                        @elseif($user->seller_status === 'approved')
                            bg-green-100 text-green-700
                        @else
                            bg-red-100 text-red-700
                        @endif">
                        {{ $user->seller_status }}
                    </span>
                </div>

                <!-- Info -->
                <div class="space-y-2 text-sm mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Wilayah:</span>
                        <span class="font-bold text-gray-900">{{ $user->wilayah }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Daftar:</span>
                        <span class="font-bold text-gray-900">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    @if($user->seller_applied_at)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Ajukan Seller:</span>
                            <span class="font-bold text-gray-900">{{ $user->seller_applied_at->format('d M Y') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                @if($user->seller_status === 'pending')
                    <div class="flex gap-2 pt-4 border-t border-gray-100">
                        <button type="button" 
                                @click="approveUser = {{ $user->id }}; openApproveModal = true; $event.stopPropagation()"
                                class="flex-1 px-4 py-2 bg-green-50 text-green-700 rounded-xl font-bold text-xs hover:bg-green-100 transition-all">
                            ✅ Approve
                        </button>
                        <button type="button" 
                                @click="rejectUser = {{ $user->id }}; openRejectModal = true; $event.stopPropagation()"
                                class="flex-1 px-4 py-2 bg-red-50 text-red-700 rounded-xl font-bold text-xs hover:bg-red-100 transition-all">
                            ❌ Reject
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full text-center py-16">
                <p class="text-gray-400 font-bold text-lg">Tidak ada user yang perlu diverifikasi</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($users->count())
        <div class="mt-8">
            {{ $users->links() }}
        </div>
    @endif
</div>

<!-- DETAIL MODAL -->
<div x-data="{ selectedUser: null, openDetailModal: false }" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
     x-show="openDetailModal" @click.away="openDetailModal = false" x-cloak>
    <div class="bg-white rounded-[3rem] w-full max-w-4xl max-h-[90vh] overflow-y-auto p-8 lg:p-12 relative shadow-2xl"
         x-transition:enter="transition ease-out duration-300"
         x-transition:leave="transition ease-in duration-200">

        <button @click="openDetailModal = false" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 text-2xl">×</button>

        <template x-if="selectedUser">
            <div class="space-y-8">
                <!-- Header -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-3xl font-black text-gray-900 mb-2" x-text="selectedUser.name"></h2>
                    <p class="text-gray-500 font-bold" x-text="`📞 ${selectedUser.phone}`"></p>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Wilayah</p>
                            <p class="text-lg font-black text-gray-900" x-text="selectedUser.wilayah"></p>
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Status</p>
                            <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest"
                                :class="selectedUser.seller_status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                        selectedUser.seller_status === 'approved' ? 'bg-green-100 text-green-700' : 
                                        'bg-red-100 text-red-700'"
                                x-text="selectedUser.seller_status">
                            </span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div x-show="selectedUser.seller_applied_at">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Ajukan Seller</p>
                            <p class="font-bold text-gray-900" x-text="selectedUser.seller_applied_at"></p>
                        </div>
                        <div x-show="selectedUser.seller_rejected_at">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Ditolak Pada</p>
                            <p class="font-bold text-gray-900" x-text="selectedUser.seller_rejected_at"></p>
                        </div>
                    </div>
                </div>

                <!-- Rejection Reason -->
                <div x-show="selectedUser.seller_rejection_reason" class="p-4 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-xs font-black uppercase tracking-widest text-red-600 mb-2">Alasan Penolakan</p>
                    <p class="text-red-900 font-bold" x-text="selectedUser.seller_rejection_reason"></p>
                </div>

                <!-- Map -->
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4">Lokasi</p>
                    <div id="map" class="w-full h-64 rounded-2xl border border-gray-200" style="background: #f3f4f6;"></div>
                    <p class="text-xs text-gray-400 mt-2" x-text="`📍 ${selectedUser.latitude.toFixed(4)}, ${selectedUser.longitude.toFixed(4)}`"></p>
                </div>

                <!-- Action Buttons -->
                <div x-show="selectedUser.seller_status === 'pending'" class="flex gap-4 pt-4 border-t border-gray-200">
                    <button @click="approveUser = selectedUser.id; openApproveModal = true"
                            class="flex-1 px-6 py-3 bg-green-600 text-white rounded-2xl font-black hover:bg-green-700 transition-all">
                        ✅ Setujui Seller
                    </button>
                    <button @click="rejectUser = selectedUser.id; openRejectModal = true"
                            class="flex-1 px-6 py-3 bg-red-600 text-white rounded-2xl font-black hover:bg-red-700 transition-all">
                        ❌ Tolak Seller
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- APPROVE MODAL -->
<div x-data="{ approveUser: null, openApproveModal: false, loading: false }" 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
     x-show="openApproveModal" @click.away="openApproveModal = false" x-cloak>
    <div class="bg-white rounded-[3rem] w-full max-w-md p-8 lg:p-10 relative shadow-2xl"
         x-transition:enter="transition ease-out duration-300"
         x-transition:leave="transition ease-in duration-200">

        <button @click="openApproveModal = false" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 text-2xl">×</button>

        <div class="text-center space-y-6">
            <div class="text-5xl">✅</div>
            <div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">Setujui Seller?</h3>
                <p class="text-gray-500 font-bold">User ini akan menjadi seller dan dapat langsung mulai berjualan.</p>
            </div>

            <form @submit.prevent="async () => {
                loading = true;
                try {
                    const res = await fetch(`/admin/users/${approveUser}/approve`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content },
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } finally {
                    loading = false;
                }
            }" class="space-y-4">
                <button type="submit" :disabled="loading" 
                        class="w-full px-6 py-3 bg-green-600 text-white rounded-2xl font-black hover:bg-green-700 disabled:opacity-50 transition-all">
                    <span x-show="!loading">Setujui & Aktifkan</span>
                    <span x-show="loading">Sedang memproses...</span>
                </button>
                <button type="button" @click="openApproveModal = false" 
                        class="w-full px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold hover:bg-gray-200 transition-all">
                    Batal
                </button>
            </form>
        </div>
    </div>
</div>

<!-- REJECT MODAL -->
<div x-data="{ rejectUser: null, openRejectModal: false, loading: false, reason: '' }" 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
     x-show="openRejectModal" @click.away="openRejectModal = false" x-cloak>
    <div class="bg-white rounded-[3rem] w-full max-w-md p-8 lg:p-10 relative shadow-2xl"
         x-transition:enter="transition ease-out duration-300"
         x-transition:leave="transition ease-in duration-200">

        <button @click="openRejectModal = false" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 text-2xl">×</button>

        <div class="space-y-6">
            <div class="text-center">
                <div class="text-4xl mb-2">❌</div>
                <h3 class="text-2xl font-black text-gray-900">Tolak Seller?</h3>
            </div>

            <form @submit.prevent="async () => {
                if (!reason.trim()) {
                    alert('Silakan masukkan alasan penolakan');
                    return;
                }
                loading = true;
                try {
                    const res = await fetch(`/admin/users/${rejectUser}/reject`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ reason }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } finally {
                    loading = false;
                }
            }" class="space-y-4">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Alasan Penolakan</label>
                    <textarea x-model="reason" rows="4" required
                              placeholder="Jelaskan mengapa Anda menolak permohonan seller ini..."
                              class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-4 focus:ring-red-100 font-bold resize-none"></textarea>
                </div>

                <button type="submit" :disabled="loading" 
                        class="w-full px-6 py-3 bg-red-600 text-white rounded-2xl font-black hover:bg-red-700 disabled:opacity-50 transition-all">
                    <span x-show="!loading">Tolak Permohonan</span>
                    <span x-show="loading">Sedang memproses...</span>
                </button>
                <button type="button" @click="openRejectModal = false; reason = ''" 
                        class="w-full px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold hover:bg-gray-200 transition-all">
                    Batal
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Leaflet Map Script -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
    let map;
    document.addEventListener('alpine:initialized', function() {
        // Initialize map when detail modal opens
        setInterval(function() {
            if (document.getElementById('map') && !map) {
                initMap();
            }
        }, 100);
    });

    function initMap() {
        if (map) return;
        
        map = L.map('map').setView([-6.1753, 106.8249], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
    }

    function updateMapMarker(lat, lng) {
        if (!map) return;
        map.setView([lat, lng], 15);
        L.marker([lat, lng]).addTo(map).bindPopup('Lokasi Seller');
    }
</script>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
