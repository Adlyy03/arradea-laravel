<?php

namespace Tests\Feature;

use App\Models\AccessCode;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MarketplaceEndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected AccessCode $activeCode;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'location.center_lat' => -6.200000,
            'location.center_lng' => 106.816666,
            'location.max_radius' => 5,
        ]);

        $this->activeCode = AccessCode::create([
            'code' => 'ARRADEA-E2E',
            'is_active' => true,
        ]);
    }

    public function test_register_and_login_flows_with_access_code_validation(): void
    {
        $okRegister = $this->postJson('/api/register', [
            'name' => 'Buyer One',
            'phone' => '+628333330001',
            'password' => 'password',
            'password_confirmation' => 'password',
            'access_code' => $this->activeCode->code,
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ]);

        $okRegister->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', [
            'phone' => '+628333330001',
            'access_code_id' => $this->activeCode->id,
        ]);

        $badRegister = $this->postJson('/api/register', [
            'name' => 'Buyer Two',
            'phone' => '+628333330002',
            'password' => 'password',
            'password_confirmation' => 'password',
            'access_code' => 'KODE-SALAH',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ]);

        $badRegister->assertStatus(422)
            ->assertJsonPath('success', false);

        $withoutCode = $this->postJson('/api/register', [
            'name' => 'Buyer Three',
            'phone' => '+628333330003',
            'password' => 'password',
            'password_confirmation' => 'password',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ]);

        $withoutCode->assertStatus(422)
            ->assertJsonValidationErrors('access_code');

        $okLogin = $this->postJson('/api/login', [
            'phone' => '+628333330001',
            'password' => 'password',
        ]);

        $okLogin->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token']]);

        $badLogin = $this->postJson('/api/login', [
            'phone' => '+628333330001',
            'password' => 'wrong-password',
        ]);

        $badLogin->assertStatus(422)
            ->assertJsonValidationErrors('phone');
    }

    public function test_role_switch_seller_product_creation_and_seller_can_buy_other_store(): void
    {
        $buyer = User::factory()->create(['is_seller' => false, 'role' => 'buyer']);
        $otherSeller = User::factory()->create(['is_seller' => true, 'role' => 'buyer']);

        $otherStore = Store::factory()->create(['user_id' => $otherSeller->id, 'status' => 'active']);
        $category = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik']);
        $otherProduct = Product::factory()->create([
            'store_id' => $otherStore->id,
            'category_id' => $category->id,
            'price' => 120000,
            'stock' => 20,
            'discount_percent' => 0,
        ]);

        Sanctum::actingAs($buyer);
        $toggle = $this->patchJson('/api/profile/seller-mode', [
            'enable' => true,
            'store_name' => 'Toko Buyer',
        ]);

        $toggle->assertOk()->assertJsonPath('success', true);
        $this->assertTrue((bool) $buyer->fresh()->is_seller);
        $this->assertNotNull($buyer->fresh()->store);

        // Refresh authenticated model after toggle so role/store state is up-to-date.
        Sanctum::actingAs($buyer->fresh());

        $createProduct = $this->postJson('/api/products', [
            'name' => 'Produk Baru',
            'description' => 'Produk seller baru',
            'price' => 75000,
            'stock' => 5,
        ]);

        $createProduct->assertCreated()->assertJsonPath('success', true);

        $buyOtherStoreProduct = $this->postJson('/api/orders', [
            'product_id' => $otherProduct->id,
            'quantity' => 2,
            'notes' => 'Beli sebagai seller',
        ]);

        $buyOtherStoreProduct->assertCreated()->assertJsonPath('success', true);

        $order = Order::query()->where('user_id', $buyer->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
    }

    public function test_product_crud_stock_price_and_updates_endpoint(): void
    {
        $seller = User::factory()->create(['is_seller' => true, 'role' => 'buyer']);
        $store = Store::factory()->create(['user_id' => $seller->id, 'status' => 'active']);
        $category = Category::create(['name' => 'Fashion', 'slug' => 'fashion']);

        Sanctum::actingAs($seller);

        $create = $this->postJson('/api/products', [
            'name' => 'Jaket Denim',
            'description' => 'Jaket keren',
            'price' => 200000,
            'stock' => 10,
            'category_id' => $category->id,
        ]);

        $create->assertCreated()->assertJsonPath('success', true);
        $productId = (int) $create->json('data.id');

        $update = $this->putJson('/api/products/' . $productId, [
            'name' => 'Jaket Denim Premium',
            'description' => 'Sudah diupdate',
            'price' => 250000,
            'stock' => 7,
            'category_id' => $category->id,
        ]);

        $update->assertOk()->assertJsonPath('success', true);

        $updates = $this->getJson('/api/products/updates?ids[]=' . $productId);
        $updates->assertOk()->assertJsonPath('success', true);
        $this->assertEquals(250000.0, (float) $updates->json('data.0.price'));
        $this->assertEquals(7, (int) $updates->json('data.0.stock'));

        $delete = $this->deleteJson('/api/products/' . $productId);
        $delete->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseMissing('products', ['id' => $productId]);
        $this->assertEquals($store->id, (int) $store->id);
    }

    public function test_chat_message_flow_has_no_double_message(): void
    {
        $seller = User::factory()->create(['is_seller' => true]);
        $buyer = User::factory()->create(['is_seller' => false]);
        $store = Store::factory()->create(['user_id' => $seller->id, 'status' => 'active']);
        $product = Product::factory()->create(['store_id' => $store->id, 'stock' => 10, 'price' => 50000]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'store_id' => $store->id,
            'product_id' => $product->id,
            'variant_key' => 'default',
            'quantity' => 1,
            'unit_price_original' => 50000,
            'unit_price_final' => 50000,
            'discount_percent_applied' => 0,
            'total_price' => 50000,
            'status' => 'pending',
        ]);

        $this->actingAs($buyer)->get('/chat/' . $order->id)->assertOk();

        $chat = Chat::query()->where('order_id', $order->id)->first();
        $this->assertNotNull($chat);

        $send = $this->actingAs($seller)->postJson('/chat/' . $chat->id, [
            'message' => 'Pesanan diproses sekarang',
        ]);

        $send->assertCreated()->assertJsonPath('success', true);

        $this->assertDatabaseHas('messages', [
            'chat_id' => $chat->id,
            'sender_id' => $seller->id,
            'message' => 'Pesanan diproses sekarang',
        ]);

        $count = Message::query()
            ->where('chat_id', $chat->id)
            ->where('sender_id', $seller->id)
            ->where('message', 'Pesanan diproses sekarang')
            ->count();

        $this->assertSame(1, $count);
    }

    public function test_cart_update_quantity_checkout_and_discount_total(): void
    {
        $seller = User::factory()->create(['is_seller' => true]);
        $buyer = User::factory()->create(['is_seller' => false]);
        $store = Store::factory()->create(['user_id' => $seller->id, 'status' => 'active']);
        $category = Category::create(['name' => 'Rumah Tangga', 'slug' => 'rumah-tangga']);

        $product = Product::factory()->create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Blender',
            'price' => 100000,
            'stock' => 10,
            'discount_percent' => 10,
            'discount_start_at' => now()->subDay(),
            'discount_end_at' => now()->addDay(),
        ]);

        $this->actingAs($buyer)->post('/buyer/cart', [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect();

        $cart = Cart::query()->where('user_id', $buyer->id)->firstOrFail();

        $this->actingAs($buyer)->put('/buyer/cart/' . $cart->id, [
            'quantity' => 3,
        ])->assertRedirect();

        $this->actingAs($buyer)->post('/buyer/cart/checkout', [
            'notes' => 'Checkout diskon',
        ])->assertRedirect(route('buyer.orders'));

        $order = Order::query()->where('user_id', $buyer->id)->latest()->first();
        $this->assertNotNull($order);

        // 100000 - 10% = 90000; 90000 * 3 = 270000
        $this->assertEquals(270000.0, (float) $order->total_price);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(7, (int) $product->fresh()->stock);
    }

    public function test_order_status_and_cancel_logic(): void
    {
        $seller = User::factory()->create(['is_seller' => true]);
        $buyer = User::factory()->create(['is_seller' => false]);
        $store = Store::factory()->create(['user_id' => $seller->id, 'status' => 'active']);
        $product = Product::factory()->create(['store_id' => $store->id, 'stock' => 8, 'price' => 30000]);

        $pendingOrder = Order::create([
            'user_id' => $buyer->id,
            'store_id' => $store->id,
            'product_id' => $product->id,
            'variant_key' => 'default',
            'quantity' => 1,
            'unit_price_original' => 30000,
            'unit_price_final' => 30000,
            'discount_percent_applied' => 0,
            'total_price' => 30000,
            'status' => 'pending',
        ]);

        $this->actingAs($buyer)
            ->put('/web/order/' . $pendingOrder->id . '/cancel')
            ->assertSessionHas('success');

        $this->assertEquals('dibatalkan', $pendingOrder->fresh()->status);

        $processedOrder = Order::create([
            'user_id' => $buyer->id,
            'store_id' => $store->id,
            'product_id' => $product->id,
            'variant_key' => 'default',
            'quantity' => 1,
            'unit_price_original' => 30000,
            'unit_price_final' => 30000,
            'discount_percent_applied' => 0,
            'total_price' => 30000,
            'status' => 'accepted',
        ]);

        $this->actingAs($buyer)
            ->put('/web/order/' . $processedOrder->id . '/cancel')
            ->assertSessionHasErrors();

        $this->assertEquals('accepted', $processedOrder->fresh()->status);
    }

    public function test_search_and_filter_products(): void
    {
        $seller = User::factory()->create([
            'is_seller' => true,
            'role' => 'seller',
            'store_status' => 'open',
        ]);
        $store = Store::factory()->create(['user_id' => $seller->id, 'status' => 'active']);

        $food = Category::create(['name' => 'Makanan', 'slug' => 'makanan']);
        $drink = Category::create(['name' => 'Minuman', 'slug' => 'minuman']);

        Product::factory()->create([
            'store_id' => $store->id,
            'category_id' => $food->id,
            'name' => 'Keripik Kentang',
            'price' => 15000,
            'stock' => 20,
        ]);

        Product::factory()->create([
            'store_id' => $store->id,
            'category_id' => $drink->id,
            'name' => 'Jus Mangga',
            'price' => 18000,
            'stock' => 20,
        ]);

        $search = $this->getJson('/api/products/search?q=keripik');
        $search->assertOk()->assertJsonPath('success', true);
        $this->assertStringContainsStringIgnoringCase('keripik', (string) $search->json('data.data.0.name'));

        $buyer = User::factory()->create(['is_seller' => false]);
        $filter = $this->actingAs($buyer)->get('/products?category=makanan');
        $filter->assertOk()->assertSee('Keripik Kentang')->assertDontSee('Jus Mangga');
    }

    public function test_error_handling_for_empty_invalid_and_not_found_api(): void
    {
        $emptyLogin = $this->postJson('/api/login', []);
        $emptyLogin->assertStatus(422)->assertJsonValidationErrors(['phone', 'password']);

        $seller = User::factory()->create(['is_seller' => true]);
        Store::factory()->create(['user_id' => $seller->id, 'status' => 'active']);
        Sanctum::actingAs($seller);

        $invalidProduct = $this->postJson('/api/products', [
            'name' => 'Invalid Product',
            'price' => -1,
            'stock' => -5,
        ]);
        $invalidProduct->assertStatus(422)->assertJsonValidationErrors(['price', 'stock']);

        $notFound = $this->getJson('/api/products/999999');
        $notFound->assertStatus(404);
    }

    public function test_cross_seller_web_product_update_must_be_forbidden(): void
    {
        $sellerA = User::factory()->create(['is_seller' => true]);
        $storeA = Store::factory()->create(['user_id' => $sellerA->id, 'status' => 'active']);
        $category = Category::create(['name' => 'Gadget', 'slug' => 'gadget']);

        $productA = Product::factory()->create([
            'store_id' => $storeA->id,
            'category_id' => $category->id,
            'name' => 'Produk A',
            'price' => 99000,
            'stock' => 4,
        ]);

        $sellerB = User::factory()->create(['is_seller' => true]);
        Store::factory()->create(['user_id' => $sellerB->id, 'status' => 'active']);

        $response = $this->actingAs($sellerB)->put('/web/product/' . $productA->id . '/update', [
            'name' => 'Dibajak Seller B',
            'price' => 1000,
            'stock' => 1,
            'description' => 'Tidak boleh berhasil',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('Produk A', $productA->fresh()->name);
    }

    public function test_cross_seller_order_status_update_must_be_forbidden(): void
    {
        $sellerA = User::factory()->create(['is_seller' => true]);
        $sellerB = User::factory()->create(['is_seller' => true]);
        $buyer = User::factory()->create(['is_seller' => false]);

        $storeA = Store::factory()->create(['user_id' => $sellerA->id, 'status' => 'active']);
        Store::factory()->create(['user_id' => $sellerB->id, 'status' => 'active']);
        $product = Product::factory()->create(['store_id' => $storeA->id, 'stock' => 10, 'price' => 35000]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'store_id' => $storeA->id,
            'product_id' => $product->id,
            'variant_key' => 'default',
            'quantity' => 1,
            'unit_price_original' => 35000,
            'unit_price_final' => 35000,
            'discount_percent_applied' => 0,
            'total_price' => 35000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($sellerB)->put('/web/order/' . $order->id . '/status', [
            'status' => 'accepted',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('pending', $order->fresh()->status);
    }

    public function test_payment_feature_gap_is_detected_in_schema(): void
    {
        $this->assertFalse(Schema::hasColumn('orders', 'payment_method'));
        $this->assertFalse(Schema::hasColumn('orders', 'payment_status'));
    }
}
