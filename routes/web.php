<?php
use Illuminate\Support\Facades\Route;
Route::view('/', 'home')->name('home');
Route::view('/uslugi/strony-internetowe', 'service', ['kind' => 'web'])->name('web');
Route::view('/uslugi/aplikacje-internetowe', 'service', ['kind' => 'app'])->name('apps');
Route::get('/realizacje', fn () => view('portfolio', ['projects' => \App\Models\Project::published()->orderBy('sort_order')->orderBy('id')->get()]))->name('portfolio');
Route::get('/realizacje/{slug}', function (string $slug) {
    return view('project', ['project' => \App\Models\Project::published()->where('slug', $slug)->firstOrFail()]);
})->name('project');
Route::view('/o-firmie', 'about')->name('about');
Route::view('/kontakt', 'contact')->name('contact');

Route::post('/kontakt', [\App\Http\Controllers\ContactController::class, 'store'])->middleware('throttle:5,10')->name('contact.store');
Route::view('/polityka-prywatnosci', 'privacy')->name('privacy');

Route::get('/robots.txt', [\App\Http\Controllers\SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [\App\Http\Controllers\SeoController::class, 'sitemap'])->name('sitemap');
