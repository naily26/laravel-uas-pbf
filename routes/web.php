<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\GudangController;
use App\Http\Controllers\BarangController;
// use App\Http\Controllers\UserController;
// use App\Http\Controllers\TransaksiController;
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
    return 'not found';
});

//Route Gudang
// Route::post('/api/gudang_insert', [GudangController::class, 'insert']);
// Route::get('/api/gudang_all', [GudangController::class, 'get']);
// Route::get('/api/gudang_by_id/{id}', [GudangController::class, 'detail']);
// Route::put('/api/gudang_update', [GudangController::class, 'update']);
// Route::delete('/api/gudang_delete/{id}', [GudangController::class, 'delete']);

//Route barang
Route::post('/api/barang_insert', [BarangController::class, 'insert']);
Route::get('/api/barang_all', [BarangController::class, 'get']);
Route::put('/api/barang_update', [BarangController::class, 'update']);
Route::delete('/api/barang_delete/{id}', [BarangController::class, 'delete']);

//Route User
// Route::post('/api/user_insert', [UserController::class, 'insert']);
// Route::post('/api/user_login', [UserController::class, 'loginApps']);
// Route::get('/api/user_all', [UserController::class, 'get']);
// Route::get('/api/user_by_id/{id}', [UserController::class, 'detail']);
// Route::put('/api/user_update', [UserController::class, 'update']);
// Route::delete('/api/user_delete/{id}', [UserController::class, 'delete']);

//Route Transaksi
// Route::post('/api/transaction_insert', [TransaksiController::class, 'insert']);
// Route::get('/api/transaction_all', [TransaksiController::class, 'get']);
