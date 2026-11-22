@extends('demo.installflow.layout')
@section('content')
@php
    $clientOptions = [''=>'Wybierz klienta'] + $clients->pluck('name','id')->all();
    $employeeOptions = [''=>'Nie przypisano'] + $employees->pluck('name','id')->all();
@endphp
<div class="if-page-heading"><div><a class="if-text-link" href="{{ route('installflow.orders') }}">Wszystkie zlecenia</a><h1>{{ $title }}</h1><p>Przypisz osobę, ustaw termin i zapisz status prac.</p></div></div>
<form class="if-panel if-form-panel" method="post" action="{{ $order ? route('installflow.orders.update',$order->id) : route('installflow.orders.store') }}">@csrf @if($order)@method('PUT')@endif
@if($order?->offer_id)<p class="if-note">Zlecenie pochodzi z zaakceptowanej oferty. Klient i wartość pozostają zgodne z dokumentem źródłowym.</p>@endif
<div class="if-form-grid">
@include('demo.installflow.partials.field',['name'=>'title','label'=>'Nazwa zlecenia','value'=>$order?->title,'required'=>true,'full'=>true])
@include('demo.installflow.partials.field',['name'=>'client_id','label'=>'Klient','value'=>$order?->client_id,'options'=>$clientOptions,'required'=>true])
@include('demo.installflow.partials.field',['name'=>'employee_id','label'=>'Pracownik','value'=>$order?->employee_id,'options'=>$employeeOptions])
@include('demo.installflow.partials.field',['name'=>'status','label'=>'Status realizacji','value'=>$order?->status ?? 'new','options'=>$orderStatuses,'required'=>true])
@include('demo.installflow.partials.field',['name'=>'scheduled_at','label'=>'Planowany termin','type'=>'datetime-local','value'=>$order?->scheduled_at ? \Carbon\Carbon::parse($order->scheduled_at)->format('Y-m-d\TH:i') : request('date')])
@include('demo.installflow.partials.field',['name'=>'gross','label'=>'Wartość brutto (zł)','type'=>'number','step'=>'0.01','value'=>$order ? number_format($order->gross_cents/100,2,'.','') : '0','required'=>true])
@include('demo.installflow.partials.field',['name'=>'notes','label'=>'Notatki dla zespołu','type'=>'textarea','value'=>$order?->notes,'full'=>true])
</div><div class="if-form-actions"><a class="if-button if-secondary" href="{{ route('installflow.orders') }}">Anuluj</a><button class="if-button" type="submit">Zapisz zlecenie</button></div></form>
@endsection
