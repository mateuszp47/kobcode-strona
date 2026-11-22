<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
@include('partials.seo')
<meta name="theme-color" content="#f4f2ed">
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="preload" href="{{ asset('fonts/manrope-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<a class="skip" href="#main">Przejdź do treści</a>
<header class="site-header"><div class="wrap header-inner">
<a class="site-logo" href="{{ route('home') }}" aria-label="KOBCODE — strona główna">kobcode<span class="site-logo-registered" aria-hidden="true">®</span></a>
<button class="menu" type="button" aria-expanded="false" aria-controls="nav">Menu <span aria-hidden="true">☰</span></button>
<nav id="nav" aria-label="Główna nawigacja"><a href="{{ route('portfolio') }}">Realizacje</a><a href="{{ route('home') }}#uslugi">Usługi</a><a href="{{ route('about') }}">O firmie</a>@if(\App\Demo\InstallFlow\DemoAccess::enabled())<a href="{{ route('demo.index') }}">Demo aplikacji</a>@endif<a href="{{ route('home') }}#wspolpraca">Współpraca</a><a class="nav-cta" href="{{ route('contact') }}">Porozmawiajmy <span aria-hidden="true">@include('partials.arrow-up-right')</span></a></nav>
</div></header>
<main id="main">@yield('content')</main>
<footer class="site-footer"><div class="wrap">
<div class="footer-top"><a class="site-logo" href="{{ route('home') }}" aria-label="KOBCODE — strona główna">kobcode<span class="site-logo-registered" aria-hidden="true">®</span></a><p>Projektujemy i tworzymy<br>oprogramowanie na zamówienie.</p><div class="footer-links">@foreach(config('offer') as $name => $offer)<a href="{{ route($name) }}">{{ $offer['name'] }}</a>@endforeach<a href="{{ route('portfolio') }}">Realizacje</a>@if(\App\Demo\InstallFlow\DemoAccess::enabled())<a href="{{ route('demo.index') }}">Demo aplikacji</a>@endif<a href="{{ route('contact') }}">Kontakt @include('partials.arrow-up-right')</a><a href="{{ route('privacy') }}">Prywatność</a></div></div>
<div class="footer-bottom" id="dane-spolki"><p>KOBCODE sp. z o.o.<br>ul. Żurawia 11, 26-600 Radom@if(config('site.phone'))<br><a href="tel:{{ preg_replace('/[^+0-9]/', '', config('site.phone')) }}">{{ config('site.phone') }}</a>@endif</p><p>NIP 9482601374<br>KRS 0000512381 · REGON 147266793</p><span>© {{ date('Y') }} KOBCODE</span><a href="#main">Wróć na górę ↑</a></div>
</div></footer>
</body></html>
