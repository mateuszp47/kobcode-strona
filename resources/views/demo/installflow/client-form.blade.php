@extends('demo.installflow.layout')
@section('content')
<div class="if-page-heading"><div><a class="if-text-link" href="{{ route('installflow.clients') }}">Wszyscy klienci</a><h1>{{ $title }}</h1><p>Używaj fikcyjnych danych. Wpis będzie widoczny wyłącznie w tej sesji.</p></div></div>
<form class="if-panel if-form-panel" method="post" action="{{ $client ? route('installflow.clients.update',$client->id) : route('installflow.clients.store') }}">@csrf @if($client)@method('PUT')@endif<div class="if-form-grid">
@include('demo.installflow.partials.field',['name'=>'name','label'=>'Imię i nazwisko / nazwa firmy','value'=>$client?->name,'required'=>true,'full'=>true])
@include('demo.installflow.partials.field',['name'=>'type','label'=>'Rodzaj klienta','value'=>$client?->type ?? 'person','options'=>['person'=>'Osoba prywatna','company'=>'Firma'],'required'=>true])
@include('demo.installflow.partials.field',['name'=>'city','label'=>'Miasto','value'=>$client?->city,'required'=>true])
@include('demo.installflow.partials.field',['name'=>'email','label'=>'E-mail','type'=>'email','value'=>$client?->email])
@include('demo.installflow.partials.field',['name'=>'phone','label'=>'Telefon','type'=>'tel','value'=>$client?->phone])
@include('demo.installflow.partials.field',['name'=>'address','label'=>'Adres montażu','value'=>$client?->address,'full'=>true])
@include('demo.installflow.partials.field',['name'=>'notes','label'=>'Notatki o kliencie','type'=>'textarea','value'=>$client?->notes,'full'=>true])
</div><div class="if-form-actions"><a class="if-button if-secondary" href="{{ route('installflow.clients') }}">Anuluj</a><button class="if-button" type="submit">Zapisz klienta</button></div></form>
@endsection
