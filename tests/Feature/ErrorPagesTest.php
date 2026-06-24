<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

it('renders the styled frontend 404 page with full site chrome', function (): void {
    $response = $this->get('/this-page-does-not-exist');

    $response->assertStatus(404);
    $response->assertSee(__('errors.codes.404.title'));
    $response->assertSee(__('errors.back_home'));
    // The footer proves the full site chrome (header + footer) is present.
    $response->assertSee('id="colophon"', false);
});

it('renders a minimal frontend 5xx page without site chrome', function (): void {
    $html = view('errors.5xx', ['exception' => new HttpException(500)])->render();

    expect($html)
        ->toContain(__('errors.codes.500.title'))
        ->toContain(__('errors.back_home'))
        ->not->toContain('id="colophon"');
});

it('resolves generic copy for uncommon client errors via the 4xx fallback', function (): void {
    $html = view('errors.4xx', ['exception' => new HttpException(418)])->render();

    expect($html)
        ->toContain('418')
        ->toContain(__('errors.codes.4xx.title'));
});

it('renders the barebones Filament error page for admin requests', function (): void {
    $response = $this->get('/admin/this-page-does-not-exist');

    $response->assertStatus(404);
    $response->assertSee(__('errors.codes.404.title'));
    $response->assertSee(__('errors.back_dashboard'));
    // No public-site footer: the admin set uses the Filament layout instead.
    $response->assertDontSee('id="colophon"', false);
});
