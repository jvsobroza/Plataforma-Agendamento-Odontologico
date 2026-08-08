<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFilialRequest;
use App\Http\Requests\UpdateFilialRequest;
use App\Models\Filial;

class FilialController extends Controller
{
    public function index()
    {
        return response()->json(
            Filial::where('ativo', true)->paginate(15)
        );
    }

    public function store(StoreFilialRequest $request)
    {
        $filial = Filial::create($request->validated());

        return response()->json($filial, 201);
    }

    public function show(string $id)
    {
        return response()->json(
            Filial::with(['usuarios', 'servicos'])->findOrFail($id)
        );
    }

    public function update(UpdateFilialRequest $request, string $id)
    {
        $filial = Filial::findOrFail($id);
        $filial->update($request->validated());

        return response()->json($filial);
    }

    public function destroy(string $id)
    {
        $filial = Filial::findOrFail($id);
        $filial->update(['ativo' => false]);

        return response()->json(['message' => 'Filial desativada com sucesso.']);
    }

    public function servicos(string $id)
    {
        return response()->json(Filial::with('servicos')->findOrFail($id)->servicos);
    }
}