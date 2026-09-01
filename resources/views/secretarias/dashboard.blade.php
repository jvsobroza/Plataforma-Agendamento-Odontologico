@extends('layout')

@section('titulo', 'Dashboard')

@section('topbar')
<div>
    <h1 class="topbar-title">Dashboard</h1>
    <p class="topbar-subtitle">Bem vindo, {{ auth()->user()->nome }}</p>
</div>

<div class="topbar-actions">
    <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
    <a href="{{ route('agendamentos.create') }}" class="btn-primary-brand">
        <i class="bi bi-plus-lg"></i> Novo Agendamento
    </a>
</div>
@endsection

@section('content')
<input type="hidden" id="filialCidade" value="{{ $filial[0]['cidade'] ?? 'Não especificada' }}">
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
            <div>
                <div class="stat-label">CONSULTAS HOJE</div>
                <div class="stat-value">{{ $consultasHoje ?? "0"}}</div>
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
                <h2 class="panel-title">Agenda Do Dia - Filial {{ $filial[0]['cidade'] ?? 'Não especificada' }}</h2>
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
                    <div class="agenda-item" onclick="abrirModalAgendamento({{ json_encode($item) }})">
                        <span class="agenda-time">{{ $item['hora'] }}</span>
                        <span class="status-dot {{ $item['status'] }}"></span>
                        <span class="agenda-info">
                            <span class="agenda-paciente d-block">{{ $item['paciente'] }}</span>
                            <span class="agenda-servico">{{ $item['servico'] }}</span>
                        </span>
                        <span class="badge-status {{ $item['status'] }}">{{ $status[$item['status']] ?? $item['status'] }}</span>

                        <i class="bi bi-chevron-right agenda-chevron"></i>
                    </div>
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
<!-- modal, pego do site Alecrim Platters https://github.com/jvsobroza/Alecrim-Platter-Website -->
<dialog id="modalAgendamento" class="modal-sante">
    <div class="modal-content">
        <button type="button" class="btn-fechar" onclick="fecharModalAgendamento()">&times;</button>
        <div class="modal-header">
            <div id="modalAvatar" class="avatar-iniciais">--</div>
            <div class="header-info">
                <h2 id="modalPaciente" class="paciente-nome">Nome do Paciente</h2>
                <span id="modalServicoHeader" class="paciente-servico">Serviço</span>
            </div>
            <div class="filial-tag">
                <span id="modalFilial"></span>
                <i class="bi bi-map"></i>
            </div>
        </div>
        <div class="modal-grid">
            <div class="info-card">
                <span class="card-label">Horário</span>
                <strong id="modalHora" class="card-value"></strong>
            </div>

            <div class="info-card">
                <span class="card-label">Status</span>
                <div class="status-wrapper">
                    <span id="modalStatusDot" class="status-dot-md"></span>
                    <span id="modalStatus" class="badge-status-pill"></span>
                </div>
            </div>

            <div class="info-card">
                <span class="card-label">Tempo Estimado</span>
                <strong id="modalTempo" class="card-value"></strong>
            </div>

            <div class="info-card">
                <span class="card-label">Serviço</span>
                <strong id="modalServicoCard" class="card-value"></strong>
            </div>
        </div>

        <div class="modal-section">
            <span class="section-title">Observações</span>
            <div id="modalObservacao" class="observation-box">
            </div>
        </div>

        <div class="modal-section">
            <span class="section-title">Última Consulta</span>
            <p id="modalUltimaConsulta" class="section-text"></p>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn-reagendar" onclick="reagendarAgendamento()">
                <i class="bi bi-arrow-repeat"></i> Reagendar
            </button>
            <button type="button" class="btn-cancelar" onclick="cancelarAgendamento()">
                <i class="bi bi-x-lg"></i> Cancelar
            </button>
            <form id="formDeletarAgendamento" action="" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</dialog>
@endsection
<script>
    let idAgendamento = null;

    function abrirModalAgendamento(item) {
        const modal = document.getElementById('modalAgendamento');
        agendamentoIdAtual = item.id;
        const filial = document.getElementById('filialCidade');
        const inicial = item.paciente ? item.paciente.trim().charAt(0).toUpperCase() : '';
        const statusTexto = {
            'concluido': 'Concluído',
            'andamento': 'Em Andamento',
            'pendente': 'Agendado'
        };
        document.getElementById('modalAvatar').innerText = inicial;
        document.getElementById('modalPaciente').innerText = item.paciente;
        document.getElementById('modalServicoHeader').innerText = item.servico;
        document.getElementById('modalFilial').innerText = filial.value || 'Não especificada';
        document.getElementById('modalHora').innerText = item.hora || '--:--';
        document.getElementById('modalTempo').innerText = '45 min'; //FAZER TEMPO
        document.getElementById('modalServicoCard').innerText = item.servico || '';
        document.getElementById('modalStatus').innerText = statusTexto[item.status] || item.status;
        document.getElementById('modalObservacao').innerText = item.observacao || 'Sem observações.';
        document.getElementById('modalUltimaConsulta').innerText = item.ultima_consulta || 'Paciente novo - sem histórico';

        modal.showModal();
    }

    function fecharModalAgendamento() {
        document.getElementById('modalAgendamento').close();
    }

    function reagendarAgendamento() {} //falta

    function cancelarAgendamento() {
        if (!agendamentoIdAtual) return;
        if (confirm('Tem certeza que deseja cancelar este agendamento?')) {
            const form = document.getElementById('formDeletarAgendamento');
            const baseUrl = "{{ route('agendamentos.destroy', ':id') }}";
            form.action = baseUrl.replace(':id', agendamentoIdAtual);
            form.submit();
        }
    }
</script>