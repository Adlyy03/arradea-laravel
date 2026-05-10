# Contoh Penggunaan Fitur Otomatis

## 1. Menampilkan Produk dengan Diskon di Blade

### Cara Sederhana (Menggunakan Accessor)
```blade
@foreach($products as $product)
    <div class="product-card">
        <h3>{{ $product->name }}</h3>
        
        {{-- Menggunakan accessor otomatis --}}
        @if($product->has_active_discount)
            <span class="badge-discount">{{ $product->active_discount_percent }}% OFF</span>
            <div class="price">
                <span class="original">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                <span class="final">Rp {{ number_format($product->final_price, 0, ',', '.') }}</span>
            </div>
        @else
            <div class="price">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </div>
        @endif
    </div>
@endforeach
```

### Cara Menggunakan Component
```blade
@foreach($products as $product)
    <div class="product-card">
        <h3>{{ $product->name }}</h3>
        
        {{-- Menggunakan component yang sudah dibuat --}}
        <x-product-price :product="$product" />
    </div>
@endforeach
```

---

## 2. Menampilkan Status Toko

### Di Halaman Produk
```blade
<div class="store-info">
    <h2>{{ $product->store->name }}</h2>
    
    {{-- Menggunakan component status toko --}}
    <x-store-status :store="$product->store" />
</div>
```

### Di Dashboard Seller
```blade
<div class="dashboard-header">
    <h1>Dashboard Seller</h1>
    
    {{-- Status toko seller yang sedang login --}}
    <x-store-status :seller="Auth::user()" />
</div>
```

---

## 3. API Response dengan Diskon Otomatis

### Controller
```php
public function index()
{
    $products = Product::with(['store', 'category'])
        ->where('stock', '>', 0)
        ->get();
    
    // Accessor otomatis akan ditambahkan ke response JSON
    return response()->json([
        'success' => true,
        'data' => $products
    ]);
}
```

### Response JSON
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Produk A",
            "price": 100000,
            "discount_percent": 20,
            "discount_start_at": "2026-05-01T00:00:00.000000Z",
            "discount_end_at": "2026-05-31T23:59:59.000000Z",
            "stock": 50,
            "final_price": 80000,
            "has_active_discount": true,
            "active_discount_percent": 20
        }
    ]
}
```

---

## 4. Filter Produk dengan Diskon Aktif

### Query Builder
```php
// Ambil produk yang sedang diskon
$discountedProducts = Product::where('discount_percent', '>', 0)
    ->where(function($query) {
        $query->whereNull('discount_start_at')
              ->orWhere('discount_start_at', '<=', now('Asia/Jakarta'));
    })
    ->where(function($query) {
        $query->whereNull('discount_end_at')
              ->orWhere('discount_end_at', '>=', now('Asia/Jakarta'));
    })
    ->get();

