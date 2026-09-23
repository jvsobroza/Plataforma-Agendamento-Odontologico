<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Filial;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    private const TIPOS = [
        'agendamentos' => 'Agendamentos por período',
        'financeiro' => 'Relatório financeiro',
        'servicos' => 'Serviços mais realizados',
        'cancelamentos' => 'Cancelamentos',
        'pacientes' => 'Pacientes atendidos',
    ];

    public function index(Request $request, string $tipo = 'agendamentos')
    {
        if (!isset(self::TIPOS[$tipo])) {
            $tipo = 'agendamentos';
        }
        [$inicio, $fim] = $this->periodo($request);
        $agendamentos = $this->agendamentosDoPeriodo($request->user(), $inicio, $fim, $request);
        $dados = match ($tipo) {
            'financeiro' => $this->financeiro($agendamentos),
            'servicos' => $this->servicos($agendamentos),
            'cancelamentos' => $this->cancelamentos($agendamentos),
            'pacientes' => $this->pacientes($agendamentos),
            default => $agendamentos,
        };
        return view('relatorios.index', [
            'tipo' => $tipo,
            'tipos' => self::TIPOS,
            'titulo' => self::TIPOS[$tipo],
            'dados' => $dados,
            'filiais' => Filial::where('ativo', true)->orderBy('cidade')->get(),
            'inicio' => $inicio->toDateString(),
            'fim' => $fim->toDateString(),
            'total' => $agendamentos->count(),
            'valorTotal' => $this->valorTotalAgendamentos($agendamentos),
        ]);
    }

    private function periodo(Request $request)
    {
        $inicio = Carbon::parse($request->input('inicio', now()->startOfMonth()->toDateString()))->startOfDay();
        $fim = Carbon::parse($request->input('fim', now()->endOfMonth()->toDateString()))->endOfDay();
        if ($inicio->greaterThan($fim)) {
            [$inicio, $fim] = [$fim->copy()->startOfDay(), $inicio->copy()->endOfDay()];
        }
        return [$inicio, $fim];
    }

    private function agendamentosDoPeriodo($usuario, Carbon $inicio, Carbon $fim, Request $request)
    {
        $consulta = Agendamento::with(['paciente', 'filial', 'servicoTratamentos.servico'])
            ->whereBetween('data_hora', [$inicio, $fim]);
        $this->aplicarFilial($consulta, $usuario, $request);
        if ($request->filled('status_agendamento')) {
            $consulta->where('status_agendamento', $request->input('status_agendamento'));
        }
        if ($request->filled('status_pagamento')) {
            $consulta->where('status_pagamento', $request->input('status_pagamento'));
        }
        return $consulta->orderBy('data_hora')->get();
    }

    private function aplicarFilial($consulta, $usuario, Request $request)
    {
        if ($usuario->tipo == 2) {
            $consulta->where('id_filial', $usuario->id_filial);
        } elseif ($request->filled('filial')) {
            $consulta->where('id_filial', $request->integer('filial'));
        }
    }

    private function valor(Agendamento $agendamento)
    {
        return (float) $agendamento->servicoTratamentos->sum('preco');
    }

    private function valorTotalAgendamentos($agendamentos)
    {
        $total = 0;
        foreach ($agendamentos as $agendamento) {
            $total += $this->valor($agendamento);
        }
        return $total;
    }

    private function financeiro($agendamentos)
    {
        $resultado = [];
        foreach ($agendamentos->groupBy('status_pagamento') as $status => $itens) {
            $resultado[] = [
                'status' => $status,
                'quantidade' => $itens->count(),
                'valor' => $this->valorTotalAgendamentos($itens),
            ];
        }
        return $resultado;
    }

    private function servicos($agendamentos)
    {
        $servicos = [];
        foreach ($agendamentos as $agendamento) {
            foreach ($agendamento->servicoTratamentos as $tratamento) {
                $nome = $tratamento->servico->nome ?? 'Consulta';
                if (!isset($servicos[$nome])) {
                    $servicos[$nome] = [
                        'nome' => $nome,
                        'quantidade' => 0,
                        'valor' => 0,
                    ];
                }
                $servicos[$nome]['quantidade']++;
                $servicos[$nome]['valor'] += (float) $tratamento->preco;
            }
        }
        $servicos = array_values($servicos);
        for ($i = 0; $i < count($servicos); $i++) {
            for ($j = $i + 1; $j < count($servicos); $j++) {
                if ($servicos[$j]['quantidade'] > $servicos[$i]['quantidade']) {
                    $temporario = $servicos[$i];
                    $servicos[$i] = $servicos[$j];
                    $servicos[$j] = $temporario;
                }
            }
        }
        return $servicos;
    }

    private function cancelamentos($agendamentos)
    {
        $cancelamentos = [];
        foreach ($agendamentos as $agendamento) {
            if (mb_strtolower($agendamento->status_agendamento) == 'cancelado') {
                $cancelamentos[] = $agendamento;
            }
        }
        return $cancelamentos;
    }

    private function pacientes($agendamentos)
    {
        $pacientes = [];
        foreach ($agendamentos as $agendamento) {
            $idPaciente = $agendamento->id_paciente;
            if (!isset($pacientes[$idPaciente])) {
                $pacientes[$idPaciente] = [
                    'nome' => $agendamento->paciente->nome,
                    'quantidade' => 0,
                    'ultimo_atendimento' => null,
                ];
            }
            $pacientes[$idPaciente]['quantidade']++;
            if (
                $pacientes[$idPaciente]['ultimo_atendimento'] === null
                || $agendamento->data_hora->greaterThan($pacientes[$idPaciente]['ultimo_atendimento'])
            ) {
                $pacientes[$idPaciente]['ultimo_atendimento'] = $agendamento->data_hora;
            }
        }
        $pacientes = array_values($pacientes);

        for ($i = 0; $i < count($pacientes); $i++) {
            for ($j = $i + 1; $j < count($pacientes); $j++) {
                $nomeAtual = mb_strtolower($pacientes[$i]['nome']);
                $proximoNome = mb_strtolower($pacientes[$j]['nome']);

                if ($proximoNome < $nomeAtual) {
                    $temporario = $pacientes[$i];
                    $pacientes[$i] = $pacientes[$j];
                    $pacientes[$j] = $temporario;
                }
            }
        }

        return $pacientes;
    }
}
