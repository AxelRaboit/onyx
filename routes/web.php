<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevDashboardController;
use App\Http\Controllers\DevPasswordController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::controller(DevPasswordController::class)->prefix('dev-access')->name('dev.password.')->group(function () {
    Route::get('/', 'show')->name('show');
    Route::post('/', 'check')->name('check')->middleware('throttle:5,15');
});

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:ROLE_DEV'])->group(function () {
    Route::get('/dev/dashboard', [DevDashboardController::class, 'stats'])->name('dev.dashboard.stats');
    Route::get('/dev/dashboard/users', [DevDashboardController::class, 'users'])->name('dev.dashboard.users');
    Route::post('/dev/dashboard/users/{user}/toggle-role', [DevDashboardController::class, 'toggleRole'])->name('dev.dashboard.users.toggle-role');
    Route::delete('/dev/dashboard/users/{user}', [DevDashboardController::class, 'destroyUser'])->name('dev.dashboard.users.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/locale', [LocaleController::class, 'update'])->name('locale.update');
});

require __DIR__.'/auth.php';
