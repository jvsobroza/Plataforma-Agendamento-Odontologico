@extends('layout')

@section('titulo', 'Agendamentos')

@section('topbar')
<div>
    <h1 class="topbar-title">Agendamentos</h1>
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

<div class="card">
    <div class="card-body p-3">
        <div id="calendar"></div>
    </div>
</div>
<div class="modal fade" id="agendamentoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="border-bottom: 1px solid #EEF1F6;">
                <div class="d-flex align-items-center gap-3">
                    <div id="modalAvatar" class="service-avatar" style="width:48px; height:48px; font-weight:700; font-size:1rem;"></div>
                    <div>
                        <h5 class="mb-0" id="modalPaciente"></h5>
                        <span class="text-muted" id="modalServico" style="font-size: .88rem;"></span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="detail-label">Horário</div>
                        <div class="detail-value mb-0" id="modalHorario"></div>
                    </div>
                    <div class="col-6">
                        <div class="detail-label">Status</div>
                        <span class="badge-status" id="modalStatus"></span>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">Filial</div>
                    <div class="detail-value mb-0" id="modalFilial"></div>
                </div>

                <div class="mb-1">
                    <div class="detail-label">Observações</div>
                    <div class="detail-value mb-0" id="modalObservacoes"></div>
                </div>
            </div>

            <div class="modal-footer" style="border-top: 1px solid #EEF1F6;">
                <a href="#" id="modalEditarLink" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil-square me-1"></i> Editar
                </a>
                <form id="modalExcluirForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm"
                        onclick="return confirm('Excluir este agendamento?')">
                        <i class="bi bi-trash me-1"></i> Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.11/index.global.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.11/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const agendamentos = @json($agendamentos);
        const coresByStatus = {
            concluido: '#9CC450',
            pendente: '#003087',
            cancelado: '#E63008',
        };

        const nomesStatus = {
            concluido: 'Concluído',
            pendente: 'Pendente',
            cancelado: 'Cancelado',
        };

        const eventos = [];

        for (let i = 0; i < agendamentos.length; i++) {
            const ag = agendamentos[i];
            let nomePaciente = 'Paciente';
            if (ag.paciente) {
                nomePaciente = ag.paciente.nome;
            }
            let nomeServico = null;
            if (ag.servico_tratamentos && ag.servico_tratamentos.length > 0) {
                if (ag.servico_tratamentos[0].servico) {
                    nomeServico = ag.servico_tratamentos[0].servico.nome;
                }
            }
            let titulo = nomePaciente;
            if (nomeServico) {
                titulo = titulo + ' - ' + nomeServico;
            }
            let cor = coresByStatus[ag.status_agendamento];
            if (!cor) {
                cor = '#B0B0B0';
            }
            eventos.push({
                id: ag.id,
                title: titulo,
                start: ag.data_hora,
                backgroundColor: cor,
                extendedProps: {
                    paciente: nomePaciente,
                    servico: nomeServico ? nomeServico : 'Consulta',
                    filial: ag.filial ? ag.filial.cidade : '-',
                    status: ag.status_agendamento,
                    statusLabel: nomesStatus[ag.status_agendamento] ? nomesStatus[ag.status_agendamento] : ag.status_agendamento,
                    observacoes: ag.observacoes ? ag.observacoes : 'Nenhuma',
                    id: ag.id,
                }
            });
        }
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'pt-br',
            height: 'auto',
            slotMinTime: '08:30:00',
            slotMaxTime: '18:30:00',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay',
            },
            buttonText: {
                today: 'Hoje',
                month: 'Mês',
                week: 'Semana',
                day: 'Dia',
            },
            events: eventos,
            eventClick: function(info) {
                const props = info.event.extendedProps;
                document.getElementById('modalAvatar').textContent = props.paciente
                    .split(' ')
                    .map(p => p[0])
                    .slice(0, 2)
                    .join('')
                    .toUpperCase();
                document.getElementById('modalPaciente').textContent = props.paciente;
                document.getElementById('modalServico').textContent = props.servico;
                document.getElementById('modalHorario').textContent =
                    info.event.start.toLocaleString('pt-BR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                document.getElementById('modalFilial').textContent = props.filial;
                document.getElementById('modalObservacoes').textContent = props.observacoes;
                const statusEl = document.getElementById('modalStatus');
                statusEl.textContent = props.statusLabel;
                statusEl.className = 'badge-status ' + props.status;
                document.getElementById('modalEditarLink').href = `/agendamentos/${props.id}/edit`;
                document.getElementById('modalExcluirForm').action = `/agendamentos/${props.id}`;

                new bootstrap.Modal(document.getElementById('agendamentoModal')).show();
            },
        });

        calendar.render();
    });
</script>
@endpush