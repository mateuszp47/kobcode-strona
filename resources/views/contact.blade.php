@extends('layout')
@section('title', 'Kontakt — KOBCODE Radom')
@section('content')<section class="wrap section page"><span class="eyebrow">KONTAKT</span><h1>Opowiedz nam,<br>co planujesz.</h1><p class="lead">Napisz, czym zajmuje się Twoja firma, co chcesz stworzyć i jaki problem rozwiązać. Nie potrzebujesz gotowej specyfikacji.</p>
@if(config('site.email'))<a class="button" href="mailto:{{ config('site.email') }}">{{ config('site.email') }} ↗</a>@else<p class="notice">Bezpośredni kontakt internetowy jest w przygotowaniu.</p>@endif
@if(config('site.phone'))<p><a href="tel:{{ preg_replace('/[^+0-9]/', '', config('site.phone')) }}">{{ config('site.phone') }}</a></p>@endif
<div class="prose"><h2>KOBCODE sp. z o.o.</h2><p>ul. Żurawia 11<br>26-600 Radom</p><p>NIP 9482601374<br>KRS 0000512381<br>REGON 147266793</p></div></section>@endsection