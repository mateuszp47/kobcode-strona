@extends('demo.installflow.layout')
@section('content')
<div class="if-page-heading"><div><a class="if-text-link" href="{{ route('installflow.employees') }}">Cały zespół</a><h1>{{ $title }}</h1><p>Podaj fikcyjne dane pracownika. Nie tworzy to konta w panelu KOBCODE.</p></div></div>
<form class="if-panel if-form-panel" method="post" action="{{ $employee ? route('installflow.employees.update',$employee->id) : route('installflow.employees.store') }}">@csrf @if($employee)@method('PUT')@endif<div class="if-form-grid">
@include('demo.installflow.partials.field',['name'=>'name','label'=>'Imię i nazwisko','value'=>$employee?->name,'required'=>true])
@include('demo.installflow.partials.field',['name'=>'role','label'=>'Specjalizacja / stanowisko','value'=>$employee?->role,'required'=>true])
@include('demo.installflow.partials.field',['name'=>'email','label'=>'E-mail demonstracyjny','type'=>'email','value'=>$employee?->email,'required'=>true])
@include('demo.installflow.partials.field',['name'=>'phone','label'=>'Telefon demonstracyjny','type'=>'tel','value'=>$employee?->phone,'required'=>true])
@include('demo.installflow.partials.field',['name'=>'availability','label'=>'Dostępność','value'=>$employee?->availability ?? 'available','options'=>$availability,'required'=>true])
</div><div class="if-form-actions"><a class="if-button if-secondary" href="{{ route('installflow.employees') }}">Anuluj</a><button class="if-button" type="submit">Zapisz pracownika</button></div></form>
@endsection
