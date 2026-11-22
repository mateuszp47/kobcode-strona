@extends('demo.installflow.layout')
@section('content')
@php
    $clientOptions = [''=>'Wybierz klienta'] + $clients->pluck('name','id')->all();
    $formItems = old('items', $items->map(fn($item) => ['description'=>$item->description,'quantity'=>number_format($item->quantity_hundredths/100,2,'.',''),'unit_price'=>number_format($item->unit_cents/100,2,'.','')])->all());
    if (!$formItems) $formItems = [['description'=>'','quantity'=>'1','unit_price'=>'0']];
@endphp
<div class="if-page-heading"><div><a class="if-text-link" href="{{ route('installflow.offers') }}">Wszystkie oferty</a><h1>{{ $title }}</h1><p>Kwoty pozycji są netto. Podsumowanie uwzględnia demonstracyjną stawkę VAT 23%.</p></div></div>
<form class="if-panel if-form-panel" method="post" action="{{ $offer ? route('installflow.offers.update',$offer->id) : route('installflow.offers.store') }}" data-offer-form>@csrf @if($offer)@method('PUT')@endif<div class="if-form-grid">
@include('demo.installflow.partials.field',['name'=>'title','label'=>'Nazwa oferty','value'=>$offer?->title,'required'=>true,'full'=>true])
@include('demo.installflow.partials.field',['name'=>'client_id','label'=>'Klient','value'=>$offer?->client_id ?? request('client'),'options'=>$clientOptions,'required'=>true])
@include('demo.installflow.partials.field',['name'=>'status','label'=>'Status','value'=>$offer?->status ?? 'draft','options'=>$offerStatuses,'required'=>true])
@include('demo.installflow.partials.field',['name'=>'valid_until','label'=>'Termin ważności','type'=>'date','value'=>$offer?->valid_until ?? now()->addDays(14)->format('Y-m-d'),'required'=>true])
</div><div class="if-section-title"><h2>Pozycje oferty</h2><button class="if-button if-secondary if-small" type="button" data-add-item>Dodaj pozycję</button></div>
<div class="if-offer-items" data-offer-items>@foreach($formItems as $index=>$item)<div class="if-offer-row" data-offer-row><label>Opis pozycji<input name="items[{{ $index }}][description]" value="{{ $item['description'] }}" required maxlength="200"></label><label>Ilość<input name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}" type="number" min="0.01" max="999.99" step="0.01" required data-quantity></label><label>Cena netto (zł)<input name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] }}" type="number" min="0" max="9999999.99" step="0.01" required data-price></label><div class="if-line-total"><span>Wartość netto</span><output data-line-total>—</output></div><button class="if-remove-item" type="button" aria-label="Usuń pozycję" data-remove-item>×</button></div>@endforeach</div>
<template id="if-item-template"><div class="if-offer-row" data-offer-row><label>Opis pozycji<input name="items[__INDEX__][description]" required maxlength="200"></label><label>Ilość<input name="items[__INDEX__][quantity]" value="1" type="number" min="0.01" max="999.99" step="0.01" required data-quantity></label><label>Cena netto (zł)<input name="items[__INDEX__][unit_price]" value="0" type="number" min="0" max="9999999.99" step="0.01" required data-price></label><div class="if-line-total"><span>Wartość netto</span><output data-line-total>—</output></div><button class="if-remove-item" type="button" aria-label="Usuń pozycję" data-remove-item>×</button></div></template>
<div class="if-offer-summary" aria-live="polite"><div><span>Netto</span><output data-total-net>—</output></div><div><span>VAT 23%</span><output data-total-vat>—</output></div><div><strong>Razem brutto</strong><output data-total-gross>—</output></div></div>
@include('demo.installflow.partials.field',['name'=>'notes','label'=>'Dodatkowe ustalenia','type'=>'textarea','value'=>$offer?->notes,'full'=>true])
<div class="if-form-actions"><a class="if-button if-secondary" href="{{ route('installflow.offers') }}">Anuluj</a><button class="if-button" type="submit">Zapisz ofertę</button></div></form>
@endsection
