<?php

use App\Http\Controllers\Api\Authentication\AuthenticationController;

use App\Http\Controllers\Api\MasterItem\ItemController;
use App\Http\Controllers\Api\MasterItem\StockController;
use App\Http\Controllers\Api\MasterItem\StockCardController;
use App\Http\Controllers\Api\MasterItem\UnitController;

use App\Http\Controllers\Api\MasterService\AidController;
use App\Http\Controllers\Api\MasterService\ServiceController;

use App\Http\Controllers\Api\Purchase\GoodReceivedController;
use App\Http\Controllers\Api\Purchase\PurchaseOrderController;
use App\Http\Controllers\Api\Purchase\PurchaseRequestController;
use App\Http\Controllers\Api\Purchase\SupplierController;

use App\Http\Controllers\Api\System\PermissionController;
use App\Http\Controllers\Api\System\RoleController;
use App\Http\Controllers\Api\System\UserController;

use App\Http\Controllers\Api\Sales\CashierController;

use App\Http\Controllers\Api\Summary\SummarySalesController;

use App\Http\Controllers\Api\Utils\UtilsController;

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('authentication/login', [AuthenticationController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::post('authentication/logout', [AuthenticationController::class, 'logout']);

    Route::resource('master-item/item', ItemController::class);
    Route::resource('master-item/unit', UnitController::class);
    Route::get('master-item/stock/export-excel', [StockController::class, 'exportExcel']);
    Route::get('master-item/stock', [StockController::class, 'index']);
    Route::resource('master-item/stock-card', StockCardController::class)->only(['index', 'store']);
    
    Route::resource('master-service/service', ServiceController::class);
    Route::resource('master-service/aid', AidController::class);
    
    Route::get('system/permission/parsed', [PermissionController::class, 'parsedPermission']);
    Route::resource('system/permission', PermissionController::class);
    Route::resource('system/role', RoleController::class);
    Route::resource('system/user', UserController::class);

    Route::resource('purchase/supplier', SupplierController::class);
    Route::get('purchase/purchase-request/download-pdf/{purchaseRequest}', [PurchaseRequestController::class, 'downloadPdf']);
    Route::resource('purchase/purchase-request', PurchaseRequestController::class)->only(['index', 'store']);
    Route::get('purchase/purchase-order/download-pdf/{purchaseOrder}', [PurchaseOrderController::class, 'downloadPdf']);
    Route::resource('purchase/purchase-order', PurchaseOrderController::class)->only(['index', 'store']);
    Route::get('purchase/good-received/download-pdf/{goodReceived}', [GoodReceivedController::class, 'downloadPdf']);
    Route::resource('purchase/good-received', GoodReceivedController::class)->only(['index', 'store']);

    Route::resource('sales/cashier', CashierController::class)->only(['index', 'store']);

    Route::get('summary/summary-sales', [SummarySalesController::class, 'index']);

    Route::get('utils/get-date', [UtilsController::class, 'getDate']);
    Route::get('utils/stock-reminder', [UtilsController::class, 'stockReminder']);
});