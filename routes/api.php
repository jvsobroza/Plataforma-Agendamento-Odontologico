<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\FilialController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PlanoTratamentoController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\ServicoTratamentoController;
use App\Http\Controllers\UserController;

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