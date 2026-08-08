<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AgendamentoController;
use App\Http\Controllers\api\FilialController;
use App\Http\Controllers\api\PacienteController;
use App\Http\Controllers\api\PlanoTratamentoController;
use App\Http\Controllers\api\ServicoController;
use App\Http\Controllers\api\ServicoTratamentoController;
use App\Http\Controllers\api\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('usuarios', UserController::class);
Route::apiResource('pacientes', PacienteController::class);

Route::apiResource('filials', FilialController::class);
Route::get('filials/{id}/servicos', [FilialController::class, 'servicos']);

Route::apiResource('agendamentos', AgendamentoController::class);
Route::apiResource('planos-tratamento', PlanoTratamentoController::class);
Route::apiResource('servicos-tratamento', ServicoTratamentoController::class);
Route::put('servicos-tratamento/{id}', [ServicoTratamentoController::class, 'update']);

Route::apiResource('servicos', ServicoController::class);