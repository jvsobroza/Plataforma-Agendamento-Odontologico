@extends('layout')

@section('titulo', 'Relatórios')

@section('topbar')
<div>
    <h1 class="topbar-title">Relatórios</h1>
    <p class="topbar-subtitle">Acompanhe os resultados da clínica por período</p>
</div>
@endsection

@section('content')
<div class="card mb-4 relatorio-navegacao">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            @foreach ($tipos as $chave => $nome)
            <a href="{{ route('relatorios.index', ['tipo' => $chave, 'inicio' => $inicio, 'fim' => $fim]) }}"
                class="btn {{ $tipo == $chave ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $nome }}
            </a>
            @endforeach
        </div>
    </div>
</div>

<div class="card mb-4 relatorio-filtros">
    <div class="card-header section-card-header">
        <div>
            <span class="section-overline">Filtros</span>
            <h5 class="mb-0">{{ $titulo }}</h5>
        </div>
        <span class="section-icon"><i class="bi bi-file-earmark-bar-graph"></i></span>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('relatorios.index', $tipo) }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="inicio" class="form-label">Data inicial</label>
                <input type="date" id="inicio" name="inicio" value="{{ $inicio }}" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label for="fim" class="form-label">Data final</label>
                <input type="date" id="fim" name="fim" value="{{ $fim }}" class="form-control" required>
            </div>
            @if (auth()->user()->tipo == 1)
            <div class="col-md-2">
                <label for="filial" class="form-label">Filial</label>
                <select id="filial" name="filial" class="form-select">
                    <option value="">Todas</option>
                    @foreach ($filiais as $filial)
                    <option value="{{ $filial->id }}" @selected(request('filial')==$filial->id)>{{ $filial->cidade }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            @if (in_array($tipo, ['agendamentos', 'financeiro']))
            <div class="col-md-2">
                <label for="status_agendamento" class="form-label">Agendamento</label>
                <select id="status_agendamento" name="status_agendamento" class="form-select">
                    <option value="">Todos</option>
                    @foreach (['Pendente', 'Concluído', 'Cancelado'] as $status)
                    <option value="{{ $status }}" @selected(request('status_agendamento')===$status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status_pagamento" class="form-label">Pagamento</label>
                <select id="status_pagamento" name="status_pagamento" class="form-select">
                    <option value="">Todos</option>
                    @foreach (['Pendente', 'Pago'] as $status)
                    <option value="{{ $status }}" @selected(request('status_pagamento')===$status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn-primary-brand border-0"><i class="bi bi-search"></i> Filtrar</button>
                <a href="{{ route('relatorios.index', $tipo) }}" class="btn btn-light">Limpar</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4 relatorio-resumo">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="section-icon"><i class="bi bi-calendar-check"></i></span>
                <div><small class="text-muted d-block">Agendamentos no período</small><strong class="fs-4">{{ $total }}</strong></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="section-icon"><i class="bi bi-currency-dollar"></i></span>
                <div><small class="text-muted d-block">Valor dos serviços</small><strong class="fs-4">R$ {{ number_format($valorTotal, 2, ',', '.') }}</strong></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header section-card-header relatorio-cabecalho">
        <h5 class="mb-0">Resultado</h5>
        <button type="button" class="btn btn-outline-primary relatorio-imprimir" onclick="window.print()">
            <i class="bi bi-printer"></i> Imprimir
        </button>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            @if ($tipo == 'agendamentos')
            <thead>
                <tr>
                    <th>Data e hora</th>
                    <th>Paciente</th>
                    <th>Filial</th>
                    <th>Serviço</th>
                    <th>Agendamento</th>
                    <th>Pagamento</th>
                    <th class="text-end">Valor</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dados as $agendamento)
                <tr>
                    <td>{{ $agendamento->data_hora->format('d/m/Y H:i') }}</td>
                    <td>{{ $agendamento->paciente->nome }}</td>
                    <td>{{ $agendamento->filial->cidade }}</td>
                    <td>{{ $agendamento->servicoTratamentos->pluck('servico.nome')->filter()->join(', ') ?: 'Consulta' }}</td>
                    <td>{{ $agendamento->status_agendamento }}</td>
                    <td>{{ $agendamento->status_pagamento }}</td>
                    <td class="text-end">R$ {{ number_format($agendamento->servicoTratamentos->sum('preco'), 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Nenhum registro encontrado.</td>
                </tr>
                @endforelse
            </tbody>
            @elseif ($tipo == 'financeiro')
            <thead>
                <tr>
                    <th>Status do pagamento</th>
                    <th>Agendamentos</th>
                    <th class="text-end">Valor</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dados as $item)<tr>
                    <td>{{ $item['status'] }}</td>
                    <td>{{ $item['quantidade'] }}</td>
                    <td class="text-end">R$ {{ number_format($item['valor'], 2, ',', '.') }}</td>
                </tr>@empty<tr>
                    <td colspan="3" class="text-center text-muted py-4">Nenhum registro encontrado.</td>
                </tr>@endforelse</tbody>
            @elseif ($tipo == 'servicos')
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th>Atendimentos</th>
                    <th class="text-end">Valor total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dados as $item)<tr>
                    <td>{{ $item['nome'] }}</td>
                    <td>{{ $item['quantidade'] }}</td>
                    <td class="text-end">R$ {{ number_format($item['valor'], 2, ',', '.') }}</td>
                </tr>@empty<tr>
                    <td colspan="3" class="text-center text-muted py-4">Nenhum registro encontrado.</td>
                </tr>@endforelse</tbody>
            @elseif ($tipo == 'cancelamentos')
            <thead>
                <tr>
                    <th>Data e hora</th>
                    <th>Paciente</th>
                    <th>Filial</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>@forelse ($dados as $agendamento)<tr>
                    <td>{{ $agendamento->data_hora->format('d/m/Y H:i') }}</td>
                    <td>{{ $agendamento->paciente->nome }}</td>
                    <td>{{ $agendamento->filial->cidade }}</td>
                    <td>{{ $agendamento->observacoes ?: '-' }}</td>
                </tr>@empty<tr>
                    <td colspan="4" class="text-center text-muted py-4">Nenhum cancelamento encontrado.</td>
                </tr>@endforelse</tbody>
            @elseif ($tipo == 'pacientes')
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Atendimentos</th>
                    <th>Último atendimento</th>
                </tr>
            </thead>
            <tbody>@forelse ($dados as $item)<tr>
                    <td>{{ $item['nome'] }}</td>
                    <td>{{ $item['quantidade'] }}</td>
                    <td>{{ $item['ultimo_atendimento']?->format('d/m/Y H:i') ?: '-' }}</td>
                </tr>@empty<tr>
                    <td colspan="3" class="text-center text-muted py-4">Nenhum paciente encontrado.</td>
                </tr>@endforelse</tbody>
            @else
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Filial</th>
                    <th>Agendamentos</th>
                </tr>
            </thead>
            <tbody>@forelse ($dados as $item)<tr>
                    <td>{{ $item['data'] }}</td>
                    <td>{{ $item['horario'] }}</td>
                    <td>{{ $item['filial'] }}</td>
                    <td>{{ $item['quantidade'] }}</td>
                </tr>@empty<tr>
                    <td colspan="4" class="text-center text-muted py-4">Nenhum horário encontrado.</td>
                </tr>@endforelse</tbody>
            @endif
        </table>
    </div>
</div>
@endsection