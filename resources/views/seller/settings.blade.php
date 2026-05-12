@extends('layouts.dashboard')

@section('title', 'Settings - Arradea Seller')
@section('page_title', 'Pengaturan Toko')

@section('content')
<div class="space-y-6 lg:space-y-12">
    @php
        $seller = auth()->user();
        $paymentOptions = [
            'qris' => 'QRIS',
            'dana' => 'DANA',
            'gopay' => 'GoPay',
            'ovo' => 'OVO',
            'shopeepay' => 'ShopeePay',
            'bank' => 'Transfer Bank',
        ];
    @endphp

    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-600 to-slate-600 p-8 lg:p-10 lg:p-20 rounded-2xl lg:rounded-3xl lg:rounded-2xl lg:rounded-3xl lg:rounded-[4rem] text-white overflow-hidden relative shadow-2xl">
        <div class="absolute -top-32 -right-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-40"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-white/10 rounded-full blur-3xl opacity-20"></div>

        <div class="relative z-10">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-300">Konfigurasi</p>
            <h1 class="text-4xl lg:text-6xl font-black tracking-tighter leading-tight lg:leading-none mb-4">Pengaturan <span class="text-yellow-300 underline underline-offset-4 lg:underline-offset-8">Toko</span>.</h1>
            <p class="text-gray-200 font-medium text-lg">Kelola informasi dan preferensi toko Anda.</p>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="bg-white rounded-2xl lg:rounded-3xl lg:rounded-[3.5rem] shadow-sm border border-gray-100 p-5 lg:p-10">
        <h2 class="text-2xl font-black text-gray-900 mb-8">Informasi Toko</h2>

        @if(session('success'))
        <div class="mb-6 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-2xl">
            <span class="text-lg">✅</span>
            <p class="text-sm font-bold text-green-700">{{ session('success') }}</p>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl">
            <span class="text-lg">❌</span>
            <ul class="text-sm font-medium text-red-700 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('seller.settings.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-black text-gray-700 mb-2">Nama Toko <span class="text-red-400">*</span></label>
                    <input type="text"
                           name="store_name"
                           value="{{ old('store_name', auth()->user()->store->name ?? '') }}"
                           required
                           placeholder="Nama toko Anda"
                           class="w-full px-6 py-4 bg-gray-50 border {{ $errors->has('store_name') ? 'border-red-300' : 'border-gray-200' }} rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium">
                    @error('store_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-black text-gray-700 mb-2">Alamat Toko</label>
                    <input type="text"
                           name="store_address"
                           value="{{ old('store_address', auth()->user()->store->address ?? '') }}"
                           placeholder="Alamat lengkap toko"
                           class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium">
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-sm font-black text-gray-700 mb-2">Deskripsi Toko</label>
                    <textarea name="store_description"
                              rows="4"
                              placeholder="Ceritakan tentang toko Anda..."
                              class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium resize-none">{{ old('store_description', auth()->user()->store->description ?? '') }}</textarea>
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-sm font-black text-gray-700 mb-2">Logo Toko</label>
                    <input type="file"
                           name="store_image"
                           accept="image/*"
                           class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium file:mr-4 file:px-4 file:py-2 file:rounded-xl file:border-0 file:bg-primary-600 file:text-white file:font-black">
                    <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG, WebP. Maksimal 2MB.</p>
                    @if(auth()->user()->store && auth()->user()->store->image)
                        <div class="mt-4 rounded-2xl border border-gray-200 bg-gray-50 p-4 inline-block">
                            <img src="{{ asset('storage/'.auth()->user()->store->image) }}" alt="Logo toko" class="w-32 h-32 object-cover rounded-xl">
                            <p class="text-center text-xs text-gray-500 mt-2">Logo saat ini</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Pengaturan Pembayaran QRIS</h3>
                        <p class="text-sm text-gray-500 mt-1">Data ini akan ditampilkan ke buyer saat checkout QRIS manual.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[10px] lg:text-xs font-black uppercase tracking-wider {{ $seller->hasQrisPaymentSetup() ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $seller->hasQrisPaymentSetup() ? 'QRIS Aktif' : 'Belum Diset' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-black text-gray-700 mb-2">Nama Penerima <span class="text-red-400">*</span></label>
                            <input type="text"
                                   name="payment_name"
                                   value="{{ old('payment_name', $seller->payment_name) }}"
                                   placeholder="Contoh: Toko Arradea"
                                   required
                                   class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium">
                            <p class="text-xs text-gray-400 mt-2">Nama ini akan ditampilkan ke buyer saat checkout QRIS.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-black text-gray-700 mb-2">Jenis Pembayaran</label>
                            <select name="payment_type" class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium">
                                <option value="">Pilih jenis pembayaran</option>
                                @foreach($paymentOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('payment_type', $seller->payment_type) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-black text-gray-700 mb-2">Nomor / Akun Pembayaran</label>
                            <input type="text"
                                   name="payment_number"
                                   value="{{ old('payment_number', $seller->payment_number) }}"
                                   placeholder="Nomor HP / rekening / ID akun"
                                   class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium">
                            <p class="text-xs text-gray-400 mt-2">Kosongkan jika hanya memakai QRIS image.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-black text-gray-700 mb-2">Upload QRIS</label>
                            <input type="file"
                                   name="qris_image"
                                   accept="image/*"
                                   class="w-full px-4 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium file:mr-4 file:px-4 file:py-2 file:rounded-xl file:border-0 file:bg-primary-600 file:text-white file:font-black">
                        </div>

                        <div class="rounded-3xl border border-dashed border-gray-300 bg-gray-50 p-4">
                            @if($seller->qris_image)
                                <img src="{{ asset('storage/'.$seller->qris_image) }}" alt="QRIS seller" class="w-full max-w-sm mx-auto rounded-2xl border border-gray-200 shadow-sm object-contain bg-white">
                                <p class="text-center text-xs text-gray-500 mt-3">Gambar QRIS saat ini</p>
                            @else
                                <div class="text-center py-10 text-gray-400">
                                    <div class="text-4xl mb-3">🧾</div>
                                    <p class="text-sm font-bold">Belum ada gambar QRIS</p>
                                    <p class="text-xs mt-1">Upload gambar QR untuk ditampilkan ke buyer.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex justify-end">
                <button type="submit"
                        class="px-8 py-4 text-white font-black rounded-2xl transition hover:opacity-90 active:scale-95"
                        style="background:#72bf77">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection