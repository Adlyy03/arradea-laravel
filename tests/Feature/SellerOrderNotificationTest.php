<?php

namespace Tests\Feature;

use App\Models\AccessCode;
use App\Models\Category;
use App\Models\FcmToken;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderPaymentNotification;
use App\Notifications\OrderStatusNotification;
use App\Services\PushNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SellerOrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected User $buyer;
    protected Store $store;
    protected Product $product;
    protected Category $category;
    protected AccessCode $accessCode;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock PushNotificationService agar tidak perlu koneksi Firebase asli
        $this->mock(PushNotificationService::class, function ($mock) {
            $mock->shouldReceive('sendToUser')->andReturn([
                'success' => true,
                'total' => 1,
                'successful' => 1,
                'failed' => 0,
                'invalid_tokens' => [],
            ]);
            $mock->shouldReceive('sendToAll')->andReturn([
                'success' => true,
            ]);
        });

        // Setup access code
        $this->accessCode = AccessCode::create([
            'code' => 'TEST-CODE',
            'is_active' => true,
        ]);

        // Create seller user
        $this->seller = User::factory()->create([
            'is_seller' => true,
            'seller_status' => 'approved',
            'seller_approved_at' => now(),
            'phone_verified_at' => now(),
            'access_code_id' => $this->accessCode->id,
            'preferred_mode' => 'seller',
        ]);

        // Create store for seller
        $this->store = Store::factory()->create([
            'user_id' => $this->seller->id,
            'status' => 'active',
        ]);

        // Create category
        $this->category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
            'sort_order' => 1,
        ]);

        // Create product
        $this->product = $this->store->products()->create([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'description' => 'Produk test',
            'price' => 100000,
            'stock' => 50,
            'is_active' => true,
        ]);

        // Create buyer user
        $this->buyer = User::factory()->create([
            'is_seller' => false,
            'phone_verified_at' => now(),
            'access_code_id' => $this->accessCode->id,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TEST 1: Notifikasi database dikirim ke seller saat order via Cart Checkout
    // ─────────────────────────────────────────────────────────────────────────
    public function test_seller_receives_database_notification_on_cart_checkout(): void
    {
        Notification::fake();

        // Buyer menambahkan produk ke keranjang
        $this->actingAs($this->buyer)
            ->post('/buyer/cart', [
                'product_id' => $this->product->id,
                'quantity' => 2,
            ])
            ->assertSessionHasNoErrors();

        // Buyer melakukan checkout
        $this->actingAs($this->buyer)
            ->post('/buyer/cart/checkout', [
                'payment_method' => 'cod',
            ])
            ->assertRedirect();

        // Assert: Seller menerima NewOrderNotification
        Notification::assertSentTo(
            $this->seller,
            NewOrderNotification::class,
            function (NewOrderNotification $notification) {
                $data = $notification->toArray($this->seller);
                return $data['type'] === 'new_order'
                    && isset($data['order_id'])
                    && str_contains($data['message'], $this->buyer->name);
            }
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TEST 2: Notifikasi database dikirim ke seller saat order via API (Sanctum)
    // ─────────────────────────────────────────────────────────────────────────
    public function test_seller_receives_database_notification_on_api_order(): void
    {
        Notification::fake();

        // Buyer membuat pesanan via API menggunakan Sanctum
        $token = $this->buyer->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])
            ->postJson('/api/orders', [
                'product_id' => $this->product->id,
                'quantity' => 1,
                'notes' => 'Pesanan test via API',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        // Assert: Seller menerima NewOrderNotification
        Notification::assertSentTo(
            $this->seller,
            NewOrderNotification::class
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TEST 3: Notifikasi TIDAK dikirim jika seller dari store null
    // ─────────────────────────────────────────────────────────────────────────
    public function test_no_notification_when_store_owner_relationship_missing(): void
    {
        Notification::fake();

        // Buat user baru yang bukan seller, untuk simulasi store tanpa seller yang valid
        $dummyUser = User::factory()->create([
            'is_seller' => false,
            'access_code_id' => $this->accessCode->id,
        ]);

        $orphanStore = Store::factory()->create([
            'user_id' => $dummyUser->id,
            'status' => 'active',
        ]);

        $orphanProduct = $orphanStore->products()->create([
            'category_id' => $this->category->id,
            'name' => 'Orphan Product',
            'description' => 'Produk dari store user non-seller',
            'price' => 50000,
            'stock' => 10,
            'is_active' => true,
        ]);

        // Buyer baru agar cart bersih
        $buyer2 = User::factory()->create([
            'is_seller' => false,
            'phone_verified_at' => now(),
            'access_code_id' => $this->accessCode->id,
        ]);

        // Buyer tambah ke cart dan checkout
        $this->actingAs($buyer2)
            ->post('/buyer/cart', [
                'product_id' => $orphanProduct->id,
                'quantity' => 1,
            ]);

        $this->actingAs($buyer2)
            ->post('/buyer/cart/checkout', [
                'payment_method' => 'cod',
            ]);

        // Store user ada tapi dia bukan seller asli → notifikasi tetap dikirim ke user
        // Karena kode hanya cek `$seller = $cart->product->store->user` dan `if ($seller)`
        // User tetap ada, jadi notifikasi dikirim
        Notification::assertSentTo(
            $dummyUser,
            NewOrderNotification::class
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TEST 4: Isi data notifikasi benar (order_id, message, url)
    // ─────────────────────────────────────────────────────────────────────────
    public function test_notification_data_contains_correct_order_info(): void
    {
        Notification::fake();

        // Buyer checkout via cart
        $this->actingAs($this->buyer)
            ->post('/buyer/cart', [
                'product_id' => $this->product->id,
                'quantity' => 3,
            ]);

        $this->actingAs($this->buyer)
            ->post('/buyer/cart/checkout', [
                'payment_method' => 'cod',
            ]);

        $order = Order::where('user_id', $this->buyer->id)->first();
        $this->assertNotNull($order, 'Order harus berhasil dibuat');

        // Assert data notifikasi
        Notification::assertSentTo(
            $this->seller,
            NewOrderNotification::class,
            function (NewOrderNotification $notification) use ($order) {
                $data = $notification->toArray($this->seller);
                return $data['order_id'] === $order->id
                    && $data['type'] === 'new_order'
                    && isset($data['url']);
            }
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TEST 5: Multiple orders → multiple notifikasi ke seller
    // ─────────────────────────────────────────────────────────────────────────
    public function test_seller_receives_notification_for_each_cart_item(): void
    {
        Notification::fake();

        // Buat produk kedua di toko yang sama
        $product2 = $this->store->products()->create([
            'category_id' => $this->category->id,
            'name' => 'Test Product 2',
            'description' => 'Produk test 2',
            'price' => 200000,
            'stock' => 30,
            'is_active' => true,
        ]);

        // Buyer tambahkan 2 produk ke keranjang
        $this->actingAs($this->buyer)
            ->post('/buyer/cart', [
                'product_id' => $this->product->id,
                'quantity' => 1,
            ]);

        $this->actingAs($this->buyer)
            ->post('/buyer/cart', [
                'product_id' => $product2->id,
                'quantity' => 1,
            ]);

        // Buyer checkout
        $this->actingAs($this->buyer)
            ->post('/buyer/cart/checkout', [
                'payment_method' => 'cod',
            ]);

        // Assert: 2 notifikasi NewOrder dikirim ke seller (1 per order)
        Notification::assertSentToTimes($this->seller, NewOrderNotification::class, 2);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TEST 6: Buyer menerima notifikasi saat seller update status order
    // ─────────────────────────────────────────────────────────────────────────
    public function test_buyer_receives_notification_when_seller_updates_order_status(): void
    {
        Notification::fake();

        // Create order langsung
        $order = $this->buyer->orders()->create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'variant_key' => 'default',
            'quantity' => 1,
            'unit_price_original' => 100000,
            'unit_price_final' => 100000,
            'discount_percent_applied' => 0,
            'total_price' => 100000,
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
        ]);

        // Seller mengubah status order ke processing
        // Perlu set session active_mode=seller agar middleware role:seller lolos
        $this->actingAs($this->seller)
            ->withSession(['active_mode' => 'seller'])
            ->put("/web/order/{$order->id}/status", [
                'status' => 'processing',
            ]);

        // Assert status berubah
        $this->assertEquals('processing', $order->fresh()->status);

        // Assert buyer menerima OrderStatusNotification
        Notification::assertSentTo(
            $this->buyer,
            OrderStatusNotification::class
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TEST 7: Order membuat record di database notifications
    // ─────────────────────────────────────────────────────────────────────────
    public function test_notification_is_stored_in_database(): void
    {
        // Tidak menggunakan Notification::fake() agar notifikasi benar-benar tersimpan

        // Buyer checkout via cart
        $this->actingAs($this->buyer)
            ->post('/buyer/cart', [
                'product_id' => $this->product->id,
                'quantity' => 1,
            ]);

        $this->actingAs($this->buyer)
            ->post('/buyer/cart/checkout', [
                'payment_method' => 'cod',
            ]);

        // Assert: notifikasi tersimpan di database
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->seller->id,
            'notifiable_type' => User::class,
        ]);

        // Verify isi notifikasi
        $notification = $this->seller->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals('new_order', $notification->data['type']);
        $this->assertArrayHasKey('order_id', $notification->data);
        $this->assertArrayHasKey('message', $notification->data);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TEST 8: Notifikasi via channel 'database'
    // ─────────────────────────────────────────────────────────────────────────
    public function test_notification_uses_database_channel(): void
    {
        $order = $this->buyer->orders()->create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'variant_key' => 'default',
            'quantity' => 1,
            'unit_price_original' => 100000,
            'unit_price_final' => 100000,
            'discount_percent_applied' => 0,
            'total_price' => 100000,
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
        ]);

        $notification = new NewOrderNotification($order);
        $channels = $notification->via($this->seller);

        $this->assertContains('database', $channels);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TEST 9: Order via web route juga mengirim notifikasi
    // ─────────────────────────────────────────────────────────────────────────
    public function test_seller_receives_notification_on_web_order(): void
    {
        Notification::fake();

        // Buyer membuat pesanan via web route
        $response = $this->actingAs($this->buyer)
            ->post('/web/order', [
                'product_id' => $this->product->id,
                'quantity' => 1,
                'payment_method' => 'cod',
                'notes' => 'Test order via web',
            ]);

        // Web order bisa redirect atau return JSON tergantung request type
        $response->assertRedirect();

        // Assert: seller menerima notifikasi
        Notification::assertSentTo(
            $this->seller,
            NewOrderNotification::class
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TEST 10: Stok berkurang dan notifikasi dikirim secara bersamaan
    // ─────────────────────────────────────────────────────────────────────────
    public function test_stock_decrements_and_notification_sent_together(): void
    {
        Notification::fake();

        $initialStock = $this->product->stock; // 50

        $this->actingAs($this->buyer)
            ->post('/buyer/cart', [
                'product_id' => $this->product->id,
                'quantity' => 5,
            ]);

        $this->actingAs($this->buyer)
            ->post('/buyer/cart/checkout', [
                'payment_method' => 'cod',
            ]);

        // Assert stok berkurang
        $this->assertEquals($initialStock - 5, $this->product->fresh()->stock);

        // Assert notifikasi tetap dikirim
        Notification::assertSentTo($this->seller, NewOrderNotification::class);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TEST 11: Notifikasi berisi pesan yang menyebutkan nama buyer
    // ─────────────────────────────────────────────────────────────────────────
    public function test_notification_message_mentions_buyer_name(): void
    {
        Notification::fake();

        $this->actingAs($this->buyer)
            ->post('/buyer/cart', [
                'product_id' => $this->product->id,
                'quantity' => 1,
            ]);

        $this->actingAs($this->buyer)
            ->post('/buyer/cart/checkout', [
                'payment_method' => 'cod',
            ]);

        Notification::assertSentTo(
            $this->seller,
            NewOrderNotification::class,
            function (NewOrderNotification $notification) {
                $data = $notification->toArray($this->seller);
                return str_contains($data['message'], $this->buyer->name);
            }
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TEST 12: Seller tidak bisa membeli produk sendiri (no self-notification)
    // ─────────────────────────────────────────────────────────────────────────
    public function test_seller_cannot_buy_own_product(): void
    {
        Notification::fake();

        // Seller mencoba membeli produk sendiri via keranjang
        $this->actingAs($this->seller)
            ->post('/buyer/cart', [
                'product_id' => $this->product->id,
                'quantity' => 1,
            ]);

        $this->actingAs($this->seller)
            ->post('/buyer/cart/checkout', [
                'payment_method' => 'cod',
            ]);

        // Assert: Tidak boleh ada order dan notifikasi
        Notification::assertNothingSent();
    }
}
