<?php

namespace Database\Factories;

use App\Contracts\ContentCollectionModel;
use App\Models\Analytics;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Analytics>
 */
class AnalyticsFactory extends Factory
{
    public function definition(): array
    {
        $timestamp = $this->faker->dateTimeBetween('-90 days', 'now');

        return [
            'url' => '/'.$this->faker->slug(),
            // Draw from a limited pool of visitors so some return across views,
            // keeping "unique visitors" believably lower than total views.
            'visitor_hash' => hash('sha256', (string) $this->faker->numberBetween(1, 250)),
            'referrer' => $this->faker->randomElement([
                'google.com', 'google.com', 'google.com',
                'facebook.com', 'facebook.com',
                'instagram.com', 'instagram.com',
                'seznam.cz',
                null, null, null,   // direct traffic
            ]),
            'device_type' => $this->faker->randomElement([
                'mobile', 'mobile', 'mobile', 'mobile',
                'desktop', 'desktop', 'desktop',
                'tablet',
            ]),
            'country' => $this->faker->randomElement([
                'CZ', 'CZ', 'CZ', 'CZ', 'CZ', 'CZ', 'CZ',
                'SK', 'SK',
                'DE', 'AT', 'PL', 'GB', 'US',
            ]),
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }

    /**
     * Attach the row to a piece of content, keeping the url consistent with it.
     */
    public function forSubject(ContentCollectionModel $subject): static
    {
        return $this->state(fn (): array => [
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'url' => $subject->getPermalink(),
        ]);
    }
}
