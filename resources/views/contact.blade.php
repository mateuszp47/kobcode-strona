@extends('layout')
@section('title', 'Kontakt — KOBCODE')
@section('content')<section class="wrap section page"><span class="eyebrow">KONTAKT</span><h1>Opowiedz nam,<br>co planujesz.</h1><p class="lead">Napisz, czym zajmuje się Twoja firma, co chcesz stworzyć i jaki problem rozwiązać. Nie potrzebujesz gotowej specyfikacji.</p>
@if(config('site.email'))<a class="button" href="mailto:{{ config('site.email') }}">{{ config('site.email') }} ↗</a>@elseif(!config('site.contact_enabled'))<p class="notice">Bezpośredni kontakt internetowy jest w przygotowaniu.</p>@endif
@if(config('site.phone'))<p><a href="tel:{{ preg_replace('/[^+0-9]/', '', config('site.phone')) }}">{{ config('site.phone') }}</a></p>@endif

@if(session('success'))<p role="status" class="notice">{{ session('success') }}</p>@endif
@if(config('site.contact_enabled'))
<form class="contact-form" method="post" action="{{ route('contact.store') }}">
@csrf
@if($errors->any())<div role="alert" class="form-error"><p>Sprawdź zaznaczone pola.</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@foreach(['name'=>'Imię','email'=>'Adres e-mail','company'=>'Firma (opcjonalnie)','phone'=>'Telefon (opcjonalnie)'] as $field=>$label)
<div><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" type="{{ $field === 'email' ? 'email' : ($field === 'phone' ? 'tel' : 'text') }}" autocomplete="{{ ['name'=>'given-name','email'=>'email','company'=>'organization','phone'=>'tel'][$field] }}" value="{{ old($field) }}" maxlength="{{ ['name'=>100,'email'=>255,'company'=>150,'phone'=>40][$field] }}" @required(in_array($field, ['name','email'])) @if($errors->has($field)) aria-invalid="true" aria-describedby="{{ $field }}-error" @endif>@error($field)<p class="form-error" id="{{ $field }}-error">{{ $message }}</p>@enderror</div>@endforeach
<div class="full"><label for="message">Co chcesz stworzyć?</label><textarea id="message" name="message" rows="6" required minlength="10" maxlength="5000" @if($errors->has('message')) aria-invalid="true" aria-describedby="message-error" @endif>{{ old('message') }}</textarea>@error('message')<p class="form-error" id="message-error">{{ $message }}</p>@enderror</div>
<div class="honeypot" aria-hidden="true"><label for="website">Zostaw puste</label><input id="website" name="website" tabindex="-1" autocomplete="off"></div>
<p class="full">Dane wykorzystamy do obsługi zapytania. <a class="text-link" href="{{ route('privacy') }}">Informacje o prywatności</a>.</p><button class="button" type="submit">Wyślij zapytanie ↗</button>
</form>
@endif
<div class="prose"><h2>KOBCODE sp. z o.o.</h2><p>Współpracujemy z firmami z całej Polski. Rozmowę o projekcie możemy rozpocząć zdalnie.</p><p>NIP 9482601374<br>KRS 0000512381<br>REGON 147266793</p></div></section>@endsection