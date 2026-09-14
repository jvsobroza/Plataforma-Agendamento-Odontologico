<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgendamentoRequest;
use App\Http\Requests\UpdateAgendamentoRequest;
use App\Models\Agendamento;
use App\Models\Filial;
use App\Models\Paciente;
use Illuminate\Http\Request;

class AgendamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuario = auth()->user();
        $consulta = Agendamento::with(['paciente', 'filial', 'servicoTratamentos.servico']);
        if ($usuario->tipo == 2) {
            $consulta->where('id_filial', $usuario->id_filial);
        }
        $agendamentos = $consulta->get();
        return view("agendamentos.index", compact("agendamentos"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pacientes = Paciente::where('ativo', true)->orderBy('nome')->get();
        $filiais = Filial::where('ativo', true)->orderBy('cidade')->get();

        return view('agendamentos.create', compact('pacientes', 'filiais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAgendamentoRequest $request)
    {
        $agendamento = $request->validated();
        $horarioAgendado = $agendamento['data_hora'];
        if (Agendamento::where('data_hora', $horarioAgendado)->exists() && $agendamento['status_agendamento'] != 'cancelado' && $agendamento['id_filial'] == $request->id_filial) {
            return redirect()->back()->withErrors(['data_hora' => 'O horário selecionado já está agendado. Por favor, escolha outro horário.'])->withInput();
        } else {
            Agendamento::create($agendamento);
        }
        return redirect()->back()->with('success', 'Agendamento cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Agendamento $agendamento)
    {
        $agendamento = Agendamento::with([
            'paciente',
            'filial',
            'servicoTratamentos.servico',
        ])->findOrFail($agendamento->id);
        return view('agendamentos.show', compact('agendamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agendamento $agendamento)
    {
        $agendamento = Agendamento::with(['paciente', 'filial'])->findOrFail($agendamento->id);
        $pacientes = Paciente::where(function ($query) use ($agendamento) {
            $query->where('ativo', true)
                ->orWhere('id', $agendamento->id_paciente);
        })->orderBy('nome')->get();
        $filiais = Filial::where('ativo', true)
            ->orWhere('id', $agendamento->id_filial)
            ->orderBy('cidade')
            ->get();

        return view('agendamentos.edit', compact('agendamento', 'pacientes', 'filiais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAgendamentoRequest $request, Agendamento $agendamento)
    {
        $agendamento->update($request->validated());
        return redirect()->back()->with('success', 'Agendamento atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agendamento $agendamento)
    {
        $agendamento = Agendamento::findOrFail($agendamento->id);
        $agendamento->update(['ativo' => false]);
        $agendamento->update(['status_agendamento' => 'cancelado']);
        return redirect()->back()->with('success', 'Agendamento desativado com sucesso.');
    }
}
