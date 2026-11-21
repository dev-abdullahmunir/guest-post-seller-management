<?php

use Illuminate\Support\Facades\Route;
use Modules\SellerManagement\Http\Controllers\SellerController;

Route::prefix('sellers')->name('sellers.')->group(function () {
    Route::get('/', [SellerController::class, 'index'])->name('index');
    Route::post('/', [SellerController::class, 'store'])->name('store');
    Route::get('/{id}', [SellerController::class, 'show'])->name('show');
    Route::put('/{id}', [SellerController::class, 'update'])->name('update');
    Route::delete('/{id}', [SellerController::class, 'destroy'])->name('destroy');
});
