@extends('layouts.app', ['title' => $page->title])

@section('content')
    @foreach ($sections as $section)
        {!! $section !!}
    @endforeach
@endsection
