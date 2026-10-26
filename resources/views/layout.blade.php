<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'KOBCODE — strony internetowe i aplikacje dla firm')</title>
<meta name="description" content="@yield('description', 'Projektujemy i tworzymy strony internetowe oraz aplikacje dla firm z całej Polski. Poznaj realizacje KOBCODE i porozmawiajmy o Twoim projekcie.')">
<meta name="robots" content="{{ config('site.indexable') ? 'index,follow' : 'noindex,nofollow' }}">
<link rel="canonical" href="{{ url()->current() }}"><meta name="theme-color" content="#f4f2ed">
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="preload" href="{{ asset('fonts/manrope-latin.woff') }}" as="font" type="font/woff" crossorigin>
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<a class="skip" href="#main">Przejdź do treści</a>
<header class="site-header"><div class="wrap header-inner">
<a class="site-logo" href="{{ route('home') }}" aria-label="KOBCODE — strona główna">kobcode<span class="site-logo-registered" aria-hidden="true">®</span></a>
<button class="menu" type="button" aria-expanded="false" aria-controls="nav">Menu <span aria-hidden="true">☰</span></button>
<nav id="nav" aria-label="Główna nawigacja"><a href="{{ route('portfolio') }}">Realizacje</a><a href="{{ route('home') }}#uslugi">Usługi</a><a href="{{ route('about') }}">O firmie</a><a href="{{ route('home') }}#wspolpraca">Współpraca</a><a class="nav-cta" href="{{ route('contact') }}">Porozmawiajmy <span aria-hidden="true">↗</span></a></nav>
</div></header>
<main id="main">@yield('content')</main>
<footer class="site-footer"><div class="wrap">
<div class="footer-top"><a class="site-logo" href="{{ route('home') }}" aria-label="KOBCODE — strona główna">kobcode<span class="site-logo-registered" aria-hidden="true">®</span></a><p>Projektujemy strony.<br>Tworzymy oprogramowanie.</p><div class="footer-links"><a href="{{ route('portfolio') }}">Realizacje</a><a href="{{ route('contact') }}">Kontakt ↗</a><a href="{{ route('privacy') }}">Prywatność</a></div></div>
<div class="footer-bottom"><p>KOBCODE sp. z o.o.<br>ul. Żurawia 11, 26-600 Radom</p><p>NIP 9482601374<br>KRS 0000512381 · REGON 147266793</p><span>© {{ date('Y') }} KOBCODE</span><a href="#main">Wróć na górę ↑</a></div>
</div></footer>
</body></html>
