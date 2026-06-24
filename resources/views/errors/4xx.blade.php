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

@extends('layouts.website')

@section('content')
    <section class="section bg-primary-darkest flex min-h-[60vh] items-center">
        <div class="max-content-width w-full text-center">
            <p class="text-gradient-secondary mb-4 font-serif text-4xl md:text-5xl">{{ $code }}</p>
            <h1 class="mb-4">{{ $title }}</h1>
            <p class="mx-auto mb-8 max-w-xl text-gray">{{ $message }}</p>
            <a href="{{ url('/') }}" class="btn-primary">{{ __('errors.back_home') }}</a>
        </div>
    </section>
@endsection
