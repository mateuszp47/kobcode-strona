@extends('layout')
@php($service = config('offer.'.$serviceKey))
@section('title', $service['title'])
@section('description', $service['description'])
@section('content')
<section class="wrap section page">
    <nav class="service-crumbs" aria-label="Ścieżka nawigacji">
        <a href="{{ route('home') }}">Strona główna</a>
        <span aria-hidden="true"> / </span>
        <span aria-current="page">{{ $service['name'] }}</span>
    </nav>

    <h1>{{ $service['name'] }}</h1>
    <p class="lead">{{ $service['lead'] }}</p>
    <a class="text-link" href="{{ route('contact') }}">Zapytaj o wycenę <span aria-hidden="true">↗</span></a>

    <div class="steps light">
        @foreach($service['scope'] as $heading => $paragraph)
            <article>
                <h2>{{ $heading }}</h2>
                <p>{{ $paragraph }}</p>
            </article>
        @endforeach
    </div>

    <section class="service-brief">
        <h2>{{ $service['brief'] }}</h2>
        <p>{{ $service['answer'] }}</p>
        <div class="brief-columns">
            @foreach($service['questions'] as $question => $answer)
                <article>
                    <h3>{{ $question }}</h3>
                    <p>{{ $answer }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="prose">
        <h2>Przykłady z naszych realizacji</h2>
        @foreach($service['cases'] as $anchor => $label)
            <p><a class="text-link" href="{{ route('portfolio') }}#{{ $anchor }}">{{ $label }} ↗</a></p>
        @endforeach
    </section>
    <nav class="prose" aria-label="Powiązane usługi">
        <h2>Powiązane usługi</h2>
        @foreach($service['related'] as $name)
            <p @if($serviceKey === 'apps' && $name === 'automation') id="automatyzacje" @endif>
                <a class="text-link" @if($serviceKey === 'apps' && $name === 'automation') id="integracje" @endif
                   href="{{ route($name) }}">{{ config('offer.'.$name.'.name') }} ↗</a>
            </p>
        @endforeach
    </nav>
</section>
@include('partials.contact-banner')
@endsection
