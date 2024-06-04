<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseOrderController;

Route::delete('/orders/bulk', [PurchaseOrderController::class, 'bulkDestroy']);
Route::resource('orders', PurchaseOrderController::class);
