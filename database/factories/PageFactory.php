<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence();

        return [
            'title' => $title,
            'slug' => Str::kebab($title),
            'parent_id' => null,
            'content' => [],
            'status' => ContentStatus::DRAFT,
        ];
    }

    public function published(): static
    {
        return $this->state(['status' => ContentStatus::PUBLISHED]);
    }

    public function disabled(): static
    {
        return $this->state(['status' => ContentStatus::DISABLED]);
    }
}
