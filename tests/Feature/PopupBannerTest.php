<?php

use App\Settings\PopupSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $settings = app(PopupSettings::class);
    $settings->enabled = false;
    $settings->stripeEnabled = false;
    $settings->stripeText = '';
    $settings->stripeCta = null;
    $settings->popupEnabled = false;
    $settings->popupImage = null;
    $settings->popupHeading = null;
    $settings->popupContent = null;
    $settings->popupCta = null;
    $settings->save();
});

it('renders nothing when globally disabled', function (): void {
    $settings = app(PopupSettings::class);
    $settings->enabled = false;
    $settings->stripeEnabled = true;
    $settings->stripeText = 'Hello banner';
    $settings->save();

    $html = view('components.popup-banner')->render();

    expect($html)->toBe('');
});

it('renders nothing when both stripe and popup are disabled', function (): void {
    $settings = app(PopupSettings::class);
    $settings->enabled = true;
    $settings->stripeEnabled = false;
    $settings->popupEnabled = false;
    $settings->save();

    $html = view('components.popup-banner')->render();

    expect($html)->toBe('');
});

it('renders stripe text when stripe is enabled', function (): void {
    $settings = app(PopupSettings::class);
    $settings->enabled = true;
    $settings->stripeEnabled = true;
    $settings->stripeText = 'Přihlášky jsou otevřeny!';
    $settings->save();

    $html = view('components.popup-banner')->render();

    expect($html)->toContain('Přihlášky jsou otevřeny!');
});

it('renders stripe CTA link when provided', function (): void {
    $settings = app(PopupSettings::class);
    $settings->enabled = true;
    $settings->stripeEnabled = true;
    $settings->stripeText = 'Přihlášky';
    $settings->stripeCta = [
        'text' => 'Zjistit více',
        'link' => ['type' => 'external', 'url' => 'https://example.com'],
    ];
    $settings->save();

    $html = view('components.popup-banner')->render();

    expect($html)
        ->toContain('Zjistit více')
        ->toContain('https://example.com')
        ->toContain('_blank');
});

it('renders popup heading and content when popup is enabled', function (): void {
    $settings = app(PopupSettings::class);
    $settings->enabled = true;
    $settings->popupEnabled = true;
    $settings->popupHeading = 'Vítejte!';
    $settings->popupContent = '<p>Obsah modálního okna.</p>';
    $settings->save();

    $html = view('components.popup-banner')->render();

    expect($html)
        ->toContain('Vítejte!')
        ->toContain('Obsah modálního okna.');
});

it('does not render stripe markup when only popup is enabled', function (): void {
    $settings = app(PopupSettings::class);
    $settings->enabled = true;
    $settings->stripeEnabled = false;
    $settings->popupEnabled = true;
    $settings->popupHeading = 'Popup only';
    $settings->save();

    $html = view('components.popup-banner')->render();

    expect($html)
        ->not->toContain('Zavřít oznámení')
        ->toContain('Popup only');
});

it('does not render popup markup when only stripe is enabled', function (): void {
    $settings = app(PopupSettings::class);
    $settings->enabled = true;
    $settings->stripeEnabled = true;
    $settings->stripeText = 'Stripe only';
    $settings->popupEnabled = false;
    $settings->save();

    $html = view('components.popup-banner')->render();

    expect($html)
        ->toContain('Stripe only')
        ->not->toContain('scale-95');
});

it('passes the dismissal key to alpine init', function (): void {
    $settings = app(PopupSettings::class);
    $settings->enabled = true;
    $settings->stripeEnabled = true;
    $settings->stripeText = 'Test';
    $settings->save();

    $html = view('components.popup-banner')->render();

    expect($html)->toContain('dcpp_popup_dismissed');
});
