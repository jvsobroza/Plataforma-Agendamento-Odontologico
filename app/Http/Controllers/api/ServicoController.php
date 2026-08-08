<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServicoRequest;
use App\Http\Requests\UpdateServicoRequest;
use App\Models\Servico;

class ServicoController extends Controller
{
    public function index()
    {
        return response()->json(
            Servico::where('ativo', true)->paginate(15)
        );
    }

    public function store(StoreServicoRequest $request)
    {
        $servico = Servico::create($request->validated());

        return response()->json($servico, 201);
    }

    public function show(string $id)
    {
        return response()->json(Servico::with('filiais')->findOrFail($id));
    }

    public function update(UpdateServicoRequest $request, string $id)
    {
        $servico = Servico::findOrFail($id);
        $servico->update($request->validated());

        return response()->json($servico);
    }

    public function destroy(string $id)
    {
        $servico = Servico::findOrFail($id);
        $servico->update(['ativo' => false]);

        return response()->json(['message' => 'Serviço desativado com sucesso.']);
    }
}