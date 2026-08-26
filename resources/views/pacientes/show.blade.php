@extends('layout')

@section('titulo', 'Pacientes')

@section('topbar')
<div>
    <h1 class="topbar-title">Pacientes</h1>
    <p class="topbar-subtitle">Bem vindo, {{ auth()->user()->nome }}</p>
</div>

<div class="topbar-actions">
    <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
</div>
@endsection

@section('content')
<div class="page-content container-fluid py-4">

    <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary mb-4">
        <i class="fas fa-arrow-left me-1"></i> Voltar
    </a>

    <div class="mb-5">
        <p class="mb-1" style="font-size:11px; letter-spacing:3px; color:#c95c0a; text-transform:uppercase;">Paciente:</p>
        <h1><i class="fas fa-concierge-bell me-2" style="color:#c95c0a;"></i>{{ $paciente->nome }}</h1>
    </div>

    <div class="card" style="max-width: 480px;">
        <div class="card-body p-4">

            <p class="detail-label">Nome</p>
            <p class="detail-value">{{ $paciente->nome }}</p>

            <p class="detail-label">CPF</p>
            <p class="detail-value">{{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $paciente->cpf) }}</p>

            <p class="detail-label">Data de Nascimento</p>
            <p class="detail-value">{{ \Carbon\Carbon::parse($paciente->data_nascimento)->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</p>

            <p class="detail-label">Telefone</p>
            <p class="detail-value">{{ $paciente->telefone }}</p>

            <p class="detail-label">Observações Médicas</p>
            <p class="detail-value">{{ $paciente->observacoes_medicas ?? 'Nenhuma' }}</p>

            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('pacientes.edit', $paciente->id) }}"
                    class="btn btn-primary flex-fill">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <form action="{{ route('pacientes.destroy', $paciente->id) }}"
                    method="POST" class="flex-fill">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100"
                        onclick="return confirm('Excluir este paciente?')">
                        <i class="fas fa-trash me-1"></i> Excluir
                    </button>
                </form>
            </div>

        </div>
    </div>

    <div class="row mt-5">
        <div class="col-lg-6 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><i class="fas fa-notes-medical me-2" style="color:#c95c0a;"></i>Planos de Tratamento</h4>
                <a href="{{ route('dentista.planos-tratamento.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Novo Plano
                </a>
            </div>

            @if($paciente->planos->isEmpty())
            <p class="text-muted">Nenhum plano de tratamento registrado.</p>
            @else
            <div class="card">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Plano</th>
                                <th>Status</th>
                                <th>Serviços Planejados</th>
                                <th>Serviços Concluídos</th>
                            </tr>
                        </thead> //DEIXAR PRA DEPOIS
                    </table>
                </div>
            </div>
            @endif
        </div>
        <div class="col-lg-6 mb-4">
            <h4 class="mb-3"><i class="fas fa-calendar-check me-2" style="color:#c95c0a;"></i>Agendamentos</h4>
            @if($paciente->agendamentos->isEmpty())
            <p class="text-muted">Nenhum agendamento registrado.</p>
            @else
            <div class="card">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>Status</th>
                                <th>Pagamento</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paciente->agendamentos->sortByDesc('data_hora') as $agendamento)
                            <tr onclick="window.location='{{ route('agendamentos.show', $agendamento->id) }}'" style="cursor:pointer;">
                                <td>{{ \Carbon\Carbon::parse($agendamento->data_hora)->locale('pt_BR')->translatedFormat('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($agendamento->data_hora)->format('H:i') }}</td>
                                <td>
                                    <span class="badge {{ 
                                                $agendamento->status_agendamento == 'concluido' ? 'bg-success' : 
                                                ($agendamento->status_agendamento == 'andamento' ? 'bg-info text-dark' : 'bg-warning text-dark') 
                                            }}">
                                        {{ ucfirst($agendamento->status_agendamento) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $agendamento->status_pagamento == 'pago' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($agendamento->status_pagamento) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $agendamento->observacoes ?? 'Nenhuma' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection