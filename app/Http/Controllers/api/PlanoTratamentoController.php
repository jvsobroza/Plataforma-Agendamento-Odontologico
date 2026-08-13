<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanoTratamentoRequest;
use App\Http\Requests\UpdatePlanoTratamentoRequest;
use App\Models\PlanoTratamento;

class PlanoTratamentoController extends Controller
{
    public function index()
    {
        return response()->json(
            PlanoTratamento::where('ativo', true)->paginate(15)
        );
    }

    public function store(StorePlanoTratamentoRequest $request)
    {
        $plano = PlanoTratamento::create($request->validated());

        return response()->json($plano, 201);
    }

    public function show(string $id)
    {
        return response()->json(
            PlanoTratamento::with(['paciente', 'servicosTratamento.servico'])->findOrFail($id)
        );
    }

    public function update(UpdatePlanoTratamentoRequest $request, string $id)
    {
        $plano = PlanoTratamento::findOrFail($id);
        $plano->update($request->validated());

        return response()->json($plano);
    }

    public function destroy(string $id)
    {
        $plano = PlanoTratamento::findOrFail($id);
        $plano->update(['ativo' => false]);

        return response()->json(['message' => 'Plano de tratamento desativado com sucesso.']);
    }
}