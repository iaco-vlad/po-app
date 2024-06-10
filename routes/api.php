<?php

use App\Http\Controllers\PurchaseOrderMetricsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseOrderController;

Route::delete('/orders/bulk', [PurchaseOrderController::class, 'bulkDestroy']);
Route::resource('orders', PurchaseOrderController::class);

Route::get('/metrics/purchase-orders', [PurchaseOrderMetricsController::class, 'index']);
