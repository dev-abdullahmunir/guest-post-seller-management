<?php

use Illuminate\Support\Facades\Route;
use Modules\SellerManagement\Http\Controllers\SellerController;
use Modules\SellerManagement\Http\Controllers\WebsiteController;

/*
|--------------------------------------------------------------------------
| SellerManagement Module Routes
|--------------------------------------------------------------------------
*/

// Web Page Views
Route::get('/sellers', [SellerController::class, 'renderView'])->name('sellers');
Route::get('/websites', [WebsiteController::class, 'renderView'])->name('websites');

// Sellers / Vendors API
Route::prefix('api/sellers')->group(function () {
    Route::get('/', [SellerController::class, 'index'])->name('api.sellers.index');
    Route::post('/', [SellerController::class, 'store'])->name('api.sellers.store');
    Route::get('/{id}', [SellerController::class, 'show'])->name('api.sellers.show');
    Route::post('/{id}/update', [SellerController::class, 'update'])->name('api.sellers.update');
    Route::post('/{id}/delete', [SellerController::class, 'destroy'])->name('api.sellers.destroy');
});

// Websites / Inventory API
Route::prefix('api/websites')->group(function () {
    Route::get('/', [WebsiteController::class, 'index'])->name('api.websites.index');
    Route::post('/', [WebsiteController::class, 'store'])->name('api.websites.store');
    Route::get('/{id}', [WebsiteController::class, 'show'])->name('api.websites.show');
    Route::post('/{id}/update', [WebsiteController::class, 'update'])->name('api.websites.update');
    Route::post('/{id}/delete', [WebsiteController::class, 'destroy'])->name('api.websites.destroy');
});
