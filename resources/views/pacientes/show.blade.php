@extends('layout')

@section('titulo', 'Perfil do Paciente')

@section('topbar')
<div>
    <h1 class="topbar-title">Perfil do paciente</h1>
    <p class="topbar-subtitle">Acompanhe os dados clínicos, planos e agendamentos.</p>
</div>

<div class="topbar-actions">
    <span class="pill-date">
        <i class="bi bi-calendar3 me-2"></i>
        {{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}
    </span>
</div>
@endsection

@section('content')
<div class="patient-page">

    <div class="patient-page-nav">
        <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>
            Voltar para pacientes
        </a>
    </div>

    <section class="patient-hero card">
        <div class="card-body">
            <div class="patient-hero-content">

                <div class="patient-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>

                <div class="patient-hero-info">
                    <span class="patient-eyebrow">Paciente</span>
                    <h2>{{ $paciente->nome }}</h2>

                    <div class="patient-quick-info">
                        <span>
                            <i class="bi bi-calendar-event"></i>
                            {{ \Carbon\Carbon::parse($paciente->data_nascimento)->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}
                        </span>

                        <span>
                            <i class="bi bi-telephone"></i>
                            {{ $paciente->telefone ?? 'Telefone não informado' }}
                        </span>
                    </div>
                </div>

                <div class="patient-hero-actions">
                    <a href="{{ route('pacientes.edit', $paciente->id) }}" class="btn btn-brand">
                        <i class="bi bi-pencil-square"></i>
                        Editar paciente
                    </a>

                    <form action="{{ route('pacientes.destroy', $paciente->id) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-delete-patient"
                            onclick="return confirm('Tem certeza que deseja excluir o paciente {{ $paciente->nome }}?')">
                            <i class="bi bi-trash3"></i>
                            Excluir
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <div class="row g-4 mt-1">

        <div class="col-xl-5">

            <section class="card h-100 patient-details-card">
                <div class="card-header section-card-header">
                    <div>
                        <span class="section-overline">Cadastro</span>
                        <h5 class="mb-0">Informações do paciente</h5>
                    </div>

                    <span class="section-icon">
                        <i class="bi bi-person-vcard"></i>
                    </span>
                </div>

                <div class="card-body">
                    <div class="patient-detail-grid">

                        <div class="patient-detail-item">
                            <div class="patient-detail-icon">
                                <i class="bi bi-person"></i>
                            </div>

                            <div>
                                <span class="detail-label">Nome completo</span>
                                <p class="detail-value">{{ $paciente->nome }}</p>
                            </div>
                        </div>

                        <div class="patient-detail-item">
                            <div class="patient-detail-icon">
                                <i class="bi bi-card-text"></i>
                            </div>

                            <div>
                                <span class="detail-label">CPF</span>
                                <p class="detail-value">
                                    {{ $paciente->cpf
                                            ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $paciente->cpf)
                                            : 'Não informado'
                                        }}
                                </p>
                            </div>
                        </div>

                        <div class="patient-detail-item">
                            <div class="patient-detail-icon">
                                <i class="bi bi-calendar3"></i>
                            </div>

                            <div>
                                <span class="detail-label">Data de nascimento</span>
                                <p class="detail-value">
                                    {{ \Carbon\Carbon::parse($paciente->data_nascimento)->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="patient-detail-item">
                            <div class="patient-detail-icon">
                                <i class="bi bi-telephone"></i>
                            </div>

                            <div>
                                <span class="detail-label">Telefone</span>
                                <p class="detail-value">{{ $paciente->telefone ?? 'Não informado' }}</p>
                            </div>
                        </div>

                        <div class="patient-detail-item patient-observations">
                            <div class="patient-detail-icon">
                                <i class="bi bi-clipboard2-pulse"></i>
                            </div>

                            <div>
                                <span class="detail-label">Observações médicas</span>
                                <p class="detail-value mb-0">
                                    {{ $paciente->observacoes_medicas ?? 'Nenhuma observação médica registrada.' }}
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
                        <span class="section-overline">Histórico</span>
                        <h5 class="mb-0">Agendamentos</h5>
                    </div>

                    <span class="badge-count">
                        {{ $paciente->agendamentos->count() }}
                        {{ $paciente->agendamentos->count() == 1 ? 'registro' : 'registros' }}
                    </span>
                </div>

                @if($paciente->agendamentos->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <p class="mb-1 fw-semibold">Nenhum agendamento registrado</p>
                    <small>Os futuros atendimentos deste paciente aparecerão aqui.</small>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table patient-appointments-table align-middle">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Status</th>
                                <th>Pagamento</th>
                                <th>Observações</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($paciente->agendamentos->sortByDesc('data_hora') as $agendamento)
                            @php
                            $status = strtolower($agendamento->status_agendamento ?? '');

                            $statusClass = [
                            'concluido' => 'status-success',
                            'andamento' => 'status-info',
                            'cancelado' => 'status-danger',
                            ][$status] ?? 'status-warning';

                            $pagamento = strtolower($agendamento->status_pagamento ?? '');

                            $pagamentoClass = [
                            'pago' => 'status-success',
                            'pendente' => 'status-danger',
                            ][$pagamento] ?? 'status-warning';
                            @endphp

                            <tr
                                class="appointment-row"
                                onclick="window.location='{{ route('agendamentos.show', $agendamento->id) }}'">
                                <td>
                                    <div class="appointment-date">
                                        <strong>
                                            {{ \Carbon\Carbon::parse($agendamento->data_hora)->format('d/m/Y') }}
                                        </strong>
                                    </div>
                                </td>

                                <td>
                                    <span class="appointment-time">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($agendamento->data_hora)->format('H:i') }}
                                    </span>
                                </td>

                                <td>
                                    <span class="custom-status-badge {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $agendamento->status_agendamento)) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="custom-status-badge {{ $pagamentoClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $agendamento->status_pagamento)) }}
                                    </span>
                                </td>

                                <td class="appointment-note">
                                    {{ $agendamento->observacoes ?? 'Sem observações' }}
                                </td>

                                <td class="text-end">
                                    <i class="bi bi-chevron-right appointment-chevron"></i>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </section>

        </div>

        <div class="col-12">

            <section class="card treatment-card">
                <div class="card-header section-card-header">
                    <div>
                        <span class="section-overline">Planejamento clínico</span>
                        <h5 class="mb-0">Planos de tratamento</h5>
                    </div>

                    <a
                        href="{{ route('dentista.planos-tratamento.create', ['paciente_id' => $paciente->id]) }}"
                        class="btn btn-brand btn-sm">
                        <i class="bi bi-plus-lg"></i>
                        Novo plano
                    </a>
                </div>

                @if($paciente->planos->isEmpty())
                <div class="empty-state treatment-empty-state">
                    <i class="bi bi-clipboard2-plus"></i>
                    <p class="mb-1 fw-semibold">Nenhum plano de tratamento registrado</p>
                    <small>Crie um plano para organizar os serviços e procedimentos do paciente.</small>
                </div>
                @else
                <div class="p-4">
                    <div class="alert alert-primary mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-clipboard2-check fs-5"></i>
                        <span>
                            Existem {{ $paciente->planos->count() }}
                            {{ $paciente->planos->count() == 1 ? 'plano registrado' : 'planos registrados' }}
                            para este paciente.
                        </span>
                    </div>
                </div>
                @endif
            </section>

        </div>

    </div>
</div>
@endsection