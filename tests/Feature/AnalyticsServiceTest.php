<?php

use App\Models\Analytics;
use App\Models\Page;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Carbon::setTestNow(Carbon::parse('2026-06-19 12:00:00'));
});

test('dailyVisits returns a zero-filled current-period series keyed by date', function (): void {
    Analytics::factory()->create(['created_at' => Carbon::today()]);
    Analytics::factory()->count(2)->create(['created_at' => Carbon::today()->subDays(3)]);

    $series = app(AnalyticsService::class)->dailyVisits(7);

    expect($series)
        ->toHaveCount(7)
        ->and(array_keys($series))->toBe([
            '2026-06-13', '2026-06-14', '2026-06-15', '2026-06-16',
            '2026-06-17', '2026-06-18', '2026-06-19',
        ])
        ->and($series['2026-06-19'])->toBe(1)
        ->and($series['2026-06-16'])->toBe(2)
        ->and($series['2026-06-17'])->toBe(0);
});

test('dailyVisits with endDaysAgo returns the preceding period and excludes current-period rows', function (): void {
    // Current period (last 7 days) — must NOT appear in the previous window.
    Analytics::factory()->count(5)->create(['created_at' => Carbon::today()]);
    // Previous period (the 7 days before that): 2026-06-06 .. 2026-06-12.
    Analytics::factory()->count(3)->create(['created_at' => Carbon::parse('2026-06-10 08:00:00')]);

    $series = app(AnalyticsService::class)->dailyVisits(7, endDaysAgo: 7);

    expect(array_keys($series))->toBe([
        '2026-06-06', '2026-06-07', '2026-06-08', '2026-06-09',
        '2026-06-10', '2026-06-11', '2026-06-12',
    ])
        ->and($series['2026-06-10'])->toBe(3)
        ->and(array_sum($series))->toBe(3);
});

test('mostVisitedSubject returns the content item with the most visits', function (): void {
    $popular = Page::factory()->create();
    $quiet = Page::factory()->create();

    Analytics::factory()->count(5)->forSubject($popular)->create();
    Analytics::factory()->count(2)->forSubject($quiet)->create();

    expect(app(AnalyticsService::class)->mostVisitedSubject()->is($popular))->toBeTrue();
});

test('mostVisitedSubject skips deleted subjects and falls back to the next existing one', function (): void {
    $deleted = Page::factory()->create();
    $existing = Page::factory()->create();

    // The deleted page is the most visited, but can no longer be displayed.
    Analytics::factory()->count(10)->forSubject($deleted)->create();
    Analytics::factory()->count(3)->forSubject($existing)->create();

    $deleted->delete();

    expect(app(AnalyticsService::class)->mostVisitedSubject()->is($existing))->toBeTrue();
});

test('mostVisitedSubject returns null when no subject still exists', function (): void {
    $deleted = Page::factory()->create();
    Analytics::factory()->count(4)->forSubject($deleted)->create();
    $deleted->delete();

    expect(app(AnalyticsService::class)->mostVisitedSubject())->toBeNull();
});
