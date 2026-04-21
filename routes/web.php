<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevDashboardController;
use App\Http\Controllers\DevPasswordController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NoteImageController;
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
    Route::get('/guide', fn () => inertia('Guide'))->name('guide');

    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::patch('/notes/reorder', [NoteController::class, 'reorder'])->name('notes.reorder');
    Route::get('/notes/graph', [NoteController::class, 'graph'])->name('notes.graph');
    Route::get('/notes/{note}', [NoteController::class, 'show'])->name('notes.show');
    Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::patch('/notes/{note}/move', [NoteController::class, 'move'])->name('notes.move');
    Route::get('/notes/{note}/backlinks', [NoteController::class, 'backlinks'])->name('notes.backlinks');
    Route::get('/notes/{note}/unlinked-mentions', [NoteController::class, 'unlinkedMentions'])->name('notes.unlinkedMentions');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
    Route::post('/notes/images', [NoteImageController::class, 'upload'])->name('notes.images.upload');
    Route::get('/notes/images/{filename}', [NoteImageController::class, 'serve'])->name('notes.images.serve');
});

Route::middleware(['auth', 'role:ROLE_DEV'])->group(function () {
    Route::get('/dev/dashboard', [DevDashboardController::class, 'stats'])->name('dev.dashboard.stats');
    Route::get('/dev/dashboard/users', [DevDashboardController::class, 'users'])->name('dev.dashboard.users');
    Route::post('/dev/dashboard/users', [DevDashboardController::class, 'storeUser'])->name('dev.dashboard.users.store');
    Route::patch('/dev/dashboard/users/{user}', [DevDashboardController::class, 'updateUser'])->name('dev.dashboard.users.update');
    Route::post('/dev/dashboard/users/{user}/toggle-role', [DevDashboardController::class, 'toggleRole'])->name('dev.dashboard.users.toggle-role');
    Route::post('/dev/dashboard/users/{user}/impersonate', [DevDashboardController::class, 'impersonate'])->name('dev.dashboard.users.impersonate');
    Route::delete('/dev/dashboard/users/{user}', [DevDashboardController::class, 'destroyUser'])->name('dev.dashboard.users.destroy');
    Route::get('/dev/dashboard/invitations', [DevDashboardController::class, 'invitations'])->name('dev.dashboard.invitations');
    Route::post('/dev/dashboard/invitations', [DevDashboardController::class, 'sendInvitation'])->name('dev.dashboard.invitations.send');
    Route::get('/dev/dashboard/parameters', [DevDashboardController::class, 'parameters'])->name('dev.dashboard.parameters');
    Route::patch('/dev/dashboard/parameters/{key}', [DevDashboardController::class, 'updateParameter'])->name('dev.dashboard.parameters.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/dev/impersonation/leave', [DevDashboardController::class, 'leaveImpersonation'])->name('dev.impersonation.leave');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/locale', [LocaleController::class, 'update'])->name('locale.update');
});

require __DIR__.'/auth.php';
