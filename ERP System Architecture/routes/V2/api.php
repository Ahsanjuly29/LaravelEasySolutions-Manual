<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\CustomerController as CustomerControllerV2;

Route::prefix('v2')->group(function () {
    Route::post('customers', [CustomerControllerV2::class, 'store']);
    // v2 might use different DTOs / request format while reusing Actions
});
