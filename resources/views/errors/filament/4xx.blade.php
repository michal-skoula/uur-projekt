@php
    use Illuminate\Support\Facades\Lang;

    $code = $exception->getStatusCode();
    $tier = $code < 500 ? '4xx' : '5xx';
    $title = Lang::has("errors.codes.{$code}.title")
        ? __("errors.codes.{$code}.title")
        : __("errors.codes.{$tier}.title");
    $message = Lang::has("errors.codes.{$code}.message")
        ? __("errors.codes.{$code}.message")
        : __("errors.codes.{$tier}.message");
@endphp

@push('styles')
    <style>
        .fi-error-page {
            text-align: center;
        }

        .fi-error-page-code {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1;
            color: var(--primary-500);
        }

        .fi-error-page-title {
            margin-top: 0.75rem;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .fi-error-page-message {
            margin: 0.5rem 0 1.5rem;
            color: var(--gray-500);
        }
    </style>
@endpush

<x-filament-panels::layout.simple>
    <div class="fi-error-page">
        <p class="fi-error-page-code">{{ $code }}</p>
        <h1 class="fi-error-page-title">{{ $title }}</h1>
        <p class="fi-error-page-message">{{ $message }}</p>

        <x-filament::button tag="a" :href="\Filament\Facades\Filament::getUrl()">
            {{ __('errors.back_dashboard') }}
        </x-filament::button>
    </div>
</x-filament-panels::layout.simple>
