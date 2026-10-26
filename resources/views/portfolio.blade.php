@extends('layout')
@section('title', 'Realizacje — KOBCODE')
@section('content')
<section class="wrap section page"><span class="eyebrow">PORTFOLIO / STRONY INTERNETOWE</span><h1>Nasza praca.<br><span>W konkretnych widokach.</span></h1>
<p class="lead">Serwisy stworzone od podstaw dla różnych potrzeb biznesowych. Zobacz, jak prezentujemy ofertę, produkty i realizacje.</p>
<section class="case-preview" id="walbud"><div class="case-heading"><div><span class="eyebrow">PROJEKT DLA KLIENTA · OD PODSTAW</span><h2>Walbud / Walbud24</h2></div><p>Serwis firmowy z katalogiem produktów. Pierwszy ekran przedstawia rodzaje oferty i prowadzi do jej poznania oraz kontaktu.</p></div>
@include('partials.project-screenshot', ['image'=>'walbud-home','alt'=>'Walbud24 — pierwszy ekran z opisem oferty i kategoriami produktów','caption'=>'Walbud / Walbud24 · strona główna'])
</section>
<section class="case-preview" id="olaya"><div class="case-heading"><div><span class="eyebrow">FIRMA POWIĄZANA · OD PODSTAW</span><h2>Olaya — serwis producenta</h2></div><p>Strona producenta saun ogrodowych. Prezentacja marki, katalog modeli oraz galeria realizacji w trzech przesłanych widokach.</p></div>
@include('partials.project-screenshot', ['image'=>'olaya-home','alt'=>'Olaya — strona główna z prezentacją producenta i zdjęciem sauny','caption'=>'Olaya · strona główna'])
<div class="case-gallery"><figure>@include('partials.project-screenshot', ['image'=>'olaya-catalog','alt'=>'Olaya — katalog modeli saun z kartami produktów i cenami','caption'=>'Olaya · oferta','height'=>866])<figcaption>Oferta — karty modeli, zdjęcia i najważniejsze informacje o produktach.</figcaption></figure><figure>@include('partials.project-screenshot', ['image'=>'olaya-projects','alt'=>'Olaya — galeria realizacji z wyborem produktu i miasta','caption'=>'Olaya · realizacje','height'=>861])<figcaption>Realizacje — prezentacja zdjęć i uporządkowanie ich według produktów oraz miast.</figcaption></figure></div>
</section>
@if($projects->isNotEmpty())<section class="case-preview"><span class="eyebrow">POZNAJ SZCZEGÓŁY</span><h2>Opisy projektów</h2><div class="project-grid">@foreach($projects as $project)<a class="project-card" href="{{ route('project', $project->slug) }}">@if($project->cover)<img src="{{ Storage::disk('public')->url($project->cover) }}" alt="{{ $project->cover_alt }}" loading="lazy" width="900" height="600">@endif<span class="eyebrow">{{ $project->category }}</span><h3>{{ $project->title }} ↗</h3><p>{{ $project->summary }}</p></a>@endforeach</div></section>@endif
</section>@include('partials.contact-banner')
@endsection
