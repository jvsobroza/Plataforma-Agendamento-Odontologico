@extends('layout')

@section('titulo', 'Confirmar Agendamento')

@section('topbar')
<div>
    <h1 class="topbar-title">Confirmar Agendamento</h1>
    <p class="topbar-subtitle">Bem vindo, {{ auth()->user()->nome }}</p>
</div>

<div class="topbar-actions">
    <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
</div>
@endsection

@section('content')
<div class="page-content container-fluid py-4">

    <a href="{{ route('agendamentos.show', $agendamento->id) }}" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left me-1"></i> Voltar
    </a>

    <div class="mb-5">
        <p class="mb-1" style="font-size:11px; letter-spacing:3px; color:var(--azul-principal); text-transform:uppercase;">Confirmação de consulta</p>
        <h1><i class="bi bi-check-circle me-2" style="color:var(--azul-principal);"></i>Confirmar atendimento</h1>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger" style="max-width: 520px;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card" style="max-width: 520px;">
        <div class="card-body p-4">
            <form action="{{ route('dentista.servicos-tratamento.store') }}" method="POST">
                @csrf

                <input type="hidden" name="id_agendamento" value="{{ old('id_agendamento', $agendamento->id) }}">

                <div class="mb-4">
                    <span class="form-label d-block">Paciente</span>
                    <div class="form-control bg-light">{{ $agendamento->paciente->nome }}</div>
                </div>

                <div class="mb-4">
                    <span class="form-label d-block">Data e horário</span>
                    <div class="form-control bg-light">
                        {{ $agendamento->data_hora->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y, H:i') }}
                    </div>
                </div>

                @if ($planos->isNotEmpty())
                <div class="mb-4">
                    <label for="id_planos" class="form-label">Plano de tratamento</label>
                    <select name="id_planos" id="id_planos" class="form-select @error('id_planos') is-invalid @enderror" required>
                        @foreach ($planos as $plano)
                        <option value="{{ $plano->id }}" {{ old('id_planos') == $plano->id ? 'selected' : '' }}>
                            Plano #{{ $plano->id }} - {{ $plano->status }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_planos')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                @else
                <div class="alert alert-warning mb-4">
                    Este paciente não possui um plano de tratamento ativo. Crie um plano antes de confirmar o agendamento.
                </div>
                @endif

                <div class="mb-4">
                    <label for="id_servico" class="form-label">Serviço realizado</label>
                    <select name="id_servico" id="id_servico" class="form-select @error('id_servico') is-invalid @enderror" required>
                        <option value="">Selecione um serviço</option>
                        @foreach ($servicos as $servico)
                        @php
                        $planosDoServico = [];
                        foreach ($servicosPorPlano as $planoId => $idsServicos) {
                            if (in_array($servico->id, $idsServicos)) {
                                $planosDoServico[] = $planoId;
                            }
                        }
                        @endphp
                        <option value="{{ $servico->id }}"
                            data-planos="{{ implode(',', $planosDoServico) }}"
                            {{ old('id_servico') == $servico->id ? 'selected' : '' }}>
                            {{ $servico->nome }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_servico')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="tempo" class="form-label">Tempo (minutos)</label>
                        <input type="number" min="1" name="tempo" id="tempo"
                            class="form-control @error('tempo') is-invalid @enderror"
                            value="{{ old('tempo') }}" required>
                        @error('tempo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="preco" class="form-label">Preço</label>
                        <input type="number" step="0.01" min="0" name="preco" id="preco"
                            class="form-control @error('preco') is-invalid @enderror"
                            value="{{ old('preco') }}" required>
                        @error('preco')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-success" @disabled($planos->isEmpty())>
                        <i class="bi bi-check-lg me-1"></i> Confirmar agendamento
                    </button>
                    <a href="{{ route('agendamentos.show', $agendamento->id) }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const planoSelect = document.getElementById('id_planos');
        const servicoSelect = document.getElementById('id_servico');
        const servicoSelecionado = @json(old('id_servico'));

        function atualizarServicos() {
            const planoId = planoSelect ? planoSelect.value : '';

            Array.from(servicoSelect.options).forEach(function (option, index) {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                const planos = (option.dataset.planos || '').split(',');
                option.hidden = !planoId || !planos.includes(planoId);
            });

            const opcaoAtual = servicoSelect.options[servicoSelect.selectedIndex];
            if (!opcaoAtual || opcaoAtual.hidden) {
                servicoSelect.value = '';
            }
        }

        if (planoSelect) {
            planoSelect.addEventListener('change', atualizarServicos);
            atualizarServicos();

            if (servicoSelecionado) {
                servicoSelect.value = servicoSelecionado;
                atualizarServicos();
            }
        }
    });
</script>
@endpush
@endsection