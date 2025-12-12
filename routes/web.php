<?php

use App\Http\Controllers\AssistantController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryRouteController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\GasTypeController;
use App\Http\Controllers\GrnController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RefillController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierInvoiceController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\SupplierReportController;
use App\Http\Controllers\VehicleController;
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
    Route::resource('purchase-orders', PurchaseOrderController::class)->except(['create','edit']);
    Route::post('/purchase-orders/{po}/status/{status}', [PurchaseOrderController::class, 'updateStatus'])
        ->name('purchase-orders.status');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('grns', GrnController::class)->only(['index', 'show', 'destroy']);
    Route::post('grns', [GrnController::class,'store'])->name('grns.store');
    Route::post('grns/{grn}/approve', [GrnController::class,'approve'])->name('grns.approve');

    Route::get('api/po-items/{po}', [GrnController::class,'getPoItems']);
    Route::get('grns/po-details/{po}', [GrnController::class,'getPoDetails']);
});

Route::get('/stocks', [StockController::class,'index']);

Route::middleware(['auth'])->group(function () {
    // Basic customer CRUD
    Route::get('/customers', [CustomerController::class,'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class,'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class,'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class,'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class,'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class,'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class,'destroy'])->name('customers.destroy');

    // Pricing management routes
    Route::get('/customers/{customer}/pricing', [CustomerController::class,'managePricing'])->name('customers.pricing');
    Route::post('/customers/{customer}/pricing', [CustomerController::class,'setPricing'])->name('customers.setPricing');
    Route::post('/customers/{customer}/pricing/remove', [CustomerController::class,'removePricing'])->name('customers.removePricing');

    // Credit limit management
    Route::put('/customers/{customer}/credit', [CustomerController::class,'updateCreditLimit'])->name('customers.updateCreditLimit');

    // Cylinder tracking routes
    Route::get('/customers/{customer}/cylinders', [CustomerController::class,'cylinderTracking'])->name('customers.cylinders');
    Route::post('/customers/{customer}/cylinders', [CustomerController::class,'recordCylinderTransaction'])->name('customers.recordCylinderTransaction');

    // Dashboard/Summary route
    Route::get('/customers/{customer}/dashboard', [CustomerController::class,'dashboard'])->name('customers.dashboard');
});

Route::middleware(['auth'])->group(function () {
    // Basic CRUD for orders
    Route::get('orders', [OrderController::class,'index'])->name('orders.index');
    Route::get('orders/create', [OrderController::class,'create'])->name('orders.create');
    Route::post('orders', [OrderController::class,'store'])->name('orders.store');
    Route::get('orders/{order}', [OrderController::class,'show'])->name('orders.show');
    Route::delete('orders/{order}', [OrderController::class,'destroy'])->name('orders.destroy');
    
    // Status management
    Route::post('orders/{order}/status/{status}', [OrderController::class,'updateStatus'])->name('orders.status');
    
    // Route assignment
    Route::post('orders/{order}/route', [OrderController::class,'assignRoute'])->name('orders.assignRoute');
    
    // Urgent marking
    Route::post('orders/{order}/urgent', [OrderController::class,'markUrgent'])->name('orders.markUrgent');
    Route::post('orders/{order}/urgent/remove', [OrderController::class,'unmarkUrgent'])->name('orders.unmarkUrgent');
    
    // API endpoints
    Route::get('api/orders/customer-price', [OrderController::class,'getCustomerPrice']);
    Route::get('api/orders/customer-credit', [OrderController::class,'getCustomerCredit']);
});

Route::middleware(['auth'])->group(function () {
    // Drivers
    Route::resource('drivers', DriverController::class);
    Route::post('drivers/{driver}/toggle-status', [DriverController::class, 'toggleStatus'])->name('drivers.toggleStatus');
    Route::get('drivers/{driver}/performance', [DriverController::class, 'performanceReport'])->name('drivers.performance');
    Route::get('api/drivers/availability', [DriverController::class, 'getAvailability']);

    // Assistants
    Route::resource('assistants', AssistantController::class);
    Route::post('assistants/{assistant}/toggle-status', [AssistantController::class, 'toggleStatus'])->name('assistants.toggleStatus');

    // Vehicles
    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/{vehicle}/toggle-status', [VehicleController::class, 'toggleStatus'])->name('vehicles.toggleStatus');
    Route::post('vehicles/{vehicle}/maintenance', [VehicleController::class, 'markMaintenance'])->name('vehicles.markMaintenance');
    Route::post('vehicles/{vehicle}/active', [VehicleController::class, 'markActiveAfterMaintenance'])->name('vehicles.markActive');
    Route::get('vehicles/{vehicle}/maintenance-report', [VehicleController::class, 'maintenanceReport'])->name('vehicles.maintenanceReport');
});

