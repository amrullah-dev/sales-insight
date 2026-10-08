<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductAnalyticsController;
use App\Http\Controllers\TrendAnalyticsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/products', [ProductAnalyticsController::class, 'index'])->name('products.index');
Route::get('/trends', [TrendAnalyticsController::class, 'index'])->name('trends.index');
