<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_success()
    {
        $user = User::factory()->create([
            'email' => 'test@email.com',
        ]);

        $data = [
            'email' => $user->email,
            'password' => 'Secret123!',
        ];

        $response = $this->postJson(config('test.url') . '/login', $data);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token'
                ]
            ]);
    }

    public function test_email_is_required()
    {
        $data = [
            'password' => 'secret123!',
        ];

        $this->postJson(config('test.url') . '/login', $data)
            ->assertStatus(422) // Unprocessable Entity (validation errors)
            ->assertJsonValidationErrors('email');
    }

    public function test_email_must_be_a_valid_email_address()
    {
        $data = [
            'email' => 'invalid_email',
            'password' => 'secret123!',
        ];

        $this->postJson(config('test.url') . '/login', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_password_is_required()
    {
        $data = [
            'email' => 'valid@email.com',
        ];

        $this->postJson(config('test.url') . '/login', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_password_must_be_at_least_8_characters_long()
    {
        $data = [
            'email' => 'valid@email.com',
            'password' => 'short',
        ];

        $this->postJson(config('test.url') . '/login', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }
}
