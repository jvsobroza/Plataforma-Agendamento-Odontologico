<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServicoTratamentoRequest;
use App\Http\Requests\UpdateServicoTratamentoRequest;
use App\Models\ServicoTratamento;
use Illuminate\Http\Request;

class ServicoTratamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $servicostratamentos = ServicoTratamento::with(['planoTratamento', 'servico', 'agendamento'])->get();
        return view("servicos-tratamentos.index", compact("servicostratamentos"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("servicos-tratamentos.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServicoTratamentoRequest $request)
    {
        $servicoTratamento = ServicoTratamento::create($request->validated());
        return redirect()->route('servicos-tratamentos.index')->with('success', 'Serviço de Tratamento cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServicoTratamento $servicoTratamento)
    {
        $servicoTratamento = ServicoTratamento::findOrFail($servicoTratamento->id);
        return view('servicos-tratamentos.show', compact('servicoTratamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServicoTratamento $servicoTratamento)
    {
        $servicoTratamento = ServicoTratamento::findOrFail($servicoTratamento->id);
        return view('servicos-tratamentos.edit', compact('servicoTratamento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServicoTratamentoRequest $request, ServicoTratamento $servicoTratamento)
    {
        $servicoTratamento->update($request->validated());
        return redirect()->route('servicos-tratamentos.index')->with('success', 'Serviço de Tratamento atualizado com sucesso.');        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServicoTratamento $servicoTratamento)
    {
        $servicoTratamento = ServicoTratamento::findOrFail($servicoTratamento->id);
        $servicoTratamento->update(['ativo' => false]);
        return redirect()->route('servicos-tratamentos.index')->with('success', 'Serviço de Tratamento desativado com sucesso.');
    }
}
