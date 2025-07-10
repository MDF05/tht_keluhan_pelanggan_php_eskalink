<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KeluhanPelangganController;

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

// API Routes untuk Keluhan Pelanggan
Route::apiResource('keluhan-pelanggan', KeluhanPelangganController::class);

// Route untuk search by nama
Route::get('keluhan-pelanggan/search/nama', [KeluhanPelangganController::class, 'searchByNama']);