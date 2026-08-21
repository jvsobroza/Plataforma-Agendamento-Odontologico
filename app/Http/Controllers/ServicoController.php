<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServicoRequest;
use App\Http\Requests\UpdateServicoRequest;
use App\Models\Servico;
use Illuminate\Http\Request;

class ServicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $servicos = Servico::all();
        return view("servicos.index", compact("servicos"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("servicos.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServicoRequest $request)
    {
        $servico = Servico::create($request->validated());
        return redirect()->route('servicos.index')->with('success', 'Serviço cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Servico $servico)
    {
        return view('servicos.show', compact('servico'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Servico $servico)
    {
        $servico = Servico::findOrFail($servico->id);
        return view('servicos.edit', compact('servico'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServicoRequest $request, Servico $servico)
    {
        $servico->update($request->validated());
        return redirect()->route('servicos.index')->with('success', 'Serviço atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Servico $servico)
    {
        $servico = Servico::findOrFail($servico->id);
        $servico->update(['ativo' => false]);
        return redirect()->route('servicos.index')->with('success', 'Serviço desativado com sucesso.');
    }
}
