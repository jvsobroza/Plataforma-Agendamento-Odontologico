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


Route::apiResource('usuarios', UserController::class)->names('api.usuarios');
Route::apiResource('pacientes', PacienteController::class)->names('api.pacientes');

Route::apiResource('filials', FilialController::class)->names('api.filials');

Route::apiResource('agendamentos', AgendamentoController::class)
    ->names('api.agendamentos');
Route::apiResource('planos-tratamento', PlanoTratamentoController::class)->names('api.planos-tratamento');
Route::apiResource('servicos-tratamento', ServicoTratamentoController::class)->names('api.servicos-tratamento');
Route::put('servicos-tratamento/{id}', [ServicoTratamentoController::class, 'update']);

Route::apiResource('servicos', ServicoController::class)->names('api.servicos');
