<?php

namespace Tests\Feature;

use App\Models\AccessCode;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BuyerToSellerWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected AccessCode $activeCode;
    protected User $buyer;
    protected User $otherBuyer;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'location.center_lat' => -6.200000,
            'location.center_lng' => 106.816666,
            'location.max_radius' => 5,
        ]);

        $this->activeCode = AccessCode::create([
            'code' => 'BUYER-SELLER-TEST',
            'is_active' => true,
        ]);

        // Create two buyer users
        $this->buyer = User::factory()->create([
            'name' => 'Pembeli Menjadi Penjual',
            'phone' => '+628333330100',
            'is_seller' => false,
            'role' => 'buyer',
            'access_code_id' => $this->activeCode->id,
        ]);

        $this->otherBuyer = User::factory()->create([
            'name' => 'Pembeli Lain',
            'phone' => '+628333330101',
            'is_seller' => false,
            'role' => 'buyer',
            'access_code_id' => $this->activeCode->id,
        ]);
    }

    /**
     * Test complete workflow: buyer switches to seller, uploads products, and other buyers can see them.
     *
     * Scenario:
     * 1. A buyer user starts with is_seller = false
     * 2. User switches to seller mode via PATCH /api/profile/seller-mode
     * 3. User verifies is_seller = true and has a store
     * 4. User creates a product via POST /api/products
     * 5. Product is visible in public API endpoint GET /api/products
     * 6. Other buyer can view the product via GET /api/products/{product}
     * 7. Other buyer can search for the product via GET /api/products/search
     * 8. Product pricing, stock, and details are correct
     */
    public function test_buyer_switches_to_seller_uploads_product_visible_to_other_buyers(): void
    {
        // Step 1: Verify buyer is not a seller initially
        $this->assertFalse($this->buyer->is_seller);
        $this->assertNull($this->buyer->store);

        // Step 2: Toggle seller mode
        Sanctum::actingAs($this->buyer);
        $toggleResponse = $this->patchJson('/api/profile/seller-mode', [
            'enable' => true,
            'store_name' => 'Toko Elektronik Budi',
            'store_description' => 'Toko elektronik terpercaya',
            'store_address' => 'Jl. Merdeka No. 123, Jakarta',
        ]);

        // Verify toggle success
        $toggleResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Seller mode activated successfully.');

        // Step 3: Verify user is now seller with store
        $this->buyer = $this->buyer->fresh();
        $this->assertTrue($this->buyer->is_seller);
        $this->assertNotNull($this->buyer->store);
        $this->assertEquals('Toko Elektronik Budi', $this->buyer->store->name);
        $this->assertEquals('Toko elektronik terpercaya', $this->buyer->store->description);

        // Refresh authenticated user in Sanctum for role-based middleware
        Sanctum::actingAs($this->buyer->fresh());

        // Step 4: Create a product
        $category = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik']);

        $productData = [
            'name' => 'Smartphone XYZ',
            'description' => 'Smartphone terbaru dengan kamera 48MP',
            'price' => 4500000,
            'stock' => 15,
            'category_id' => $category->id,
        ];

        $createResponse = $this->postJson('/api/products', $productData);

        $createResponse->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Smartphone XYZ')
            ->assertJsonPath('data.price', 4500000)
            ->assertJsonPath('data.stock', 15);

        $productId = (int) $createResponse->json('data.id');
        $this->assertGreaterThan(0, $productId);

        // Verify product is in database
        $this->assertDatabaseHas('products', [
            'id' => $productId,
            'store_id' => $this->buyer->store->id,
            'name' => 'Smartphone XYZ',
            'price' => 4500000,
            'stock' => 15,
        ]);

        // Step 5: Verify product is visible in public API listing
        $listResponse = $this->getJson('/api/products');
        $listResponse->assertOk()
            ->assertJsonPath('success', true);

        $products = $listResponse->json('data.data');
        $this->assertNotEmpty($products);

        $uploadedProduct = collect($products)->firstWhere('id', $productId);
        $this->assertNotNull($uploadedProduct, 'Product should be visible in public listing');
        $this->assertEquals('Smartphone XYZ', $uploadedProduct['name']);
        $this->assertEquals(4500000, $uploadedProduct['price']);
        $this->assertEquals(15, $uploadedProduct['stock']);
        $this->assertEquals('Toko Elektronik Budi', $uploadedProduct['store']['name']);

        // Step 6: Verify other buyer can view product detail
        Sanctum::actingAs($this->otherBuyer);
        $detailResponse = $this->getJson('/api/products/' . $productId);

        $detailResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Smartphone XYZ')
            ->assertJsonPath('data.price', 4500000)
            ->assertJsonPath('data.stock', 15)
            ->assertJsonPath('data.store.name', 'Toko Elektronik Budi');

        // Step 7: Verify other buyer can search for the product
        $searchResponse = $this->getJson('/api/products/search?q=Smartphone');
        $searchResponse->assertOk()
            ->assertJsonPath('success', true);

        $searchResults = $searchResponse->json('data.data');
        $this->assertNotEmpty($searchResults);

        $foundProduct = collect($searchResults)->firstWhere('id', $productId);
        $this->assertNotNull($foundProduct, 'Product should be searchable');
        $this->assertEquals('Smartphone XYZ', $foundProduct['name']);

        // Also search by category
        $categorySearch = $this->getJson('/api/products/search?q=Elektronik');
        $categorySearch->assertOk();
        $categorySearchResults = $categorySearch->json('data.data');
        $this->assertNotEmpty($categorySearchResults);
    }

    /**
     * Test that multiple products uploaded by the switched seller are all visible.
     */
    public function test_seller_uploads_multiple_products_all_visible_to_buyers(): void
    {
        // Toggle to seller
        Sanctum::actingAs($this->buyer);
        $this->patchJson('/api/profile/seller-mode', [
            'enable' => true,
            'store_name' => 'Multi Product Store',
        ]);

        $this->buyer = $this->buyer->fresh();
        Sanctum::actingAs($this->buyer);

        $category = Category::create(['name' => 'Gadget', 'slug' => 'gadget']);

        // Create first product
        $product1 = $this->postJson('/api/products', [
            'name' => 'Laptop Pro 15',
            'description' => 'Laptop profesional',
            'price' => 15000000,
            'stock' => 5,
            'category_id' => $category->id,
        ])->json('data');

        // Create second product
        $product2 = $this->postJson('/api/products', [
            'name' => 'Monitor 4K',
            'description' => 'Monitor resolusi tinggi',
            'price' => 3500000,
            'stock' => 8,
            'category_id' => $category->id,
        ])->json('data');

        // Create third product
        $product3 = $this->postJson('/api/products', [
            'name' => 'Keyboard Mechanical',
            'description' => 'Keyboard gaming',
            'price' => 1200000,
            'stock' => 20,
            'category_id' => $category->id,
        ])->json('data');

        // Other buyer sees all products in listing
        Sanctum::actingAs($this->otherBuyer);
        $listResponse = $this->getJson('/api/products');
        $products = $listResponse->json('data.data');

        $product1Found = collect($products)->firstWhere('id', $product1['id']);
        $product2Found = collect($products)->firstWhere('id', $product2['id']);
        $product3Found = collect($products)->firstWhere('id', $product3['id']);

        $this->assertNotNull($product1Found, 'Laptop Pro 15 should be visible');
        $this->assertNotNull($product2Found, 'Monitor 4K should be visible');
        $this->assertNotNull($product3Found, 'Keyboard Mechanical should be visible');

        $this->assertEquals('Laptop Pro 15', $product1Found['name']);
        $this->assertEquals('Monitor 4K', $product2Found['name']);
        $this->assertEquals('Keyboard Mechanical', $product3Found['name']);

        // Verify store info is consistent
        $this->assertEquals('Multi Product Store', $product1Found['store']['name']);
        $this->assertEquals('Multi Product Store', $product2Found['store']['name']);
        $this->assertEquals('Multi Product Store', $product3Found['store']['name']);
    }

    /**
     * Test that seller can modify products and updates are visible to buyers.
     */
    public function test_seller_modifies_product_changes_visible_to_buyers(): void
    {
        // Switch to seller
        Sanctum::actingAs($this->buyer);
        $this->patchJson('/api/profile/seller-mode', [
            'enable' => true,
            'store_name' => 'Update Test Store',
        ]);

        $this->buyer = $this->buyer->fresh();
        Sanctum::actingAs($this->buyer);

        $category = Category::create(['name' => 'Fashion', 'slug' => 'fashion']);

        // Create product
        $createResponse = $this->postJson('/api/products', [
            'name' => 'T-Shirt Original',
            'description' => 'Kaos original',
            'price' => 150000,
            'stock' => 50,
            'category_id' => $category->id,
        ]);

        $productId = (int) $createResponse->json('data.id');

        // Update product
        $updateResponse = $this->putJson('/api/products/' . $productId, [
            'name' => 'T-Shirt Premium Edition',
            'description' => 'Kaos premium quality',
            'price' => 250000,
            'stock' => 30,
            'category_id' => $category->id,
        ]);

        $updateResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'T-Shirt Premium Edition')
            ->assertJsonPath('data.price', 250000)
            ->assertJsonPath('data.stock', 30);

        // Other buyer can see the updated product
        Sanctum::actingAs($this->otherBuyer);
        $detailResponse = $this->getJson('/api/products/' . $productId);

        $detailResponse->assertOk()
            ->assertJsonPath('data.name', 'T-Shirt Premium Edition')
            ->assertJsonPath('data.price', 250000)
            ->assertJsonPath('data.stock', 30)
            ->assertJsonPath('data.description', 'Kaos premium quality');
    }

    /**
     * Test that deleted products are no longer visible to buyers.
     */
    public function test_seller_deletes_product_no_longer_visible_to_buyers(): void
    {
        // Switch to seller
        Sanctum::actingAs($this->buyer);
        $this->patchJson('/api/profile/seller-mode', [
            'enable' => true,
            'store_name' => 'Delete Test Store',
        ]);

        $this->buyer = $this->buyer->fresh();
        Sanctum::actingAs($this->buyer);

        $category = Category::create(['name' => 'Books', 'slug' => 'books']);

        // Create product
        $createResponse = $this->postJson('/api/products', [
            'name' => 'Book: Laravel Guide',
            'description' => 'Panduan lengkap Laravel',
            'price' => 250000,
            'stock' => 20,
            'category_id' => $category->id,
        ]);

        $productId = (int) $createResponse->json('data.id');

        // Verify product is visible to other buyer
        Sanctum::actingAs($this->otherBuyer);
        $beforeDelete = $this->getJson('/api/products/' . $productId);
        $beforeDelete->assertOk();

        // Seller deletes product
        Sanctum::actingAs($this->buyer);
        $deleteResponse = $this->deleteJson('/api/products/' . $productId);

        $deleteResponse->assertOk()
            ->assertJsonPath('success', true);

        // Verify product is deleted from database
        $this->assertDatabaseMissing('products', ['id' => $productId]);

        // Other buyer cannot find product
        Sanctum::actingAs($this->otherBuyer);
        $afterDelete = $this->getJson('/api/products/' . $productId);
        $afterDelete->assertNotFound();
    }

    /**
     * Test product visibility filters: only products from active stores with seller status are shown.
     */
    public function test_inactive_seller_products_not_visible_to_buyers(): void
    {
        // Create a seller with inactive store
        $inactiveSeller = User::factory()->create([
            'is_seller' => true,
            'role' => 'buyer',
        ]);

        $inactiveStore = Store::factory()->create([
            'user_id' => $inactiveSeller->id,
            'status' => 'inactive', // Status is inactive
        ]);

        $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);

        $hiddenProduct = Product::factory()->create([
            'store_id' => $inactiveStore->id,
            'category_id' => $category->id,
            'name' => 'Secret Product',
            'price' => 100000,
            'stock' => 10,
        ]);

        // Check listing - hidden product should not appear
        Sanctum::actingAs($this->otherBuyer);
        $listResponse = $this->getJson('/api/products');
        $products = $listResponse->json('data.data');

        $found = collect($products)->firstWhere('id', $hiddenProduct->id);
        $this->assertNull($found, 'Product from inactive store should not be visible');

        // Direct access to product should also fail
        $detailResponse = $this->getJson('/api/products/' . $hiddenProduct->id);
        $detailResponse->assertNotFound();
    }

    /**
     * Test that switching back to buyer mode preserves products but hides seller functionality.
     */
    public function test_seller_switches_back_to_buyer_products_remain(): void
    {
        // Switch to seller
        Sanctum::actingAs($this->buyer);
        $this->patchJson('/api/profile/seller-mode', [
            'enable' => true,
            'store_name' => 'Toggle Test Store',
        ]);

        $this->buyer = $this->buyer->fresh();
        Sanctum::actingAs($this->buyer);

        $category = Category::create(['name' => 'Test', 'slug' => 'test']);

        // Create product
        $createResponse = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 100000,
            'stock' => 10,
            'category_id' => $category->id,
        ]);

        $productId = (int) $createResponse->json('data.id');

        // Product is visible
        $beforeToggle = $this->getJson('/api/products/' . $productId);
        $beforeToggle->assertOk();

        // Switch back to buyer
        $toggleOffResponse = $this->patchJson('/api/profile/seller-mode', [
            'enable' => false,
        ]);

        $toggleOffResponse->assertOk()
            ->assertJsonPath('message', 'Seller mode deactivated successfully.');

        $this->buyer = $this->buyer->fresh();
        $this->assertFalse($this->buyer->is_seller);

        // Product still exists in database
        $this->assertDatabaseHas('products', ['id' => $productId]);

        // Product is still visible to other buyers
        Sanctum::actingAs($this->otherBuyer);
        $afterToggle = $this->getJson('/api/products/' . $productId);
        $afterToggle->assertOk();

        // Original seller (now buyer) cannot create products without seller mode
        Sanctum::actingAs($this->buyer);
        $forbiddenResponse = $this->postJson('/api/products', [
            'name' => 'New Product',
            'price' => 50000,
            'stock' => 5,
        ]);

        // Should be forbidden due to role middleware
        $forbiddenResponse->assertStatus(403);
    }
}
