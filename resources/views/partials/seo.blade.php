@php
    $title = html_entity_decode(trim(strip_tags($__env->yieldContent('title', 'Oprogramowanie na zamówienie dla firm — KOBCODE'))), ENT_QUOTES, 'UTF-8');
    $description = html_entity_decode(trim(strip_tags($__env->yieldContent('description', 'Projektujemy aplikacje webowe, automatyzacje, integracje i strony internetowe dla firm z całej Polski. Zobacz realizacje i porozmawiajmy o Twoim projekcie.'))), ENT_QUOTES, 'UTF-8');
    $canonical = \App\Support\Seo::url(request()->path());
    $shareImage = \App\Support\Seo::url('images/brand/digital-form-960.webp');
    $homeUrl = \App\Support\Seo::url();
    $organization = ['@type' => 'Organization', '@id' => $homeUrl.'#organization', 'name' => 'KOBCODE', 'legalName' => 'KOBCODE sp. z o.o.', 'url' => $homeUrl, 'vatID' => 'PL9482601374', 'areaServed' => ['@type' => 'Country', 'name' => 'Polska']];
    if (config('site.email')) $organization['email'] = config('site.email');
    if (config('site.phone')) $organization['telephone'] = config('site.phone');
    $graph = [$organization, ['@type' => 'WebSite', '@id' => $homeUrl.'#website', 'url' => $homeUrl, 'name' => 'KOBCODE', 'inLanguage' => 'pl-PL', 'publisher' => ['@id' => $homeUrl.'#organization']], ['@type' => 'WebPage', '@id' => $canonical.'#webpage', 'url' => $canonical, 'name' => $title, 'description' => $description, 'inLanguage' => 'pl-PL', 'isPartOf' => ['@id' => $homeUrl.'#website']]];
    if (request()->routeIs('web', 'apps')) {
        $graph[] = ['@type' => 'Service', '@id' => $canonical.'#service', 'name' => request()->routeIs('web') ? 'Tworzenie stron internetowych' : 'Aplikacje webowe, automatyzacje i integracje', 'url' => $canonical, 'description' => $description, 'provider' => ['@id' => $homeUrl.'#organization'], 'areaServed' => ['@type' => 'Country', 'name' => 'Polska']];
    }
    $schema = ['@context' => 'https://schema.org', '@graph' => $graph];
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="robots" content="{{ config('site.indexable') ? 'index,follow,max-image-preview:large' : 'noindex,nofollow' }}">
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="pl_PL">
<meta property="og:site_name" content="KOBCODE">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $shareImage }}">
<meta property="og:image:width" content="960"><meta property="og:image:height" content="960">
<meta property="og:image:alt" content="KOBCODE — projektowanie i tworzenie oprogramowania">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $shareImage }}">
<meta name="twitter:image:alt" content="KOBCODE — projektowanie i tworzenie oprogramowania">
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