Route::middleware(['auth'])->group(function () {
    // Basic CRUD for delivery routes
    Route::get('delivery-routes', [DeliveryRouteController::class, 'index'])->name('delivery-routes.index');
    Route::get('delivery-routes/create', [DeliveryRouteController::class, 'create'])->name('delivery-routes.create');
    Route::post('delivery-routes', [DeliveryRouteController::class, 'store'])->name('delivery-routes.store');
    Route::get('delivery-routes/{deliveryRoute}', [DeliveryRouteController::class, 'show'])->name('delivery-routes.show');
    Route::get('delivery-routes/{deliveryRoute}/edit', [DeliveryRouteController::class, 'edit'])->name('delivery-routes.edit');
    Route::put('delivery-routes/{deliveryRoute}', [DeliveryRouteController::class, 'update'])->name('delivery-routes.update');
    Route::delete('delivery-routes/{deliveryRoute}', [DeliveryRouteController::class, 'destroy'])->name('delivery-routes.destroy');
    
    // Stop management
    Route::post('delivery-routes/{deliveryRoute}/stops', [DeliveryRouteController::class, 'addStop'])->name('delivery-routes.addStop');
    Route::delete('delivery-routes/{deliveryRoute}/stops/{stop}', [DeliveryRouteController::class, 'removeStop'])->name('delivery-routes.removeStop');
    
    // Personnel assignment
    Route::post('delivery-routes/{deliveryRoute}/driver', [DeliveryRouteController::class, 'assignDriver'])->name('delivery-routes.assignDriver');
    Route::post('delivery-routes/{deliveryRoute}/assistant', [DeliveryRouteController::class, 'assignAssistant'])->name('delivery-routes.assignAssistant');
    
    // Status transitions
    Route::post('delivery-routes/{deliveryRoute}/in-progress', [DeliveryRouteController::class, 'markInProgress'])->name('delivery-routes.markInProgress');
    Route::post('delivery-routes/{deliveryRoute}/completed', [DeliveryRouteController::class, 'markCompleted'])->name('delivery-routes.markCompleted');
    
    // API endpoints
    Route::get('api/delivery-routes/driver-availability', [DeliveryRouteController::class, 'getDriverAvailability']);
    Route::get('api/delivery-routes/stats', [DeliveryRouteController::class, 'getRouteStats']);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('supplier-payments', SupplierPaymentController::class)->except(['create','edit','show']);
    Route::post('/supplier-payments/{payment}/status/{status}', [SupplierPaymentController::class, 'updateStatus'])
        ->name('supplier-payments.status');
    Route::get('/supplier-payments/po-details/{po}', [SupplierPaymentController::class, 'getPoDetails']);
    Route::get('/supplier-payments/ledger/{supplier}', [SupplierPaymentController::class, 'supplierLedger'])
        ->name('supplier-payments.ledger');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('refills', RefillController::class)->except(['create','edit','show']);
    Route::get('/refills/supplier/{supplier}', [RefillController::class, 'supplierSummary'])
        ->name('refills.supplier-summary');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('supplier-invoices', SupplierInvoiceController::class)->except(['create','edit','show']);
    Route::post('/supplier-invoices/{invoice}/status/{status}', [SupplierInvoiceController::class, 'updateStatus'])
        ->name('supplier-invoices.status');
    Route::get('/supplier-invoices/report/{supplier}', [SupplierInvoiceController::class, 'supplierReport'])
        ->name('supplier-invoices.report');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/suppliers/{supplier}/dashboard', [SupplierReportController::class, 'dashboard'])
        ->name('suppliers.dashboard');
    Route::get('/suppliers/{supplier}/po-vs-invoice', [SupplierReportController::class, 'poVsInvoiceComparison'])
        ->name('suppliers.po-vs-invoice');
    Route::get('/suppliers/{supplier}/refill-analysis', [SupplierReportController::class, 'refillAnalysis'])
        ->name('suppliers.refill-analysis');
    Route::get('/suppliers/{supplier}/payment-history', [SupplierReportController::class, 'paymentHistory'])
        ->name('suppliers.payment-history');
});

require __DIR__.'/auth.php';
