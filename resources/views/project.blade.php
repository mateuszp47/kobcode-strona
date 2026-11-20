@extends('layout')
@section('title', $project->seo_title ?: $project->title.' — KOBCODE')
@section('description', $project->seo_description ?: $project->summary)
@section('content')
<article class="wrap section page case-study">
    <nav class="case-breadcrumbs" aria-label="Ścieżka nawigacji"><a href="{{ route('home') }}">Strona główna</a><span aria-hidden="true"> / </span><a href="{{ route('portfolio') }}">Realizacje</a><span aria-hidden="true"> / </span><span aria-current="page">{{ $project->title }}</span></nav>
    <h1>{{ $project->title }}</h1>
    <p class="lead">{{ $project->summary }}</p>
    <p>{{ $project->category }} · {{ ['portfolio' => 'Realizacja KOBCODE', 'own' => 'Projekt własny', 'client' => 'Projekt dla klienta', 'related' => 'Projekt dla firmy powiązanej'][$project->relationship] }} · {{ ['deployed' => 'Wdrożony', 'in_development' => 'W rozwoju', 'unverified' => 'Status wdrożenia do potwierdzenia'][$project->delivery_status] ?? 'Status wdrożenia do potwierdzenia' }}</p>
    @if($project->cover)
        @php($dimensions = \App\Support\ProjectImage::dimensions($project->cover))
        <img class="project-cover" src="{{ \App\Support\ProjectImage::url($project->cover) }}" alt="{{ $project->cover_alt ?: $project->title }}" @if($srcset = \App\Support\ProjectImage::srcset($project->cover)) srcset="{{ $srcset }}" sizes="(max-width: 760px) 88vw, 1280px" @endif @if($dimensions) width="{{ $dimensions['width'] }}" height="{{ $dimensions['height'] }}" @endif fetchpriority="high" decoding="async">
    @endif
    @if(in_array($project->slug, ['cermax', 'b2b-cermax', 'olaya-aplikacja'], true))
        @include('partials.demo-notice')
    @endif
    <div class="prose">
        @foreach(['goal'=>'Cel projektu','problem'=>'Potrzeba biznesowa','scope'=>'Zakres naszej pracy','solution'=>'Jak działa rozwiązanie','result'=>'Rezultat'] as $field=>$label)
            @if($project->$field)<section><h2>{{ $label }}</h2>{!! \App\Support\ProjectContent::html($project->$field) !!}</section>@endif
        @endforeach
        @if($project->technologies)
            <section><h2>Zastosowane technologie</h2><div class="tags">@foreach($project->technologies as $technology)<span>{{ $technology }}</span>@endforeach</div></section>
        @endif
    </div>
    @if($project->gallery)
        <section aria-labelledby="gallery-heading"><div class="prose"><h2 id="gallery-heading">Widoki realizacji</h2></div>
            <div class="case-gallery">
                @foreach($project->gallery as $image)
                    @if(!empty($image['path']))
                        @php($dimensions = \App\Support\ProjectImage::dimensions($image['path']))
                        <figure><div class="screenshot-frame"><img src="{{ \App\Support\ProjectImage::url($image['path']) }}" alt="{{ $image['alt'] ?? $project->title }}" @if($srcset = \App\Support\ProjectImage::srcset($image['path'])) srcset="{{ $srcset }}" sizes="(max-width: 760px) 88vw, 640px" @endif @if($dimensions) width="{{ $dimensions['width'] }}" height="{{ $dimensions['height'] }}" @endif loading="lazy" decoding="async"></div>@if(!empty($image['caption']))<figcaption>{{ $image['caption'] }}</figcaption>@endif</figure>
                    @endif
                @endforeach
            </div>
        </section>
    @endif
    @php($services = collect(config('offer'))->only($project->service_links ?? []))
    @if($services->isNotEmpty())
        <section class="prose"><h2>Rozwiązania dla Twojej firmy</h2><ul>@foreach($services as $name=>$service)<li><a class="text-link" href="{{ route($name) }}">{{ $service['name'] }} @include('partials.arrow-up-right')</a></li>@endforeach</ul></section>
    @endif
    <p><a class="button" href="{{ route('contact') }}">Porozmawiajmy o podobnym rozwiązaniu @include('partials.arrow-up-right')</a></p>
    <p><a class="text-link" href="{{ route('portfolio') }}">← Wszystkie realizacje</a></p>
</article>
@endsection
