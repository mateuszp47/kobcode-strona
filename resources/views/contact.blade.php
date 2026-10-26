@extends('layout')
@section('title', 'Kontakt — KOBCODE')
@section('content')
<section class="wrap section page"><span class="eyebrow">KONTAKT</span><h1>Twój projekt<br><span>zaczyna się tutaj.</span></h1><p class="lead">Potrzebujesz strony internetowej lub aplikacji? Napisz, czym zajmuje się Twoja firma i co chcesz stworzyć.</p>
<div class="contact-layout"><aside class="contact-aside"><span class="eyebrow">NA POCZĄTEK WYSTARCZY</span><h2>Kilka słów<br>o Twojej firmie.</h2><ol><li>Co oferujesz i kto jest Twoim odbiorcą?</li><li>Co ma ułatwić strona lub aplikacja?</li><li>Co jest dla Ciebie najważniejsze na start?</li></ol><p>Nie potrzebujesz gotowej specyfikacji. Ustalimy szczegóły podczas rozmowy.</p>
@if(config('site.email'))<a class="text-link" href="mailto:{{ config('site.email') }}">{{ config('site.email') }} ↗</a>@endif
@if(config('site.phone'))<a class="text-link" href="tel:{{ preg_replace('/[^+0-9]/', '', config('site.phone')) }}">{{ config('site.phone') }}</a>@endif
</aside><div class="contact-form-area">
@if(session('success'))<p role="status" class="notice">{{ session('success') }}</p>@endif
@if(config('site.contact_enabled'))
<form class="contact-form" method="post" action="{{ route('contact.store') }}">
@csrf
@if($errors->any())<div role="alert" class="form-error"><p>Popraw dane w formularzu i wyślij go ponownie.</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@foreach(['name'=>'Imię','email'=>'Adres e-mail','company'=>'Firma (opcjonalnie)','phone'=>'Telefon (opcjonalnie)'] as $field=>$label)
<div><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" type="{{ $field === 'email' ? 'email' : ($field === 'phone' ? 'tel' : 'text') }}" autocomplete="{{ ['name'=>'given-name','email'=>'email','company'=>'organization','phone'=>'tel'][$field] }}" value="{{ old($field) }}" maxlength="{{ ['name'=>100,'email'=>255,'company'=>150,'phone'=>40][$field] }}" @required(in_array($field, ['name','email'])) @if($errors->has($field)) aria-invalid="true" aria-describedby="{{ $field }}-error" @endif>@error($field)<p class="form-error" id="{{ $field }}-error">{{ $message }}</p>@enderror</div>@endforeach
<div class="full"><label for="message">Opisz swój projekt</label><textarea id="message" name="message" rows="6" required minlength="10" maxlength="5000" @if($errors->has('message')) aria-invalid="true" aria-describedby="message-error" @endif>{{ old('message') }}</textarea>@error('message')<p class="form-error" id="message-error">{{ $message }}</p>@enderror</div>
<div class="honeypot" aria-hidden="true"><label for="website">Zostaw puste</label><input id="website" name="website" tabindex="-1" autocomplete="off"></div>
<p class="full">Dane wykorzystamy do obsługi zapytania. <a class="text-link" href="{{ route('privacy') }}">Informacje o prywatności</a>.</p><button class="button" type="submit">Wyślij zapytanie ↗</button>
</form>
@endif

@if(!config('site.contact_enabled'))<div class="contact-unavailable"><span class="eyebrow">KONTAKT</span><h2>Formularz jest<br>obecnie niedostępny.</h2>@if(config('site.email'))<p>Napisz bezpośrednio na podany adres e-mail.</p>@else<p>Dane kontaktowe pojawią się po uruchomieniu tej części serwisu.</p>@endif</div>@endif
</div></div></section>
@endsection