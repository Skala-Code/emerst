<?php

use App\Http\Controllers\Api\ServiceOrderCalculationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rota sem autenticação para salvar o ID do cálculo na ordem de serviço
Route::post('/service-orders/save-calculation', [ServiceOrderCalculationController::class, 'saveCalculation']);

// Rota sem autenticação para buscar liquidação do PJeCalc
Route::post('/service-orders/fetch-liquidation', [ServiceOrderCalculationController::class, 'fetchLiquidation']);
