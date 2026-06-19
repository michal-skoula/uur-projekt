<?php

namespace Database\Seeders;

use App\Models\Analytics;
use App\Models\News;
use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnalyticsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Page::all() as $page) {
            Analytics::factory()
                ->forSubject($page)
                ->count(random_int(20, 120))
                ->create();
        }

        foreach (News::all() as $news) {
            Analytics::factory()
                ->forSubject($news)
                ->count(random_int(20, 120))
                ->create();
        }

        // A handful of subject-less views (listing pages, misc urls).
        Analytics::factory()
            ->count(random_int(10, 30))
            ->create();
    }
}
