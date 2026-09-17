<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
        $horarioAgendado = Carbon::parse($agendamento['data_hora']);
        $horarioInicial = $horarioAgendado->copy()->subMinutes(30);
        $horarioFinal = $horarioAgendado->copy()->addMinutes(30);
        $conflito = Agendamento::where('id_filial', $agendamento['id_filial'])
            ->where('ativo', true)
            ->whereNotIn('status_agendamento', ['cancelado', 'Cancelado'])
            ->where('data_hora', '>', $horarioInicial)
            ->where('data_hora', '<=', $horarioFinal)
            ->exists();

        if ($conflito && $agendamento['status_agendamento'] !== 'cancelado') {
            return redirect()->back()
                ->withErrors(['data_hora' => 'Já existe um agendamento nesta filial no horário escolhido ou nos 30 minutos seguintes.'])
                ->withInput();
        }

        Agendamento::create($agendamento);

        return redirect()->route('agendamentos.index')
            ->with('success', 'Agendamento cadastrado com sucesso.');
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
        $dados = $request->validated();
        $novaData = Carbon::parse($dados['data_hora']);
        if (!$novaData->equalTo($agendamento->data_hora) && $novaData->isBefore(today())) {
            return back()
                ->withErrors(['data_hora' => 'A nova data do agendamento deve ser hoje ou uma data futura.'])
                ->withInput();
        }

        $agendamento->update($dados);
        return redirect()->route('agendamentos.show', $agendamento->id)
            ->with('success', 'Agendamento atualizado com sucesso.');
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
