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
        $servicos = Servico::where('ativo', true)->get();
        $servicosPlanejados = [];
        foreach (array_filter(explode(',', $planoTratamento->servicos_planejados ?? '')) as $item) {
            $item = trim($item);
            if ($item != '') {
                $nomeServico = $item;
                if (str_contains($item, ';')) {
                    $partes = explode(';', $item, 2);
                    $nomeServico = trim($partes[1] ?? $partes[0] ?? 'Serviço não informado');
                } else {
                    $idServico = (int) $item;
                    foreach ($servicos as $servico) {
                        if ($servico->id == $idServico) {
                            $nomeServico = $servico->nome;
                            break;
                        }
                    }
                }
                $servicosPlanejados[] = $nomeServico;
            }
        }
        $servicosConcluidos = [];
        foreach (array_filter(explode(',', $planoTratamento->servicos_concluidos ?? '')) as $item) {
            $item = trim($item);
            if ($item !== '') {
                $nomeServico = $item;
                if (str_contains($item, ';')) {
                    $partes = explode(';', $item, 2);
                    $nomeServico = trim($partes[1] ?? $partes[0] ?? 'Serviço não informado');
                } else {
                    $idServico = (int) $item;
                    foreach ($servicos as $servico) {
                        if ($servico->id == $idServico) {
                            $nomeServico = $servico->nome;
                            break;
                        }
                    }
                }
                $servicosConcluidos[] = $nomeServico;
            }
        }

        $planoTratamento->servicos_planejados_formatados = $servicosPlanejados;
        $planoTratamento->servicos_concluidos_formatados = $servicosConcluidos;

        return view('planos-tratamento.show', compact('planoTratamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $planoTratamento = PlanoTratamento::with(['paciente'])->findOrFail($id);
        return view('planos-tratamento.edit', compact('planoTratamento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanoTratamentoRequest $request, $id)
    {
        $planoTratamento = PlanoTratamento::findOrFail($id);
        $planoTratamento->update($request->validated());
        return redirect()->route('pacientes.show', ['paciente' => $planoTratamento->id_paciente])->with('success', 'Plano de Tratamento atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $planoTratamento = PlanoTratamento::findOrFail($id);
        $planoTratamento->update(['ativo' => false]);
        $planoTratamento->update(['status' => 'Cancelado']);
        return redirect()->route('pacientes.show', ['paciente' => $planoTratamento->id_paciente])
            ->with('success', 'Plano de Tratamento desativado com sucesso.');
    }
}
