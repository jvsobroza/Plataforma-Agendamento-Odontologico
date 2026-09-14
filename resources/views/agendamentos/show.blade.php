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
<div class="container-fluid py-4">
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left me-1"></i> Voltar
    </a>

    <div class="card" style="max-width: 760px;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="service-avatar" style="width: 52px; height: 52px; font-weight: 700; font-size: 1.1rem;">
                    {{ strtoupper(substr($agendamento->paciente->nome, 0, 1)) }}
                </div>
                <div>
                    <h2 class="h4 mb-1">{{ $agendamento->paciente->nome }}</h2>
                    <p class="text-muted mb-0">Agendamento #{{ $agendamento->id }}</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="detail-label">Data e horário</div>
                    <div class="detail-value">{{ $agendamento->data_hora->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y, H:i') }}</div>
                </div>
                <div class="col-md-6">
                    <div class="detail-label">Filial</div>
                    <div class="detail-value">{{ $agendamento->filial->cidade }} - {{ $agendamento->filial->endereco }}</div>
                </div>
                <div class="col-md-6">
                    <div class="detail-label">Status do agendamento</div>
                    <span class="badge-status {{ strtolower($agendamento->status_agendamento) }}">{{ ucfirst($agendamento->status_agendamento) }}</span>
                </div>
                <div class="col-md-6">
                    <div class="detail-label">Status do pagamento</div>
                    <div class="detail-value">{{ $agendamento->status_pagamento }}</div>
                </div>
                <div class="col-12">
                    <div class="detail-label">Observações</div>
                    <div class="detail-value">{{ $agendamento->observacoes ?: 'Nenhuma observação registrada.' }}</div>
                </div>
            </div>

            <div class="mt-4 pt-4" style="border-top: 1px solid #EEF1F6;">
                <div class="detail-label mb-3">Serviços e tratamentos</div>
                @forelse ($agendamento->servicoTratamentos as $servicoTratamento)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <div class="fw-semibold">{{ $servicoTratamento->servico->nome ?? 'Serviço não informado' }}</div>
                        <small class="text-muted">{{ $servicoTratamento->tempo }} minutos</small>
                    </div>
                    <span class="fw-semibold">R$ {{ number_format($servicoTratamento->preco, 2, ',', '.') }}</span>
                </div>
                @empty
                <div class="text-muted">Nenhum serviço ou tratamento vinculado.</div>
                @endforelse
            </div>

            @php
            $statusAgendamento = strtolower($agendamento->status_agendamento);
            @endphp

            @if ($statusAgendamento != 'cancelado')
            <div class="d-flex gap-2 mt-4 pt-4" style="border-top: 1px solid #EEF1F6;">
                @if ($statusAgendamento != 'concluido' && auth()->user()->tipo == 1)
                <a href="{{ route('dentista.servicos-tratamento.create') }}?id_agendamento={{ $agendamento->id }}" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i> Confirmar agendamento
                </a>
                @endif
                <a href="{{ route('agendamentos.edit', $agendamento->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil-square me-1"></i> Editar
                </a>
                <form action="{{ route('agendamentos.destroy', $agendamento->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Excluir este agendamento?')">
                        <i class="bi bi-trash me-1"></i> Excluir
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection