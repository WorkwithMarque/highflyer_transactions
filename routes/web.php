<?php

use App\Http\Controllers\TransactionIntegrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// routes/web.php



// Route::get('/sync-sales-orders', [TransactionIntegrationController::class, 'syncOrders']);
// Route::get('/ghl-opportunities-delete', [TransactionIntegrationController::class, 'deleteGHLOpportunities']);
// Route::get('/ghl-users', [TransactionIntegrationController::class, 'returnArrayOfGHLUserIds']);
// Route::get('/ghl-users-create', [TransactionIntegrationController::class, 'createGHLUsers']);
