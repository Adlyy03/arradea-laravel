# 📦 Abiyu Food Product Seeder - Dokumentasi

## 📋 Ringkasan

Saya telah membuat seeder untuk memasukkan data **5 produk makanan dengan varian** untuk seller **Abiyu**.

## ✅ File yang Telah Dibuat

### 1. **Seeder File**
- **Path**: `database/seeders/AbiuFoodProductSeeder.php`
- **Fungsi**: Membuat seller Abiyu dan 5 produk makanan dengan varian

### 2. **Testing Endpoint** 
- **Path**: `app/Http/Controllers/TestController.php`
- **URL**: `GET /api/test/abiyu-seeder`
- **Fungsi**: API endpoint untuk menjalankan seeder dan menampilkan hasil testing

### 3. **Route Testing**
- **Path**: `routes/api.php`
- **Route Added**: `GET /api/test/abiyu-seeder`

### 4. **Batch Script (Windows)**
- **Path**: `run_seeder.bat`
- **Fungsi**: Script untuk menjalankan seeder dari command line

---

## 🚀 Cara Menjalankan

### **Opsi 1: Menggunakan Artisan CLI (Recommended)**

1. Buka Command Prompt atau Windows PowerShell
2. Navigate ke folder project:
   ```bash
   cd C:\laragon\www\arradeaaaa
   ```

3. Jalankan seeder:
   ```bash
   php artisan db:seed --class=AbiuFoodProductSeeder
   ```

### **Opsi 2: Menggunakan Batch Script (Windows)**

1. Double-click file `run_seeder.bat` dari folder project
2. Script akan otomatis menjalankan seeder dan menampilkan hasil testing

### **Opsi 3: Menggunakan API Endpoint**

1. Pastikan Laravel development server berjalan:
   ```bash
   php artisan serve
   ```

2. Akses URL di browser:
   ```
   http://localhost:8000/api/test/abiyu-seeder
   ```

3. API akan menjalankan seeder dan menampilkan hasil dalam format JSON

---

## 📊 Data yang Akan Dibuat

### **Seller Information**
- **Nama**: Abiyu
- **Email**: abiyu@arradea.com
- **Password**: password
- **Role**: seller

### **Store Information**
- **Nama Store**: Abiyu Food Store
- **Deskripsi**: Toko makanan berkualitas dengan produk pilihan terbaik dan varian rasa yang lezat.
- **Alamat**: Jl. Ahmad Yani No. 100, Jakarta

### **5 Produk Makanan dengan Varian**

#### 1️⃣ **Kopi Premium Arabika** (Rp 89.000)
- **Medium Roast**: Rp 89.000 (Stock: 25)
- **Dark Roast**: Rp 95.000 (Stock: 20)
- **Light Roast**: Rp 85.000 (Stock: 5)
- **Total Varian**: 3

#### 2️⃣ **Teh Hijau Organik** (Rp 55.000)
- **Loose Leaf**: Rp 55.000 (Stock: 20)
- **Tea Bag**: Rp 65.000 (Stock: 15)
- **Powder Mix**: Rp 75.000 (Stock: 5)
- **Total Varian**: 3

#### 3️⃣ **Coklat Premium Homemade** (Rp 125.000)
- **Dark Chocolate 70%**: Rp 125.000 (Stock: 12)
- **Milk Chocolate**: Rp 115.000 (Stock: 10)
- **White Chocolate**: Rp 110.000 (Stock: 8)
- **Total Varian**: 3

#### 4️⃣ **Jamu Tradisional Asli** (Rp 35.000)
- **Kunyit Asam**: Rp 35.000 (Stock: 20)
- **Beras Kencur**: Rp 35.000 (Stock: 20)
- **Temulawak**: Rp 40.000 (Stock: 20)
- **Total Varian**: 3

#### 5️⃣ **Kacang Panggang Premium** (Rp 45.000)
- **Cashew Manis**: Rp 55.000 (Stock: 15)
- **Almond Pedas**: Rp 50.000 (Stock: 18)
- **Mixed Nuts**: Rp 45.000 (Stock: 17)
- **Total Varian**: 3

---

## 📈 Summary
- **Total Products**: 5 produk
- **Total Variants**: 15 varian
- **Total Stock**: 210+ items
- **Kategori**: Makanan & Minuman

---

## 🧪 Testing

Setelah menjalankan seeder, Anda bisa memverifikasi dengan:

### Via Artisan Tinker:
```bash
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'abiyu@arradea.com')->with('store.products.category')->first();
echo "Seller: " . $user->name;
echo "Store: " . $user->store->name;
echo "Products: " . $user->store->products->count();
```

### Via API JSON Response:
```bash
curl http://localhost:8000/api/test/abiyu-seeder
```

Akan menampilkan response JSON berisi:
```json
{
  "success": true,
  "message": "Seeder executed successfully and data verified",
  "seller": {...},
  "store": {...},
  "products": [...],
  "summary": {...}
}
```

---

## 🔧 Troubleshooting

### Jika seeder gagal karena kategori tidak ada:
- Seeder akan otomatis membuat kategori "Makanan & Minuman" jika belum ada

### Jika seeder gagal karena user sudah ada:
- Seeder menggunakan `firstOrCreate()` untuk menghindari duplikasi
- User akan diupdate dengan data baru jika sudah ada

### Jika ingin reset/menjalankan ulang:
```bash
php artisan migrate:fresh --seed
```

---

## 📝 Catatan

- File seeder ini dapat dijalankan berkali-kali tanpa membuat duplikasi
- Untuk production, pastikan TestController endpoint dihapus untuk keamanan
- Data produk dapat disesuaikan dengan mengubah seeder file

---

Silakan jalankan seeder dengan salah satu opsi di atas! 🎉
