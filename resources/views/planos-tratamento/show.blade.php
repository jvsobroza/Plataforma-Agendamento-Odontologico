@extends('layout')

@section('titulo', 'Detalhes do Plano de Tratamento')

@section('topbar')
<div>
    <h1 class="topbar-title">Detalhes do Plano de Tratamento</h1>
    <p class="topbar-subtitle">Informações do planejamento clínico</p>
</div>
<div class="topbar-actions">
    <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
</div>
@endsection

@section('content')
<div class="patient-page">

    <div class="patient-page-nav">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>
            Voltar
        </a>
    </div>

    @php
    $statusPlano = $planoTratamento->status ?? 'Em andamento';
    $statusBadgeClass = [
    'Concluído' => 'status-success',
    'Cancelado' => 'status-danger',
    'Em andamento' => 'status-info',
    ][$statusPlano] ?? 'status-warning';

    $servicosPlanejados = $planoTratamento->servicos_planejados_formatados ?? [];
    $servicosConcluidos = $planoTratamento->servicos_concluidos_formatados ?? [];
    @endphp

    <section class="patient-hero card">
        <div class="card-body">
            <div class="patient-hero-content">

                <div class="patient-avatar">
                    {{ strtoupper(substr($planoTratamento->paciente->nome, 0, 1)) }}
                </div>

                <div class="patient-hero-info">
                    <span class="patient-eyebrow">Plano de tratamento #{{ $planoTratamento->id }}</span>
                    <h2>{{ $planoTratamento->paciente->nome ?? 'Paciente não informado' }}</h2>

                    <div class="patient-quick-info">
                        <span>
                            <i class="bi bi-person-vcard"></i>
                            {{ ucfirst($planoTratamento->status ?? 'Em andamento') }}
                        </span>

                        <span>
                            <i class="bi bi-calendar3"></i>
                            {{ $planoTratamento->created_at?->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y') ?? 'Data indisponível' }}
                        </span>

                        <span class="custom-status-badge {{ $planoTratamento->ativo ? 'status-success' : 'status-danger' }}">
                            {{ $planoTratamento->ativo ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                </div>

                @if (auth()->user()->tipo == 1 && $planoTratamento->ativo)
                <div class="patient-hero-actions">
                    <a href="{{ route('dentista.planos-tratamento.edit', ['planos_tratamento' => $planoTratamento->id]) }}" class="btn btn-brand">
                        <i class="bi bi-pencil-square"></i>
                        Editar
                    </a>

                    <form action="{{ route('dentista.planos-tratamento.destroy', $planoTratamento) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-delete-patient" onclick="return confirm('Desativar este plano de tratamento?')">
                            <i class="bi bi-trash3"></i>
                            Desativar
                        </button>
                    </form>
                </div>
                @endif

            </div>
        </div>
    </section>

    <div class="row g-4 mt-1">

        <div class="col-xl-5">
            <section class="card h-100 patient-details-card">
                <div class="card-header section-card-header">
                    <div>
                        <span class="section-overline">Plano</span>
                        <h5 class="mb-0">Detalhes do plano</h5>
                    </div>

                    <span class="section-icon">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </span>
                </div>

                <div class="card-body">
                    <div class="patient-detail-grid">

                        <div class="patient-detail-item">
                            <div class="patient-detail-icon">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <span class="detail-label">Paciente</span>
                                <p class="detail-value">
                                    {{ $planoTratamento->paciente->nome ?? 'Paciente não informado' }}
                                </p>
                            </div>
                        </div>

                        <div class="patient-detail-item">
                            <div class="patient-detail-icon">
                                <i class="bi bi-flag"></i>
                            </div>
                            <div>
                                <span class="detail-label">Status</span>
                                <p class="detail-value mb-0">
                                    <span class="custom-status-badge {{ $statusBadgeClass }}">
                                        {{ $statusPlano }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="patient-detail-item">
                            <div class="patient-detail-icon">
                                <i class="bi bi-toggle-on"></i>
                            </div>
                            <div>
                                <span class="detail-label">Situação</span>
                                <p class="detail-value">
                                    <span class="custom-status-badge {{ $planoTratamento->ativo ? 'status-success' : 'status-danger' }}">
                                        {{ $planoTratamento->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="patient-detail-item">
                            <div class="patient-detail-icon">
                                <i class="bi bi-calendar3"></i>
                            </div>
                            <div>
                                <span class="detail-label">Criado em</span>
                                <p class="detail-value">
                                    {{ $planoTratamento->created_at?->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y, H:i') ?? 'Data indisponível' }}
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </div>

        <div class="col-xl-7">
            <section class="card h-100">
                <div class="card-header section-card-header">
                    <div>
                        <span class="section-overline">Procedimentos</span>
                        <h5 class="mb-0">Serviços previstos</h5>
                    </div>

                    <span class="badge-count">
                        {{ count($servicosPlanejados) }}
                        {{ count($servicosPlanejados) == 1 ? 'serviço' : 'serviços' }}
                    </span>
                </div>

                <div class="card-body">
                    @if ($servicosPlanejados)
                    <ul class="service-list">
                        @foreach ($servicosPlanejados as $servico)
                        <li class="service-list-item">
                            <div class="service-avatar">
                                <i class="bi bi-clipboard2-check"></i>
                            </div>
                            <div class="service-info">
                                <div class="service-name">{{ $servico }}</div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="empty-state">
                        <i class="bi bi-clipboard2-plus"></i>
                        <p>Nenhum serviço planejado.</p>
                    </div>
                    @endif
                </div>
            </section>
        </div>

    </div>

    <div class="row g-4 mt-1">
        <div class="col-12">
            <section class="card h-100">
                <div class="card-header section-card-header">
                    <div>
                        <span class="section-overline">Conclusão</span>
                        <h5 class="mb-0">Serviços concluídos</h5>
                    </div>

                    <span class="badge-count">
                        {{ count($servicosConcluidos) }}
                        {{ count($servicosConcluidos) == 1 ? 'serviço' : 'serviços' }}
                    </span>
                </div>

                <div class="card-body">
                    @if ($servicosConcluidos)
                    <ul class="service-list">
                        @foreach ($servicosConcluidos as $servico)
                        <li class="service-list-item">
                            <div class="service-avatar">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="service-info">
                                <div class="service-name">{{ $servico }}</div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <p>Nenhum serviço concluído.</p>
                    </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
@endsection