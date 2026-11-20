<?php

use App\Http\Controllers\Demo\InstallFlowController;
use App\Http\Middleware\Demo\{EnsureDemoEnabled, UseInstallFlowWorkspace};
use Illuminate\Support\Facades\Route;

Route::prefix('demo')->middleware(EnsureDemoEnabled::class)->group(function () {
    Route::view('/', 'demo.index')->name('demo.index');
    Route::view('/installflow/opis', 'demo.installflow-info')->name('demo.installflow');
    Route::prefix('installflow')->name('installflow.')->middleware(['throttle:180,1,installflow-read-', UseInstallFlowWorkspace::class])->group(function () {
        $controller = InstallFlowController::class;
        Route::get('/', [$controller, 'dashboard'])->name('dashboard')->block(10, 10);
        Route::post('/reset', [$controller, 'reset'])->middleware('throttle:5,1,installflow-reset-')->name('reset')->block(10, 10);
        Route::get('/raporty', [$controller, 'reports'])->name('reports')->block(10, 10);
        Route::get('/kalendarz', [$controller, 'calendar'])->name('calendar')->block(10, 10);
        Route::post('/kalendarz', [$controller, 'reschedule'])->name('calendar.save')->block(10, 10);
        Route::post('/kalendarz/{id}', [$controller, 'schedule'])->whereNumber('id')->name('schedule')->block(10, 10);
        foreach (['clients' => ['klienci', 'client', 'saveClient'], 'offers' => ['oferty', 'offer', 'saveOffer'],
            'orders' => ['zlecenia', 'order', 'saveOrder'], 'employees' => ['pracownicy', 'employee', 'saveEmployee']] as $key => [$path, $singular, $save]) {
            Route::get('/'.$path, [$controller, $key])->name($key)->block(10, 10);
            Route::get('/'.$path.'/nowy', [$controller, $singular.'Form'])->name($key.'.create')->block(10, 10);
            Route::post('/'.$path, [$controller, $save])->middleware('throttle:60,1,installflow-write-')->name($key.'.store')->block(10, 10);
            Route::get('/'.$path.'/{id}/edycja', [$controller, $singular.'Form'])->whereNumber('id')->name($key.'.edit')->block(10, 10);
            Route::put('/'.$path.'/{id}', [$controller, $save])->whereNumber('id')->middleware('throttle:60,1,installflow-write-')->name($key.'.update')->block(10, 10);
            Route::get('/'.$path.'/{id}', [$controller, $singular])->whereNumber('id')->name($key.'.show')->block(10, 10);
        }
        Route::post('/klienci/{id}/kontakt', [$controller, 'contact'])->whereNumber('id')->name('clients.contact')->block(10, 10);
        Route::delete('/klienci/{id}', [$controller, 'deleteClient'])->whereNumber('id')->name('clients.destroy')->block(10, 10);
        Route::post('/oferty/{id}/zlecenie', [$controller, 'convert'])->whereNumber('id')->name('offers.convert')->block(10, 10);
        Route::get('/oferty/{id}/druk', [$controller, 'printOffer'])->whereNumber('id')->name('offers.print')->block(10, 10);
    });
});
