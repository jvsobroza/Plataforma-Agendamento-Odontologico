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
    <div class="card-header section-card-header">
        <div>
            <span class="section-overline">Visão geral</span>
            <h5 class="mb-0">Calendário de agendamentos</h5>
        </div>
        <span class="section-icon">
            <i class="bi bi-calendar3"></i>
        </span>
    </div>

    <div class="card-body p-3">
        <div class="calendar-legend">
            <span class="legend-item"><span class="status-dot pendente"></span> Pendente</span>
            <span class="legend-item"><span class="status-dot concluido"></span> Concluído</span>
            <span class="legend-item"><span class="status-dot cancelado"></span> Cancelado</span>
        </div>
        <div id="calendar"></div>
    </div>
</div>

{{-- Modal de detalhes do agendamento --}}
<div class="modal fade" id="agendamentoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="border-bottom: 1px solid #EEF1F6;">
                <div class="d-flex align-items-center gap-3">
                    <div id="modalAvatar" class="patient-avatar" style="width:56px; height:56px; font-size:1.4rem;"></div>
                    <div>
                        <h5 class="mb-0" id="modalPaciente"></h5>
                        <span class="text-muted" id="modalServico" style="font-size: .88rem;"></span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <div class="patient-detail-grid">
                    <div class="patient-detail-item">
                        <div class="patient-detail-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div>
                            <span class="detail-label">Horário</span>
                            <p class="detail-value mb-0" id="modalHorario"></p>
                        </div>
                    </div>

                    <div class="patient-detail-item">
                        <div class="patient-detail-icon">
                            <i class="bi bi-flag"></i>
                        </div>
                        <div>
                            <span class="detail-label">Status</span>
                            <p class="detail-value mb-0">
                                <span class="badge-status" id="modalStatus"></span>
                            </p>
                        </div>
                    </div>

                    <div class="patient-detail-item">
                        <div class="patient-detail-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div>
                            <span class="detail-label">Filial</span>
                            <p class="detail-value mb-0" id="modalFilial"></p>
                        </div>
                    </div>

                    <div class="patient-detail-item patient-observations">
                        <div class="patient-detail-icon">
                            <i class="bi bi-chat-left-text"></i>
                        </div>
                        <div>
                            <span class="detail-label">Observações</span>
                            <p class="detail-value mb-0" id="modalObservacoes"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="border-top: 1px solid #EEF1F6;">
                <a href="#" id="modalVerLink" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye me-1"></i> Visualizar agendamento
                </a>
                @if (auth()->user()->tipo == 1)
                <a href="#" id="modalConfirmarLink" class="btn btn-success btn-sm">
                    <i class="bi bi-check-lg me-1"></i> Confirmar agendamento
                </a>
                @endif
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
            slotMaxTime: '19:00:00',
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
                document.getElementById('modalAvatar').textContent = props.paciente.charAt(0).toUpperCase();
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
                const status = String(props.status || '').trim().toLowerCase();
                const statusLabel = String(props.statusLabel || '').trim().toLowerCase();
                statusEl.className = 'badge-status ' + status;
                const verLink = document.getElementById('modalVerLink');
                const confirmarLink = document.getElementById('modalConfirmarLink');
                const editarLink = document.getElementById('modalEditarLink');
                const excluirForm = document.getElementById('modalExcluirForm');
                const agendamentoCancelado = status.includes('cancelado') || statusLabel.includes('cancelado');

                verLink.href = `/agendamentos/${props.id}`;

                if (agendamentoCancelado) {
                    if (confirmarLink) {
                        confirmarLink.style.setProperty('display', 'none', 'important');
                    }
                    editarLink.style.setProperty('display', 'none', 'important');
                    excluirForm.style.setProperty('display', 'none', 'important');
                } else {
                    if (confirmarLink) {
                        confirmarLink.style.removeProperty('display');
                        confirmarLink.href = `{{ route('dentista.servicos-tratamento.create') }}?id_agendamento=${props.id}`;
                    }
                    editarLink.style.removeProperty('display');
                    excluirForm.style.removeProperty('display');
                    editarLink.href = `/agendamentos/${props.id}/edit`;
                    excluirForm.action = `/agendamentos/${props.id}`;
                }

                new bootstrap.Modal(document.getElementById('agendamentoModal')).show();
            },
        });

        calendar.render();
    });
</script>
@endpush