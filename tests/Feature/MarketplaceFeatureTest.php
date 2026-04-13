<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Chat;
use App\Notifications\ChatMessageNotification;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusNotification;
use App\Notifications\SellerApplicationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MarketplaceFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketplace_full_flow()
    {
        Notification::fake();

        // 1. Create Users
        $admin = User::factory()->create(['role' => 'admin']);
        $buyer1 = User::factory()->create(['is_seller' => false]);
        $buyer2 = User::factory()->create(['is_seller' => false]);

        // 2. Buyer1 activates seller mode
        $response = $this->actingAs($buyer1)
            ->from('/profile')
            ->post('/seller/activate', [
                'store_name' => 'Toko Keren',
            ]);
        $response->assertRedirect('/profile');
        $this->assertTrue((bool) $buyer1->fresh()->is_seller);
        $this->assertEquals('approved', $buyer1->fresh()->seller_status);
        $this->assertNotNull($buyer1->fresh()->store);

        // 3. Admin can still approve seller endpoint idempotently
        $response = $this->actingAs($admin)->post('/admin/sellers/' . $buyer1->id . '/approve');
        $response->assertRedirect('/admin/sellers');
        $this->assertTrue((bool) $buyer1->fresh()->is_seller);

        Notification::assertSentTo($buyer1, SellerApplicationNotification::class);

        // 4. Seller (Buyer1) creates a product
        $category = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik', 'sort_order' => 1]);
        $product = $buyer1->fresh()->store->products()->create([
            'category_id' => $category->id,
            'name' => 'Smartphone Canggih',
            'description' => 'HP Mantap',
            'price' => 5000000,
            'stock' => 10,
        ]);
        $this->assertDatabaseHas('products', ['name' => 'Smartphone Canggih']);

        // 5. Buyer2 adds to cart and checks out
        $this->actingAs($buyer2)->post('/buyer/cart', [
            'product_id' => $product->id,
            'quantity' => 2
        ])->assertSessionHasNoErrors();
        
        $this->assertDatabaseHas('carts', ['user_id' => $buyer2->id, 'product_id' => $product->id]);

        $this->actingAs($buyer2)->post('/buyer/cart/checkout')->assertRedirect();
        
        $order = Order::where('user_id', $buyer2->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(10000000, $order->total_price);
        $this->assertEquals(8, $product->fresh()->stock);

        Notification::assertSentTo($buyer1, NewOrderNotification::class);

        // 6. Seller receives order and accepts
        $this->actingAs($buyer1->fresh())->put('/web/order/' . $order->id . '/status', [
            'status' => 'accepted'
        ]);
        $this->assertEquals('accepted', $order->fresh()->status);

        Notification::assertSentTo($buyer2, OrderStatusNotification::class);

        // 7. Chat Initialization and message
        // Chat is created when buyer visits chat page
        $this->actingAs($buyer2)->get('/chat/' . $order->id);
        $chat = Chat::where('order_id', $order->id)->first();
        $this->assertNotNull($chat);

        // Seller sends a message
        $this->actingAs($buyer1->fresh())->post('/chat/' . $chat->id, [
            'message' => 'Pesanan segera dikirim ya kak!'
        ]);
        
        $this->assertDatabaseHas('messages', [
            'chat_id' => $chat->id,
            'sender_id' => $buyer1->id,
            'message' => 'Pesanan segera dikirim ya kak!'
        ]);

        Notification::assertSentTo($buyer2, ChatMessageNotification::class);
        
        // 8. Notifications API test
        $response = $this->actingAs($buyer2)->getJson('/api/notifications');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }
}
