@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 lg:py-16 px-4">
    <div class="bg-white rounded-2xl lg:rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-5 lg:p-10 grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-10">
            <div class="lg:col-span-2 space-y-8">
                <div>
                    <h1 class="text-4xl font-black text-gray-900">Daftar Jadi Seller</h1>
                    <p class="mt-3 text-gray-500">Isi data diri toko Anda untuk mengajukan izin berjualan di Arradea. Admin akan meninjau dan memberikan persetujuan.</p>
                </div>

                @if(session('success'))
                    <div class="rounded-2xl lg:rounded-3xl border border-green-100 bg-green-50 p-6 text-green-700 font-bold">{{ session('success') }}</div>
                @endif

                @if($user->seller_status === 'pending')
                    <div class="rounded-2xl lg:rounded-3xl border border-amber-100 bg-amber-50 p-8">
                        <h2 class="text-2xl font-black text-gray-900">Aplikasi Seller Sedang Diproses</h2>
                        <p class="mt-4 text-gray-600">Permohonan Anda sedang menunggu persetujuan admin. Silakan cek kembali nanti.</p>
                    </div>
                @elseif($user->seller_status === 'rejected')
                    <div class="rounded-2xl lg:rounded-3xl border border-red-100 bg-red-50 p-8">
                        <h2 class="text-2xl font-black text-gray-900">Permohonan Seller Ditolak</h2>
                        <p class="mt-4 text-gray-600">Alasan: {{ $user->seller_rejection_reason ?? 'Tidak ada keterangan.' }}</p>
                        <p class="mt-4 text-gray-600">Anda dapat mengajukan kembali setelah memperbaiki data toko.</p>
                    </div>
                @elseif($user->role === 'seller')
                    <div class="rounded-2xl lg:rounded-3xl border border-green-100 bg-green-50 p-8">
                        <h2 class="text-2xl font-black text-gray-900">Akun Seller Aktif</h2>
                        <p class="mt-4 text-gray-600">Selamat, akun Anda sudah disetujui sebagai seller. Kelola toko dan produk Anda di dashboard seller.</p>
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
                                    <input type="text" name="store_address" value="{{ old('store_address', $user->store->address ?? '') }}" class="w-full rounded-2xl border border-gray-200 bg-white px-6 py-4 text-lg font-bold focus:border-primary-600 focus:outline-none" placeholder="Contoh: Jalan Merdeka No. 22, Jakarta">
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-4">
                            <button type="submit" class="w-full rounded-2xl lg:rounded-3xl bg-primary-700 text-white px-8 py-5 font-black text-lg hover:bg-primary-800 transition">Kirim Aplikasi Seller</button>
                            <p class="text-sm text-gray-500">Setelah dikirim, admin akan meninjau aplikasi seller Anda. Jika disetujui, akun Anda akan otomatis diubah menjadi seller.</p>
                        </div>
                    </form>
                @endif
            </div>

            <div class="rounded-2xl lg:rounded-3xl border border-gray-100 p-8 bg-primary-50">
                <h3 class="text-xl font-black text-gray-900 mb-4">Langkah Jadi Seller</h3>
                <ol class="space-y-4 text-gray-600">
                    <li class="font-bold">1. Masuk / daftar sebagai pembeli terlebih dahulu.</li>
                    <li class="font-bold">2. Isi data toko dan ajukan jadi seller.</li>
                    <li class="font-bold">3. Tunggu admin menyetujui aplikasi Anda.</li>
                    <li class="font-bold">4. Setelah disetujui, mulai tambahkan produk di dashboard seller.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
