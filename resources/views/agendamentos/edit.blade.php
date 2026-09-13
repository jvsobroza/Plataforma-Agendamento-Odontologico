@extends('layout')

@section('titulo', 'Editar Agendamento')

@section('topbar')
<div>
    <h1 class="topbar-title">Editar Agendamento</h1>
    <p class="topbar-subtitle">Atualize os dados da consulta</p>
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

    @if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card" style="max-width: 760px;">
        <div class="card-body p-4">
            <form action="{{ route('agendamentos.update', $agendamento->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="ativo" value="{{ $agendamento->ativo ? 1 : 0 }}">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="id_paciente" class="form-label">Paciente</label>
                        <select name="id_paciente" id="id_paciente" class="form-select @error('id_paciente') is-invalid @enderror" required>
                            @foreach ($pacientes as $paciente)
                            <option value="{{ $paciente->id }}" @selected(old('id_paciente', $agendamento->id_paciente) == $paciente->id)>
                                {{ $paciente->nome }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_paciente')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="id_filial" class="form-label">Filial</label>
                        <select name="id_filial" id="id_filial" class="form-select @error('id_filial') is-invalid @enderror" required>
                            @foreach ($filiais as $filial)
                            <option value="{{ $filial->id }}" data-dias="{{ $filial->datas_agenda }}" @selected(old('id_filial', $agendamento->id_filial) == $filial->id)>
                                {{ $filial->cidade }} - {{ $filial->endereco }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_filial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="data_hora" class="form-label">Data e horário</label>
                        <input type="datetime-local" name="data_hora" id="data_hora"
                            class="form-control @error('data_hora') is-invalid @enderror"
                            value="{{ old('data_hora', $agendamento->data_hora->format('Y-m-d\\TH:i')) }}"
                            min="{{ now()->format('Y-m-d\\TH:i') }}" required>
                        <div id="dia_erro" class="invalid-feedback">A filial escolhida não atende neste dia.</div>
                        @error('data_hora')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label for="status_pagamento" class="form-label">Pagamento</label>
                        <select name="status_pagamento" id="status_pagamento" class="form-select" required>
                            @foreach (['Pendente', 'Pago'] as $status)
                            <option value="{{ $status }}" @selected(old('status_pagamento', $agendamento->status_pagamento) === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="status_agendamento" class="form-label">Status</label>
                        <select name="status_agendamento" id="status_agendamento" class="form-select" required>
                            @foreach (['pendente' => 'Pendente', 'concluido' => 'Concluído', 'cancelado' => 'Cancelado'] as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected(old('status_agendamento', $agendamento->status_agendamento) === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea name="observacoes" id="observacoes" rows="4" class="form-control @error('observacoes') is-invalid @enderror">{{ old('observacoes', $agendamento->observacoes) }}</textarea>
                        @error('observacoes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary flex-fill">Cancelar</a>
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-check-lg me-1"></i> Salvar alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filial = document.getElementById('id_filial');
        const dataHora = document.getElementById('data_hora');
        const diaErro = document.getElementById('dia_erro');
        const form = dataHora.closest('form');

        function diaValido() {
            if (!dataHora.value || !filial.value) return true;
            const diasPermitidos = [];
            const diasFilial = (filial.selectedOptions[0].dataset.dias || '').split(';');

            diasFilial.forEach(function(dia) {
                if (dia !== '') {
                    diasPermitidos.push(Number(dia));
                }
            });

            return diasPermitidos.includes(new Date(dataHora.value).getDay());
        }

        function validarData() {
            const valido = diaValido();

            if (!valido) {
                dataHora.classList.add('is-invalid');
                diaErro.classList.add('d-block');
            } else {
                dataHora.classList.remove('is-invalid');
                diaErro.classList.remove('d-block');
            }

            return valido;
        }

        dataHora.addEventListener('change', validarData);
        filial.addEventListener('change', function() {
            if (!diaValido()) dataHora.value = '';
            validarData();
        });
        form.addEventListener('submit', function(event) {
            if (!validarData()) {
                event.preventDefault();
                dataHora.focus();
            }
        });
        validarData();
    });
</script>