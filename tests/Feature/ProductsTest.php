<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Store::factory(2)->create();
        Product::factory(30)->create();

        $response = $this->get(config('test.url') . '/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'data' => ['*' => []]
                ]
            ]);
    }

    public function test_product_creates_success()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $store = Store::factory()->create(['user_id' => $user->id]);

        $data = [
            'name' => 'Test product',
            'description' => 'This is a test product.',
            'amount' => 1,
            'price' => 123,
            'store_id' => $store->id,
        ];

        $response = $this->postJson(config('test.url') . '/products', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'amount',
                    'price',
                    'store_id'
                ],
            ]);
    }

    // Additional test for unauthorized access (if applicable)
    public function test_product_returns_unauthorized_for_unauthenticated_user()
    {
        $data = [
            'name' => 'Test product',
            'description' => 'This is a test product.',
            'amount' => 1,
            'price' => 123,
            'store_id' => 1,
        ];

        $response = $this->postJson(config('test.url') . '/products', $data);

        $response->assertStatus(401);
    }

    public function test_create_product_must_have_name()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $data = [
            // 'name' => 'Test product',
            'description' => 'This is a test product.',
            'amount' => 1,
            'price' => 123,
            'store_id' => $store->id,
        ];

        $response = $this->postJson(config('test.url') . '/products', $data);


        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_create_product_duplicate_name()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['name' => 'Test product', 'store_id' => $store->id]);
        $this->actingAs($user);

        $data = [
            'name' => $product->name,
            'description' => 'This is a test product.',
            'amount' => 1,
            'price' => 123,
            'store_id' => $store->id,
        ];

        $response = $this->postJson(config('test.url') . '/products', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_show_product()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['name' => 'Test product', 'store_id' => $store->id]);
        $this->actingAs($user);

        $response = $this->getJson(config('test.url') . "/products/$product->id");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'amount',
                'price',
                'store_id'
            ],
            'message',
        ]);
    }

    public function test_show_returns_not_found_for_nonexistent_product()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson(config('test.url') . "/products/123");

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message',
        ]);
    }

    public function test_update_product()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['name' => 'Test product', 'store_id' => $store->id]);
        $this->actingAs($user);

        $data = [
            'name' => 'Updated Product Name',
            'description' => 'This is an updated description.',
            'amount' => 1,
            'price' => 123,
            'store_id' => $store->id,
        ];

        $response = $this->putJson(config('test.url') . "/products/$product->id", $data);

        $response->assertStatus(200);
    }

    public function test_update_block_update_product_of_other_user()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user2->id]);
        $product = Product::factory()->create(['name' => 'Test product', 'store_id' => $store->id]);
        $this->actingAs($user);

        $data = [
            'name' => 'Updated Product Name',
            'description' => 'This is an updated description.',
            'amount' => 1,
            'price' => 123,
            'store_id' => $store->id,
        ];

        $response = $this->putJson(config('test.url') . "/products/$product->id", $data);

        $response->assertStatus(422);
        $data = $response->json();
        $this->assertContains('The selected store id is invalid for current user', $data);
    }

    public function test_update_must_have_name()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['name' => 'Test product', 'store_id' => $store->id]);
        $this->actingAs($user);

        $data = [
            // 'name' => 'Updated Product Name',
            'description' => 'This is an updated description.',
            'amount' => 1,
            'price' => 123,
            'store_id' => $store->id,
        ];

        $response = $this->putJson(config('test.url') . "/products/$product->id", $data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'errors' => [
                'name' => ["The name field is required."]
            ]
        ]);
    }

    public function test_update_product_duplicate_name()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['name' => 'Test product', 'store_id' => $store->id]);
        $product2 = Product::factory()->create(['name' => 'Test product 2', 'store_id' => $store->id]);
        $this->actingAs($user);

        $data = [
            'name' => $product2->name,
            'description' => 'This is a test product.',
            'amount' => 1,
            'price' => 123,
            'store_id' => $store->id,
        ];

        $response = $this->putJson(config('test.url') . "/products/$product->id", $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_delete_product()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['name' => 'Test product', 'store_id' => $store->id]);
        $this->actingAs($user);

        $response = $this->deleteJson(config('test.url') . "/products/$product->id");

        $response->assertStatus(200);
    }

    public function test_delete_store_from_other_user()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user2->id]);
        $product = Product::factory()->create(['name' => 'Test product', 'store_id' => $store->id]);
        $this->actingAs($user);

        $response = $this->deleteJson(config('test.url') . "/products/$product->id");

        $response->assertStatus(404);
    }
}
