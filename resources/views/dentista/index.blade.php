@extends('layout')

@section('titulo', 'Dashboard')

@section('topbar')
<div>
  <h1 class="topbar-title">Dashboard</h1>
  <p class="topbar-subtitle">Bem vindo, {{ auth()->user()->nome }}</p>
</div>

<div class="topbar-actions">
  <span class="pill-date">{{ \Carbon\Carbon::now()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
  <a href="{{ route('agendamentos.create') }}" class="btn-primary-brand">
    <i class="bi bi-plus-lg"></i> Novo Agendamento
  </a>
</div>
@endsection

@section('content')

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
      <div>
        <div class="stat-label">CONSULTAS HOJE</div>
        <div class="stat-value">{{ $consultasHoje  ?? "0"}}</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-person"></i></div>
      <div>
        <div class="stat-label">TOTAL DE PACIENTES</div>
        <div class="stat-value">{{ $totalPacientes ?? "0" }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-calendar2-week"></i></div>
      <div>
        <div class="stat-label">CONSULTAS NO MÊS</div>
        <div class="stat-value">{{ $consultasNoMes ?? "0" }}</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">

  <div class="col-lg-8">
    <div class="panel">
      <div class="panel-header">
        <h2 class="panel-title">Agenda Do Dia - Filial Jaguari</h2> <!-- depois alterar isso para a filial do dia-->
        <a href="{{ route('agendamentos.index') }}" class="pill-tag">{{ count($agendaHoje ?? []) ?: 0 }} Consultas</a>
      </div>

      <ul class="agenda-list">
        @php
        $agendaHoje = $agendaHoje ?? [];

        $status = [
        'concluido' => 'Concluído',
        'andamento' => 'Em Andamento',
        'pendente' => 'Agendado',
        ];
        @endphp

        @foreach ($agendaHoje as $item)
        <li>
          <a href="{{ route('agendamentos.show', $item['id'] ?? 0) }}" class="agenda-item">
            <span class="agenda-time">{{ $item['hora'] }}</span>
            <span class="status-dot {{ $item['status'] }}"></span>
            <span class="agenda-info">
              <span class="agenda-paciente d-block">{{ $item['paciente'] }}</span>
              <span class="agenda-servico">{{ $item['servico'] }}</span>
            </span>
            <span class="badge-status {{ $item['status'] }}">{{ $status[$item['status']] }}</span>
            @if ($item['status'] == 'andamento')
            <i class="bi bi-chevron-right agenda-chevron"></i>
            @endif
          </a>
        </li>
        @endforeach
      </ul>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="panel">
      <div class="panel-header">
        <h2 class="panel-title">Próximos Agendamentos</h2>
        <a href="{{ route('agendamentos.index') }}" class="pill-tag">Ver Todos</a>
      </div>

      <ul class="upcoming-list">
        @php
        $proximos = $proximos ?? [];
        @endphp

        @foreach ($proximos as $item)
        <li class="upcoming-item">
          <span class="date-badge">
            <span class="date-day">{{ $item['dia'] }}</span>
            <span class="date-month">{{ $item['mes'] }}</span>
          </span>
          <span class="upcoming-info">
            <span class="upcoming-paciente d-block">{{ $item['paciente'] }}</span>
            <span class="upcoming-detalhe">{{ $item['hora'] }} - {{ $item['servico'] }}</span>
          </span>
        </li>
        @endforeach
      </ul>
    </div>
  </div>

</div>

@endsection