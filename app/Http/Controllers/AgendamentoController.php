<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgendamentoRequest;
use App\Http\Requests\UpdateAgendamentoRequest;
use App\Models\Agendamento;
use Illuminate\Http\Request;

class AgendamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agendamentos = Agendamento::with(['paciente', 'filial'])->get();
        return view("agendamentos.index", compact("agendamentos"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("agendamentos.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAgendamentoRequest $request)
    {
        $agendamento = Agendamento::create($request->validated());
        return redirect()->route('agendamentos.index')->with('success', 'Agendamento cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Agendamento $agendamento)
    {
        $agendamento = Agendamento::with(['paciente', 'filial'])->findOrFail($agendamento->id);
        return view('agendamentos.show', compact('agendamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agendamento $agendamento)
    {
        $agendamento = Agendamento::with(['paciente', 'filial'])->findOrFail($agendamento->id);
        return view('agendamentos.edit', compact('agendamento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAgendamentoRequest $request, Agendamento $agendamento)
    {
        $agendamento->update($request->validated());
        return redirect()->route('agendamentos.index')->with('success', 'Agendamento atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agendamento $agendamento)
    {
        $agendamento = Agendamento::findOrFail($agendamento->id);
        $agendamento->update(['ativo' => false]);
        $agendamento->update(['status_agendamento' => 'cancelado']);
        if (auth()->user()->tipo == 1) {
            return redirect()->route('dentista.index')->with('success', 'Agendamento desativado com sucesso.');
        } else {
            return redirect()->route('secretaria.dashboard')->with('success', 'Agendamento desativado com sucesso.');
        }
    }
}
