<?php

use App\Http\Controllers\Api\CompanyController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('api-company-data', CompanyController::class);
});
