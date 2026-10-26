@extends('layout')
@section('title', 'Realizacje — KOBCODE')
@section('content')<section class="wrap section page"><span class="eyebrow">PORTFOLIO</span><h1>Cel. Rozwiązanie.<br>Efekt pracy.</h1>
@if($projects->isEmpty())<p class="lead">Przygotowujemy opisy realizacji wraz z prezentacją zakresu prac i zastosowanych rozwiązań.</p><p>Materiały projektowe pojawią się po zakończeniu ich weryfikacji i zatwierdzeniu do publikacji.</p>
@else<div class="project-grid">@foreach($projects as $project)<a class="project-card" href="{{ route('project', $project->slug) }}">@if($project->cover)<img src="{{ Storage::disk('public')->url($project->cover) }}" alt="{{ $project->cover_alt }}" loading="lazy" width="900" height="600">@endif<span class="eyebrow">{{ $project->category }}</span><h2>{{ $project->title }} ↗</h2><p>{{ $project->summary }}</p></a>@endforeach</div>@endif
<a class="button" href="{{ route('contact') }}">Porozmawiajmy o Twoim projekcie ↗</a></section>@endsection