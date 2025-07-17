<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KeluhanPelangganController;
use App\Http\Controllers\Api\KeluhanStatusHisController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ! API Routes untuk Keluhan Pelanggan
Route::apiResource('keluhan-pelanggan', KeluhanPelangganController::class);

// ! Route untuk search by nama
Route::get('keluhan-pelanggan/search/nama', [KeluhanPelangganController::class, 'searchByNama']);

// ! Export keluhan pelanggan
Route::get('keluhan-pelanggan/export/{format}', [KeluhanPelangganController::class, 'export']);

// ! Dashboard summary API
Route::get('dashboard/summary-status', [KeluhanPelangganController::class, 'summaryStatus']);
Route::get('dashboard/status-per-month', [KeluhanPelangganController::class, 'statusPerMonth']);
Route::get('dashboard/top-aging', [KeluhanPelangganController::class, 'topAging']);

// ! API Routes untuk Keluhan Status History
Route::apiResource('keluhan-status-history', KeluhanStatusHisController::class);

// ! Route untuk search by status
Route::get('keluhan-status-history/search/status', [KeluhanStatusHisController::class, 'searchByStatus']);
// ! Update status by keluhan_id
Route::put('keluhan-status-history/update-by-keluhan/{keluhan_id}', [KeluhanStatusHisController::class, 'updateStatusByKeluhanId']);
// ! Delete all status by keluhan_id
Route::delete('keluhan-status-history/delete-by-keluhan/{keluhan_id}', [KeluhanStatusHisController::class, 'deleteStatusByKeluhanId']);