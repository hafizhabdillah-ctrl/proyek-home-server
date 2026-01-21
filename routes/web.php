<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Livewire\Volt\Volt;
use App\Livewire\Chat;

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

Route::get('/chat', Chat::class)->middleware('auth')->name('chat');
Route::get('/', Chat::class)->name('chat');

Route::get('/', function () {
    return auth()->check() ? redirect()->route('chat') : redirect()->route('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
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
});
