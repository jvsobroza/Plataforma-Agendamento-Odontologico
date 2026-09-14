@extends('layout')

@section('titulo', 'Detalhes do Agendamento')

@section('topbar')
<div>
    <h1 class="topbar-title">Detalhes do Agendamento</h1>
    <p class="topbar-subtitle">Informações da consulta</p>
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
    $statusAgendamento = strtolower($agendamento->status_agendamento);

    $statusBadgeClass = [
    'concluido' => 'status-success',
    'andamento' => 'status-info',
    'cancelado' => 'status-danger',
    ][$statusAgendamento] ?? 'status-warning';
    @endphp

    <section class="patient-hero card">
        <div class="card-body">
            <div class="patient-hero-content">

                <div class="patient-avatar">
                    {{ strtoupper(substr($agendamento->paciente->nome, 0, 1)) }}
                </div>

                <div class="patient-hero-info">
                    <span class="patient-eyebrow">Agendamento #{{ $agendamento->id }}</span>
                    <h2>{{ $agendamento->paciente->nome }}</h2>

                    <div class="patient-quick-info">
                        <span>
                            <i class="bi bi-calendar-event"></i>
                            {{ $agendamento->data_hora->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y, H:i') }}
                        </span>

                        <span>
                            <i class="bi bi-geo-alt"></i>
                            {{ $agendamento->filial->cidade }}
                        </span>

                        <span class="custom-status-badge {{ $statusBadgeClass }}">
                            {{ ucfirst($agendamento->status_agendamento) }}
                        </span>
                    </div>
                </div>

                @if ($statusAgendamento != 'cancelado')
                <div class="patient-hero-actions">
                    @if ($statusAgendamento != 'concluido' && auth()->user()->tipo == 1)
                    <a href="{{ route('dentista.servicos-tratamento.create') }}?id_agendamento={{ $agendamento->id }}" class="btn btn-success">
                        <i class="bi bi-check-lg"></i>
                        Confirmar
                    </a>
                    @endif

                    <a href="{{ route('agendamentos.edit', $agendamento->id) }}" class="btn btn-brand">
                        <i class="bi bi-pencil-square"></i>
                        Editar
                    </a>

                    <form action="{{ route('agendamentos.destroy', $agendamento->id) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-delete-patient"
                            onclick="return confirm('Excluir este agendamento?')">
                            <i class="bi bi-trash3"></i>
                            Excluir
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
                        <span class="section-overline">Consulta</span>
                        <h5 class="mb-0">Detalhes da consulta</h5>
                    </div>

                    <span class="section-icon">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </span>
                </div>

                <div class="card-body">
                    <div class="patient-detail-grid">

                        <div class="patient-detail-item">
                            <div class="patient-detail-icon">
                                <i class="bi bi-calendar3"></i>
                            </div>
                            <div>
                                <span class="detail-label">Data e horário</span>
                                <p class="detail-value">
                                    {{ $agendamento->data_hora->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y, H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="patient-detail-item">
                            <div class="patient-detail-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div>
                                <span class="detail-label">Filial</span>
                                <p class="detail-value">
                                    {{ $agendamento->filial->cidade }} - {{ $agendamento->filial->endereco }}
                                </p>
                            </div>
                        </div>

                        <div class="patient-detail-item">
                            <div class="patient-detail-icon">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div>
                                <span class="detail-label">Status do pagamento</span>
                                <p class="detail-value">{{ $agendamento->status_pagamento }}</p>
                            </div>
                        </div>

                        <div class="patient-detail-item">
                            <div class="patient-detail-icon">
                                <i class="bi bi-flag"></i>
                            </div>
                            <div>
                                <span class="detail-label">Status do agendamento</span>
                                <p class="detail-value mb-0">
                                    <span class="custom-status-badge {{ $statusBadgeClass }}">
                                        {{ ucfirst($agendamento->status_agendamento) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="patient-detail-item patient-observations">
                            <div class="patient-detail-icon">
                                <i class="bi bi-chat-left-text"></i>
                            </div>
                            <div>
                                <span class="detail-label">Observações</span>
                                <p class="detail-value mb-0">
                                    {{ $agendamento->observacoes ?: 'Nenhuma observação registrada.' }}
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
                        <h5 class="mb-0">Serviços e tratamentos</h5>
                    </div>

                    <span class="badge-count">
                        {{ $agendamento->servicoTratamentos->count() }}
                        {{ $agendamento->servicoTratamentos->count() == 1 ? 'serviço' : 'serviços' }}
                    </span>
                </div>

                <div class="card-body">
                    @forelse ($agendamento->servicoTratamentos as $servicoTratamento)
                    @if ($loop->first)
                    <ul class="service-list">
                        @endif
                        <li class="service-list-item">
                            <div class="service-avatar">
                                <i class="bi bi-briefcase"></i>
                            </div>
                            <div class="service-info">
                                <div class="service-name">{{ $servicoTratamento->servico->nome ?? 'Serviço não informado' }}</div>
                                <div class="service-time">{{ $servicoTratamento->tempo }} minutos</div>
                            </div>
                            <span class="service-price">R$ {{ number_format($servicoTratamento->preco, 2, ',', '.') }}</span>
                        </li>
                        @if ($loop->last)
                    </ul>

                    <div class="service-total-row">
                        <span class="service-total-label">Total</span>
                        <span class="service-total-value">
                            R$ {{ number_format($agendamento->servicoTratamentos->sum('preco'), 2, ',', '.') }}
                        </span>
                    </div>
                    @endif
                    @empty
                    <div class="empty-state">
                        <i class="bi bi-briefcase"></i>
                        <p>Nenhum serviço ou tratamento vinculado.</p>
                    </div>
                    @endforelse
                </div>
            </section>

        </div>

    </div>
</div>
@endsection