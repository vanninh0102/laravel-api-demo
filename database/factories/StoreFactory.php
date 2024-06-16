<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::limit(5)->get()->random();
        $created_at = fake()->dateTimeBetween('-1 year');
        $updated_at = (clone $created_at)->add(date_interval_create_from_date_string(fake()->numberBetween(1, 100) . " days"));

        return [
            'name' => 'Store' . fake()->company(),
            'description' => fake()->paragraph(2), // Generate a 2-paragraph description
            'user_id' => $user->id,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];
    }
}