// Atau lebih sederhana, filter setelah load
$discountedProducts = Product::all()->filter(function($product) {
    return $product->has_active_discount;
});
```

---

## 5. Menghitung Total Keranjang dengan Diskon

### Controller
```php
public function calculateCart(Request $request)
{
    $cartItems = $request->input('items'); // [['product_id' => 1, 'quantity' => 2, 'variant' => null], ...]
    
    $total = 0;
    $details = [];
    
    foreach ($cartItems as $item) {
        $product = Product::find($item['product_id']);
        $pricing = $product->calculatePricing($item['variant'], $item['quantity']);
        
        $total += $pricing['total_final'];
        
        $details[] = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => $item['quantity'],
            'unit_price' => $pricing['unit_original'],
            'discount_percent' => $pricing['discount_percent'],
            'unit_final' => $pricing['unit_final'],
            'subtotal' => $pricing['total_final'],
        ];
    }
    
    return response()->json([
        'success' => true,
        'total' => $total,
        'details' => $details
    ]);
}
```

---

## 6. Setup Jadwal Toko di Controller

### Update Jadwal
```php
public function updateSchedule(Request $request)
{
    $validated = $request->validate([
        'open_time' => ['required', 'date_format:H:i'],
        'close_time' => ['required', 'date_format:H:i'],
        'auto_schedule' => ['boolean'],
    ]);
    
    $user = Auth::user();
    
    $user->update([
        'open_time' => $validated['open_time'],
        'close_time' => $validated['close_time'],
        'auto_schedule' => $request->boolean('auto_schedule', true),
    ]);
    
    return back()->with('success', 'Jadwal toko berhasil diperbarui!');
}
```

### Toggle Manual (Buka/Tutup)
```php
public function toggleStoreStatus()
{
    $user = Auth::user();
    
    $nextStatus = $user->store_status === 'open' ? 'closed' : 'open';
    
    $user->update([
        'store_status' => $nextStatus,
        'auto_schedule' => false, // Matikan auto schedule saat toggle manual
    ]);
    
    return back()->with('success', "Toko berhasil di{$nextStatus}!");
}
```

---

## 7. Setup Diskon Produk di Controller

### Create/Update Produk dengan Diskon
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'price' => ['required', 'numeric', 'min:0'],
        'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        'discount_start_at' => ['nullable', 'date'],
        'discount_end_at' => ['nullable', 'date', 'after:discount_start_at'],
        // ... field lainnya
    ]);
    
    $product = Product::create($validated);
    
    return redirect()->route('seller.products.index')
        ->with('success', 'Produk berhasil ditambahkan!');
}
```

---

## 8. Notifikasi Diskon akan Berakhir

### Command untuk Cek Diskon yang akan Berakhir
```php
// app/Console/Commands/NotifyExpiringDiscounts.php
public function handle()
{
    $tomorrow = now('Asia/Jakarta')->addDay();
    
    $expiringProducts = Product::where('discount_percent', '>', 0)
        ->whereDate('discount_end_at', $tomorrow->toDateString())
        ->with('store.user')
        ->get();
    
    foreach ($expiringProducts as $product) {
        // Kirim notifikasi ke seller
        $product->store->user->notify(
            new DiscountExpiringNotification($product)
        );
    }
    
    $this->info("Notified {$expiringProducts->count()} seller(s) about expiring discounts.");
}
```

### Tambahkan ke Schedule
```php
// routes/console.php
Schedule::command('discounts:notify-expiring')->dailyAt('09:00');
```

---

## 9. Widget Dashboard untuk Seller

### Statistik Diskon
```blade
<div class="stats-card">
    <h3>Produk dengan Diskon Aktif</h3>
    
    @php
        $activeDiscounts = Auth::user()->store->products
            ->filter(fn($p) => $p->has_active_discount)
            ->count();
    @endphp
    
    <p class="text-3xl font-bold">{{ $activeDiscounts }}</p>
    <p class="text-sm text-gray-500">dari {{ Auth::user()->store->products->count() }} produk</p>
</div>
```

---

## 10. Testing

### Test di Tinker
```bash
php artisan tinker
```

```php
// Test diskon produk
$product = Product::first();
$product->discount_percent = 25;
$product->discount_start_at = now();
$product->discount_end_at = now()->addDays(7);
$product->save();

// Cek hasil
$product->fresh();
$product->has_active_discount; // true
$product->active_discount_percent; // 25
$product->final_price; // harga setelah diskon 25%

// Test jadwal toko
$seller = User::where('role', 'seller')->first();
$seller->open_time = '08:00:00';
$seller->close_time = '22:00:00';
$seller->auto_schedule = true;
$seller->save();

// Jalankan sync
Artisan::call('stores:sync-schedules');
```

---

## Tips & Best Practices

1. **Validasi Input**: Selalu validasi tanggal diskon agar `discount_end_at` > `discount_start_at`
2. **Timezone Konsisten**: Gunakan `now('Asia/Jakarta')` untuk konsistensi
3. **Caching**: Pertimbangkan cache untuk produk populer dengan traffic tinggi
4. **Index Database**: Migration index sudah ditambahkan untuk performa optimal
5. **Scheduled Task**: Pastikan cron job berjalan di production
6. **Error Handling**: Tambahkan try-catch untuk operasi kritis
7. **Logging**: Log perubahan status toko dan diskon untuk audit trail
