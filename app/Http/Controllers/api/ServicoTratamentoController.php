<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServicoTratamentoRequest;
use App\Http\Requests\UpdateServicoTratamentoRequest;
use App\Models\ServicoTratamento;

class ServicoTratamentoController extends Controller
{
    public function index()
    {
        return response()->json(
            ServicoTratamento::with(['planoTratamento', 'servico', 'agendamento'])->paginate(15)
        );
    }

    public function store(StoreServicoTratamentoRequest $request)
    {
        $servicoTratamento = ServicoTratamento::create($request->validated());

        return response()->json(
            $servicoTratamento->load(['planoTratamento', 'servico', 'agendamento']),
            201
        );
    }

    public function show(string $id)
    {
        return response()->json(
            ServicoTratamento::with(['planoTratamento', 'servico', 'agendamento'])->findOrFail($id)
        );
    }

    public function update(UpdateServicoTratamentoRequest $request, string $id)
    {
        $servicoTratamento = ServicoTratamento::findOrFail($id);
        $servicoTratamento->update($request->validated());

        return response()->json($servicoTratamento);
    }

    public function destroy(string $id)
    {
        ServicoTratamento::findOrFail($id)->delete();

        return response()->json(['message' => 'Vínculo de serviço removido com sucesso.']);
    }
}