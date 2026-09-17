<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServicoTratamentoRequest;
use App\Http\Requests\UpdateServicoTratamentoRequest;
use App\Models\Agendamento;
use App\Models\PlanoTratamento;
use App\Models\Servico;
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
    public function create(Request $request)
    {
        $dados = $request->validate([
            'id_agendamento' => 'required|integer|exists:agendamentos,id',
        ]);
        $agendamento = Agendamento::with(['paciente', 'filial'])->findOrFail($dados['id_agendamento']);
        $planos = PlanoTratamento::where('id_paciente', $agendamento->id_paciente)
            ->where('ativo', true)
            ->orderByDesc('created_at')
            ->get();
        $servicosPorPlano = [];
        $idsServicosPlanejados = [];
        foreach ($planos as $plano) {
            $ids = $this->extrairIdsServicos($plano->servicos_planejados);
            $servicosPorPlano[$plano->id] = $ids;
            $idsServicosPlanejados = array_merge($idsServicosPlanejados, $ids);
        }
        $servicos = Servico::where('ativo', true)
            ->whereIn('id', array_unique($idsServicosPlanejados))
            ->orderBy('nome')
            ->get();

        return view('servicos-tratamentos.create', compact('agendamento', 'servicos', 'planos', 'servicosPorPlano'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServicoTratamentoRequest $request)
    {
        $dados = $request->validated();
        $agendamento = Agendamento::findOrFail($dados['id_agendamento']);
        $plano = PlanoTratamento::where('id', $dados['id_planos'])
            ->where('id_paciente', $agendamento->id_paciente)
            ->where('ativo', true)
            ->firstOrFail();

        if (!in_array((int) $dados['id_servico'], $this->extrairIdsServicos($plano->servicos_planejados), true)) {
            return back()
                ->withErrors(['id_servico' => 'O serviço selecionado não está planejado neste plano de tratamento.'])
                ->withInput();
        }
        $servicoTratamento = ServicoTratamento::create($dados);
        $agendamento->update(['status_agendamento' => 'concluido']);
        return redirect()->route('agendamentos.show', $agendamento->id)
            ->with('success', 'Agendamento confirmado com sucesso.');
    }

    private function extrairIdsServicos(?string $servicos): array
    {
        $ids = [];
        foreach (explode(',', $servicos ?? '') as $item) {
            $item = trim($item);
            if ($item == '') {
                continue;
            }
            $idServico = str_contains($item, ';')
                ? explode(';', $item, 2)[0]
                : $item;
            $ids[] = (int) $idServico;
        }
        return $ids;
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
