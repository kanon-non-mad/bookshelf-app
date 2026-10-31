<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'isbn' => $this->faker->unique()->numerify('#############'),
            'title' => 'テスト本',
            'author' => '著者',
            'published_at' => '2026-01-01',
        ];
    }
}
