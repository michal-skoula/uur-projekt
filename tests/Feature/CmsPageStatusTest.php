<?php

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a published page is publicly visible', function (): void {
    Page::factory()->published()->create(['slug' => '']);

    $this->get('/')->assertOk();
});

test('a draft page returns 404 for guests', function (): void {
    Page::factory()->create(['slug' => 'tajna-stranka']); // draft by default

    $this->get('/tajna-stranka')->assertNotFound();
});

test('a disabled page returns 404 for guests', function (): void {
    Page::factory()->disabled()->create(['slug' => 'vypnuta-stranka']);

    $this->get('/vypnuta-stranka')->assertNotFound();
});

test('an authenticated admin can preview a draft page on the live site', function (): void {
    Page::factory()->create(['slug' => 'tajna-stranka']);

    $this->actingAs(User::factory()->create())
        ->get('/tajna-stranka')
        ->assertOk();
});
