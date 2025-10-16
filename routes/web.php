<?php
// routes/web.php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FeuController;
use App\Http\Controllers\ColisController;
use App\Http\Controllers\AdministratifController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Models\Stock;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    
    $poidsTotal = Stock::query()
        ->selectRaw('COALESCE(SUM(COALESCE(poids_ma_kg,0) * COALESCE(stock,0)),0) as total')
        ->value('total'); // SUM(poids_ma_kg * stock) [web:74]
        

    return view('dashboard', [
        'poidsTotal' => $poidsTotal,
    ]); // passer la donnée à la vue [web:139]
})->name('dashboard')->middleware(['auth', 'verified']);

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    // Vos pages Volt existantes
    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Nouvelles resources CRUD (noms = prefix de ressource)
    Route::resource('articles', ArticleController::class)->names('articles');
    Route::resource('colis', ColisController::class)->names('colis');
    Route::resource('clients', ClientController::class)->names('clients');
    Route::resource('feux', FeuController::class)->names('feux');
    Route::resource('administratif', AdministratifController::class)->names('administratif');
    Route::resource('stock', StockController::class)->names('stock');
});

require __DIR__ . '/auth.php';
