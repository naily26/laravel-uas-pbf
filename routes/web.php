<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\TransaksiController;
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

//Route Gpeminjaman
Route::post('/api/peminjaman_insert', [PeminjamanController::class, 'insert']);
Route::get('/api/peminjaman_all', [PeminjamanController::class, 'get']);
//Route::get('/api/peminjaman_by_id/{id}', [PeminjamanController::class, 'detail']);
//Route::post('/api/peminjaman_update', [PeminjamanController::class, 'update']);
Route::delete('/api/peminjaman_delete/{id}', [PeminjamanController::class, 'delete']);

//Route barang
Route::post('/api/barang_insert', [BarangController::class, 'insert']);
Route::get('/api/barang_all', [BarangController::class, 'get']);
Route::post('/api/barang_update', [BarangController::class, 'update']);
Route::delete('/api/barang_delete/{id}', [BarangController::class, 'delete']);

//Route User
Route::post('/api/pengguna_insert', [PenggunaController::class, 'insert']);
Route::get('/api/pengguna_all', [PenggunaController::class, 'get']);
Route::post('/api/pengguna_update', [PenggunaController::class, 'update']);
Route::delete('/api/pengguna_delete/{id}', [PenggunaController::class, 'delete']);

//Route Transaksi
Route::get('/api/trans_peminjaman_update_kembali/{id}', [PeminjamanController::class, 'updateKembali']);
//Route::get('/api/trans_peminjaman_all', [TransaksiController::class, 'get']);
