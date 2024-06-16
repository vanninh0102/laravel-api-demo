<?php

namespace Tests\Feature;

use App\Http\Requests\Store\CreateStoreRequest;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Request;
use Tests\TestCase;

class StoresTest extends TestCase
{
    use RefreshDatabase;

    public function test_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user); // Act as authenticated user
        Store::factory(25)->create();

        $response = $this->get(config('test.url') . '/stores');

        // dd(auth()->guard()->user()->getAttributes(), $response->json());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'data' => ['*' => []]
                ]
            ]);
    }

    public function test_store_creates_success()
    {
        $user = User::factory()->create();
        $this->actingAs($user); // Act as authenticated user

        $data = [
            'name' => 'Test Store',
            'description' => 'This is a test store.',
        ];

        $response = $this->postJson(config('test.url') . '/stores', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'user_id'
                ],
            ]);
    }

    // Additional test for unauthorized access (if applicable)
    public function test_store_returns_unauthorized_for_unauthenticated_user()
    {
        $data = [
            'name' => 'Test Store',
            'description' => 'This is a test store.',
        ];

        $response = $this->postJson(config('test.url') . '/stores', $data);

        $response->assertStatus(401);
    }

    // Additional test for unauthorized access (if applicable)
    public function test_store_returns_data_error()
    {
        $user = User::factory()->create();
        $this->actingAs($user); // Act as authenticated user

        $data = [
            // 'name' => 'Test Store',
            'description' => 'This is a test store.',
        ];

        $response = $this->postJson(config('test.url') . '/stores', $data);


        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    // Additional test for unauthorized access (if applicable)
    public function test_store_duplicate_name()
    {
        $user = User::factory()->create();
        $this->actingAs($user); // Act as authenticated user

        Store::factory(1)->create(['name' => 'Test Store']);

        $data = [
            'name' => 'Test Store',
            'description' => 'This is a test store.',
        ];

        $response = $this->postJson(config('test.url') . '/stores', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_show_returns_store_for_authenticated_user()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->getJson(config('test.url') . "/stores/$store->id");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'user_id',
            ],
            'message',
        ]);
    }

    public function test_show_returns_not_found_for_nonexistent_store()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson(config('test.url') . "/stores/123");

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message',
        ]);
    }

    public function test_update_store()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $data = [
            'name' => 'Updated Store Name',
            'description' => 'This is an updated description.',
        ];

        $response = $this->putJson(config('test.url') . "/stores/$store->id", $data);

        $response->assertStatus(200);
    }

    public function test_update_block_update_store_of_other_user()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user2->id]);
        $this->actingAs($user);

        $data = [
            'name' => 'Updated Store Name',
            'description' => 'This is an updated description.',
        ];

        $response = $this->putJson(config('test.url') . "/stores/$store->id", $data);

        $response->assertStatus(404);
        $data = $response->json();
        $this->assertContains('Store not found', $data);
    }

    public function test_update_must_have_name()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $data = [
            // 'name' => 'Updated Store Name',
            'description' => 'This is an updated description.',
        ];

        $response = $this->putJson(config('test.url') . "/stores/$store->id", $data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'errors' => [
                'name' => ["The name field is required."]
            ]
        ]);
    }

    public function test_delete_store()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->deleteJson(config('test.url') . "/stores/$store->id");

        $response->assertStatus(200);
    }

    public function test_delete_store_from_other_user()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $store = Store::factory()->create(['user_id' => $user2->id]);
        $this->actingAs($user);

        $response = $this->deleteJson(config('test.url') . "/stores/$store->id");

        $response->assertStatus(404);
    }
}
