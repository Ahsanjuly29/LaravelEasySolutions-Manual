<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [FrontendController::class, 'dashboard'])->name('dashboard');
    Route::get('/company', [FrontendController::class, 'company'])->name('company');
});
