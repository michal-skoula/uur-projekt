<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    //    use WithoutModelEvents;

    public function run(): void
    {
        $p1 = Page::factory()->create([]);
        $p2 = Page::factory()->create([]);
        $p3 = Page::factory()->create([]);
        $p4 = Page::factory()->create(['parent_id' => $p1->id]);
        $p5 = Page::factory()->create(['parent_id' => $p2->id]);
        $p6 = Page::factory()->create(['parent_id' => $p4->id]);
        $p7 = Page::factory()->create(['parent_id' => $p3->id]);
        $p8 = Page::factory()->create(['parent_id' => $p5->id]);
        $p9 = Page::factory()->create(['parent_id' => $p6->id]);
        $p10 = Page::factory()->create(['parent_id' => $p1->id]);
        $p11 = Page::factory()->create(['parent_id' => $p8->id]);
        $p11 = Page::factory()->create(['parent_id' => $p6->id]);

    }
}
