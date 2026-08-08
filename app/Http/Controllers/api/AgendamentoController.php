<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAgendamentoRequest;
use App\Http\Requests\UpdateAgendamentoRequest;
use App\Models\Agendamento;

class AgendamentoController extends Controller
{
    public function index()
    {
        return response()->json(
            Agendamento::where('ativo', true)
                ->with(['paciente', 'filial'])
                ->orderBy('data_hora')
                ->paginate(15)
        );
    }

    public function store(StoreAgendamentoRequest $request)
    {
        $agendamento = Agendamento::create($request->validated());

        return response()->json($agendamento->load(['paciente', 'filial']), 201);
    }

    public function show(string $id)
    {
        return response()->json(
            Agendamento::with(['paciente', 'filial', 'servicosTratamento'])->findOrFail($id)
        );
    }

    public function update(UpdateAgendamentoRequest $request, string $id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $agendamento->update($request->validated());

        return response()->json($agendamento->load(['paciente', 'filial']));
    }

    public function destroy(string $id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $agendamento->update([
            'ativo' => false,
            'status_agendamento' => 'cancelado',
        ]);

        return response()->json(['message' => 'Agendamento cancelado com sucesso.']);
    }
}