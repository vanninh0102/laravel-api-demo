<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_successful()
    {
        $userData = [
            "name" => "Test User",
            "email" => "valid@email.com",
            "password" => "Secret123!",
            "password_confirmation" => "Secret123!",
        ];

        $response = $this->json('POST', config('test.url') . '/register', $userData);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'token',
                'name'
            ],
            'message'
        ]);
    }

    public function test_name_is_required()
    {
        $data = [
            'email' => 'valid@email.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
        ];

        $this->postJson(config('test.url') . '/register', $data)
            ->assertStatus(422) // Unprocessable Entity (validation errors)
            ->assertJsonValidationErrors('name');
    }

    public function test_name_cannot_exceed_255_characters()
    {
        $longName = str_repeat('a', 256); // Create a name longer than 255 chars

        $data = [
            'name' => $longName,
            'email' => 'valid@email.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
        ];

        $this->postJson(config('test.url') . '/register', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_email_is_required()
    {
        $data = [
            'name' => 'Test 001',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
        ];

        $this->postJson(config('test.url') . '/register', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_email_must_be_a_valid_email_address()
    {
        $data = [
            'name' => 'Test 001',
            'email' => 'invalid_email',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
        ];

        $this->postJson(config('test.url') . '/register', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_email_must_be_unique()
    {
        // Create a user with a pre-existing email
        $user = User::factory()->create(['email' => 'existing@email.com']);

        $data = [
            'name' => 'Test 001',
            'email' => $user->email,
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
        ];

        $this->postJson(config('test.url') . '/register', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_password_is_required()
    {
        $data = [
            'name' => 'Test 001',
            'email' => 'valid@email.com',
            'password_confirmation' => 'Secret123!',
        ];

        $this->postJson(config('test.url') . '/register', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_password_must_be_at_least_8_characters_long()
    {
        $data = [
            'name' => 'Test 001',
            'email' => 'valid@email.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ];

        $this->postJson(config('test.url') . '/register', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_password_must_contain_uppercases()
    {
        $data = [
            'name' => 'Test 001',
            'email' => 'valid@email.com',
            'password' => 'lowercase123!',
            'password_confirmation' => 'lowercase123!',
        ];

        $this->postJson(config('test.url') . '/register', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_password_must_contain_symbols()
    {
        $data = [
            'name' => 'Test 001',
            'email' => 'valid@email.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson(config('test.url') . '/register', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }
}
