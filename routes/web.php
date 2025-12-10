<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GasTypeController;
use App\Http\Controllers\GrnController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth'])->group(function() {
    Route::resource('suppliers', SupplierController::class);
});

Route::resource('gas-types', GasTypeController::class);

Route::post('/gas-types/{gasType}/supplier-rate',
    [GasTypeController::class, 'saveSupplierRate']
)->name('gas-types.supplier-rate');

Route::delete('/gas-types/{gasType}/supplier/{supplier}',
    [GasTypeController::class, 'removeSupplier']
)->name('gas-types.remove-supplier');

Route::post('/api/get-supplier-rate', [GasTypeController::class, 'getSupplierRate'])
    ->name('api.get-supplier-rate');


Route::middleware(['auth'])->group(function () {
    Route::resource('purchase-orders', PurchaseOrderController::class)->except(['create','edit','show']);
    Route::post('/purchase-orders/{po}/status/{status}', [PurchaseOrderController::class, 'updateStatus'])
        ->name('purchase-orders.status');
});

Route::middleware(['auth'])->group(function () {
    Route::get('grns', [GrnController::class,'index'])->name('grns.index');
    Route::post('grns', [GrnController::class,'store'])->name('grns.store');
    Route::delete('grns/{grn}', [GrnController::class,'destroy'])->name('grns.destroy');

    Route::post('grns/{grn}/approve', [GrnController::class,'approve'])->name('grns.approve');

    Route::get('api/po-items/{po}', [GrnController::class,'getPoItems']);
});

Route::get('/stocks', [StockController::class,'index']);



require __DIR__.'/auth.php';
