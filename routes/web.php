<?php

use App\Http\Controllers\Api\MasterItem\StockController;
use App\Http\Controllers\Api\MasterItem\StockCardController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('master-item/stock/export-excel', [StockController::class, 'exportExcel']);
Route::get('master-item/stock-card/export-excel', [StockCardController::class, 'exportExcel']);