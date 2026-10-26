<!doctype html>
<html lang="pl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'KOBCODE — strony i aplikacje internetowe dla firm')</title>
<meta name="description" content="@yield('description', 'KOBCODE. Tworzymy strony internetowe oraz aplikacje dopasowane do potrzeb firm z całej Polski.')">
<meta name="robots" content="{{ config('site.indexable') ? 'index,follow' : 'noindex,nofollow' }}">
<link rel="canonical" href="{{ url()->current() }}"><meta name="theme-color" content="#f6f6f0">
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">@vite(['resources/css/app.css','resources/js/app.js'])
</head><body><a class="skip" href="#main">Przejdź do treści</a>
<header class="wrap header"><a class="site-logo" href="{{ route('home') }}" aria-label="KOBCODE — strona główna"><span class="site-logo-word">kobcode</span><span class="site-logo-registered" aria-hidden="true">®</span></a>
<button class="menu" aria-expanded="false" aria-controls="nav">Menu <span aria-hidden="true">☰</span></button>
<nav id="nav" aria-label="Główna nawigacja"><a href="{{ route('home') }}#uslugi">Usługi</a><a href="{{ route('portfolio') }}">Realizacje</a><a href="{{ route('about') }}">O nas</a><a href="{{ route('home') }}#wspolpraca">Współpraca</a><a class="nav-cta" href="{{ route('contact') }}">Porozmawiajmy <span>↗</span></a></nav></header>
<main id="main">@yield('content')</main>
<footer class="wrap footer"><a class="site-logo" href="{{ route('home') }}"><span class="site-logo-word">kobcode</span><span class="site-logo-registered" aria-hidden="true">®</span></a><p>KOBCODE sp. z o.o.<br>ul. Żurawia 11, 26-600 Radom</p><p>NIP 9482601374<br>KRS 0000512381 · REGON 147266793</p><a href="{{ route('privacy') }}">Prywatność</a><span>© {{ date('Y') }} KOBCODE</span></footer>
</body></html>