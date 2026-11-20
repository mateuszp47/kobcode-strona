@extends('layout')
@php
    $service = config('offer.'.$serviceKey);
@endphp
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
    <a class="text-link" href="{{ route('contact') }}">Zapytaj o wycenę <span aria-hidden="true">@include('partials.arrow-up-right')</span></a>

    <div class="steps light">
        @foreach($service['scope'] as $heading => $paragraph)
            <article @if($serviceKey === 'apps' && $loop->first) id="panele-administracyjne" @endif>
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
            @php
                $slug = ['olaya' => 'olaya-strona', 'walbud' => 'walbud24'][$anchor] ?? $anchor;
                if ($slug === 'cermax' && ! $caseStudies->contains($slug) && $caseStudies->contains('b2b-cermax')) $slug = 'b2b-cermax';
                $caseUrl = $caseStudies->contains($slug) ? route('project', $slug) : route('portfolio').'#'.$anchor;
            @endphp
            <p><a class="text-link" href="{{ $caseUrl }}">{{ $label }} @include('partials.arrow-up-right')</a></p>
        @endforeach
    </section>
    @if($serviceKey === 'apps' && \App\Demo\InstallFlow\DemoAccess::enabled())
        <section class="prose"><h2>Zobacz działającą aplikację</h2><p>W InstallFlow możesz utworzyć ofertę, zaplanować montaż i sprawdzić pracę zespołu na fikcyjnych danych.</p><a class="text-link" href="{{ route('demo.installflow') }}">Poznaj demo InstallFlow</a></section>
    @endif
    <nav class="prose" aria-label="Powiązane usługi">
        <h2>Powiązane usługi</h2>
        @foreach($service['related'] as $name)
            <p @if($serviceKey === 'apps' && $name === 'automation') id="automatyzacje" @endif>
                <a class="text-link" @if($serviceKey === 'apps' && $name === 'automation') id="integracje" @endif
                   href="{{ route($name) }}">{{ config('offer.'.$name.'.name') }} @include('partials.arrow-up-right')</a>
            </p>
        @endforeach
    </nav>
</section>
@include('partials.contact-banner')
@endsection
