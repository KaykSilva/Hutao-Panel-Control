<?php

use App\Http\Controllers\Admin\BotSettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SharedAccountController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/accounts', [SharedAccountController::class, 'index'])->name('accounts.index');
    Route::post('/accounts', [SharedAccountController::class, 'store'])->name('accounts.store');
    Route::put('/account-participants/{participant}/paid', [SharedAccountController::class, 'markPaid'])->name('account-participants.paid');
    Route::put('/account-participants/{participant}/open', [SharedAccountController::class, 'markOpen'])->name('account-participants.open');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');
    Route::get('/bot', [BotSettingsController::class, 'edit'])->name('bot.edit');
    Route::put('/bot', [BotSettingsController::class, 'update'])->name('bot.update');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
});

require __DIR__.'/auth.php';
