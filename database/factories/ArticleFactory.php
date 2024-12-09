<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doc_id' => $this->faker->unique()->randomNumber(8),
            'source' => $this->faker->randomElement(['nytimes', 'cnn', 'bbc']),
            'published_at' => $this->faker->date(),
            'author' => $this->faker->name(),
            'category' => $this->faker->randomElement(['Tech', 'Health', 'Sports']),
            'content' => json_encode(['title' => $this->faker->sentence()]),
        ];
    }
}
