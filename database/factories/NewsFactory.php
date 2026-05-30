<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->paragraph(),
            'content' => '<p>'.implode('</p><p>', $this->faker->paragraphs(4)).'</p>',
            'thumbnail' => null,
            'author' => $this->faker->name(),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
