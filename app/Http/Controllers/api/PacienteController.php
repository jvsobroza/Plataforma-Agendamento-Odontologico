<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePacienteRequest;
use App\Http\Requests\UpdatePacienteRequest;
use App\Models\Paciente;

class PacienteController extends Controller
{
    public function index()
    {
        return response()->json(
            Paciente::where('ativo', true)->paginate(15)
        );
    }

    public function store(StorePacienteRequest $request)
    {
        $paciente = Paciente::create($request->validated());

        return response()->json($paciente, 201);
    }

    public function show(string $id)
    {
        return response()->json(
            Paciente::with(['agendamentos', 'planosTratamento'])->findOrFail($id)
        );
    }

    public function update(UpdatePacienteRequest $request, string $id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->update($request->validated());

        return response()->json($paciente);
    }

    public function destroy(string $id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->update(['ativo' => false]);

        return response()->json(['message' => 'Paciente desativado com sucesso.']);
    }
}