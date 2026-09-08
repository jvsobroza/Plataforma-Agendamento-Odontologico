<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanoTratamentoRequest;
use App\Http\Requests\UpdatePlanoTratamentoRequest;
use App\Models\Paciente;
use App\Models\PlanoTratamento;
use App\Models\Servico;
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
    public function create(Request $request)
    {
        $dados = $request->validate([
            'paciente_id' => 'required|integer|exists:pacientes,id',
        ]);
        $paciente = Paciente::where('ativo', true)->findOrFail($dados['paciente_id']);
        $servicos = Servico::where('ativo', true)->get();
        return view("planos-tratamento.create", compact("paciente", "servicos"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlanoTratamentoRequest $request)
    {
        $dados = $request->validated();
        $dados['servicos_planejados'] = implode(',', $dados['servicos_planejados']);
        $dados['servicos_concluidos'] = null;
        PlanoTratamento::create($dados);
        return redirect()->route('pacientes.show', ['paciente' => $dados['id_paciente']])
            ->with('success', 'Plano de Tratamento cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $planoTratamento = PlanoTratamento::with(['paciente'])->findOrFail($id);
        return view('planos-tratamento.show', compact('planoTratamento'));
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
