<?php
use Illuminate\Support\Facades\Route;
Route::view('/', 'home')->name('home');
Route::view('/uslugi/strony-internetowe', 'service', ['kind' => 'web'])->name('web');
Route::view('/uslugi/aplikacje-internetowe', 'service', ['kind' => 'app'])->name('apps');
Route::view('/realizacje', 'portfolio')->name('portfolio');
Route::view('/o-firmie', 'about')->name('about');
Route::view('/kontakt', 'contact')->name('contact');
