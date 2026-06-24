<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
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
            'status' => ContentStatus::PUBLISHED,
        ];
    }

    public function published(): static
    {
        return $this->state(['status' => ContentStatus::PUBLISHED]);
    }

    public function draft(): static
    {
        return $this->state(['status' => ContentStatus::DRAFT]);
    }

    public function disabled(): static
    {
        return $this->state(['status' => ContentStatus::DISABLED]);
    }
}
