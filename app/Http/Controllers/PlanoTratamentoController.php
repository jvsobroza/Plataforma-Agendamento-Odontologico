<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanoTratamentoRequest;
use App\Http\Requests\UpdatePlanoTratamentoRequest;
use App\Models\PlanoTratamento;
use Illuminate\Http\Request;

class PlanoTratamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $planotratamentos = PlanoTratamento::all();
        return view("plano-tratamento.index", compact("planotratamentos"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("plano-tratamento.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlanoTratamentoRequest $request)
    {
        $planoTratamento = PlanoTratamento::create($request->validated());
        return redirect()->route('plano-tratamento.index')->with('success', 'Plano de Tratamento cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PlanoTratamento $planoTratamento)
    {
        $planoTratamento = PlanoTratamento::with(['paciente'])->findOrFail($planoTratamento->id);
        return view('plano-tratamento.show', compact('planoTratamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlanoTratamento $planoTratamento)
    {
        $planoTratamento = PlanoTratamento::with(['paciente'])->findOrFail($planoTratamento->id);
        return view('plano-tratamento.edit', compact('planoTratamento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanoTratamentoRequest $request, PlanoTratamento $planoTratamento)
    {
        $planoTratamento->update($request->validated());
        return redirect()->route('plano-tratamento.index')->with('success', 'Plano de Tratamento atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlanoTratamento $planoTratamento)
    {
        $planoTratamento = PlanoTratamento::findOrFail($planoTratamento->id);
        $planoTratamento->update(['ativo' => false]);
        return redirect()->route('plano-tratamento.index')->with('success', 'Plano de Tratamento desativado com sucesso.');
    }
}
