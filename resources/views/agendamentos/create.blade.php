@extends('layout')

@section('titulo', 'Novo Agendamento')

@section('topbar')
<div>
    <h1 class="topbar-title">Novo Agendamento</h1>
    <p class="topbar-subtitle">Preencha os dados da consulta</p>
</div>

<div class="topbar-actions">
    <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
</div>
@endsection

@section('content')
<div class="container-fluid py-4">
    <a href="{{ route('agendamentos.index') }}" class="btn btn-outline-secondary mb-4">
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
            <form action="{{ route('agendamentos.store') }}" method="POST">
                @csrf
                <input type="hidden" name="ativo" value="1">
                <datalist id="pacientes_lista">
                    @foreach ($pacientes as $paciente)
                    <option value="{{ $paciente->cpf }}" label="{{ $paciente->nome }}"
                        data-id="{{ $paciente->id }}"></option>
                    @endforeach
                </datalist>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="cpf_paciente" class="form-label">Paciente (CPF)</label>
                        <input type="text" id="cpf_paciente"
                            class="form-control @error('id_paciente') is-invalid @enderror"
                            value="{{ old('cpf_paciente') }}" inputmode="numeric" maxlength="11"
                            placeholder="Digite o CPF do paciente" autocomplete="off"
                            list="pacientes_lista" required>
                        <input type="hidden" name="id_paciente" id="id_paciente"
                            value="{{ old('id_paciente') }}">
                        <div id="paciente_resultado" class="form-text">Digite os 11 números do CPF.</div>
                        @error('id_paciente')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="id_filial" class="form-label">Filial</label>
                        <select name="id_filial" id="id_filial"
                            class="form-select @error('id_filial') is-invalid @enderror" required>
                            <option value="">Selecione uma filial</option>
                            @foreach ($filiais as $filial)
                            <option value="{{ $filial->id }}" data-dias="{{ $filial->datas_agenda }}" @selected(old('id_filial')==$filial->id)>
                                {{ $filial->cidade }} - {{ $filial->endereco }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_filial')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="data_hora" class="form-label">Data e horário (30 em 30 minutos)</label>
                        <input type="datetime-local" name="data_hora" id="data_hora"
                            class="form-control @error('data_hora') is-invalid @enderror"
                            value="{{ old('data_hora', now()->format('Y-m-d\\TH:i')) }}"
                            min="{{ now()->format('Y-m-d\\TH:i') }}" required>
                        <div id="horario_erro" class="invalid-feedback">
                            Escolha um horário entre 08:30 e 18:30.
                        </div>
                        <div id="dia_erro" class="invalid-feedback">
                            A filial escolhida não atende neste dia.
                        </div>
                        @error('data_hora')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="status_pagamento" class="form-label">Pagamento</label>
                        <select name="status_pagamento" id="status_pagamento" class="form-select" required>
                            @foreach (['Pendente', 'Pago'] as $status)
                            <option value="{{ $status }}" @selected(old('status_pagamento', 'Pendente' )===$status)>
                                {{ $status }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="status_agendamento" value="pendente">

                    <div class="col-12">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea name="observacoes" id="observacoes" rows="4"
                            class="form-control @error('observacoes') is-invalid @enderror"
                            placeholder="Informações importantes sobre a consulta">{{ old('observacoes') }}</textarea>
                        @error('observacoes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('agendamentos.index') }}" class="btn btn-outline-secondary flex-fill">Cancelar</a>
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-check-lg me-1"></i> Cadastrar agendamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataHora = document.getElementById('data_hora');
        const horarioErro = document.getElementById('horario_erro');
        const diaErro = document.getElementById('dia_erro');
        const filial = document.getElementById('id_filial');
        const pacientes = [];
        const opcoesPacientes = document.querySelectorAll('#pacientes_lista option');

        opcoesPacientes.forEach(function(item) {
            pacientes.push({
                id: item.dataset.id,
                cpf: item.value,
                nome: item.label
            });
        });
        const cpfInput = document.getElementById('cpf_paciente');
        const pacienteId = document.getElementById('id_paciente');
        const resultado = document.getElementById('paciente_resultado');
        const form = pacienteId.closest('form');

        function horarioValido() {
            if (!dataHora.value) {
                return true;
            }

            const horario = dataHora.value.split('T')[1];
            return horario >= '08:30' && horario <= '18:30';
        }

        function diaValido() {
            if (!dataHora.value || !filial.value) {
                return true;
            }

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
            const horarioOk = horarioValido();
            const diaOk = diaValido();

            if (!horarioOk || !diaOk) {
                dataHora.classList.add('is-invalid');
            } else {
                dataHora.classList.remove('is-invalid');
            }

            if (!horarioOk) {
                horarioErro.classList.add('d-block');
            } else {
                horarioErro.classList.remove('d-block');
            }

            if (!diaOk) {
                diaErro.classList.add('d-block');
            } else {
                diaErro.classList.remove('d-block');
            }

            return horarioOk && diaOk;
        }

        dataHora.addEventListener('change', function() {
            validarData();
        });

        filial.addEventListener('change', function() {
            if (!diaValido()) {
                dataHora.value = '';
            }
            validarData();
        });

        cpfInput.addEventListener('input', function() {
            const cpf = cpfInput.value.replace(/\D/g, '').slice(0, 11);
            cpfInput.value = cpf;
            pacienteId.value = '';

            if (cpf.length < 11) {
                resultado.className = 'form-text';
                resultado.textContent = 'Digite os 11 números do CPF.';
                return;
            }

            const paciente = pacientes.find(function(item) {
                return item.cpf === cpf;
            });

            if (!paciente) {
                resultado.className = 'form-text text-danger';
                resultado.textContent = 'Nenhum paciente ativo encontrado para este CPF.';
                return;
            }

            pacienteId.value = paciente.id;
            resultado.className = 'form-text text-success';
            resultado.textContent = 'Paciente encontrado: ' + paciente.nome;
        });

        form.addEventListener('submit', function(event) {
            if (!validarData()) {
                event.preventDefault();
                dataHora.focus();
                return;
            }

            if (!pacienteId.value) {
                event.preventDefault();
                resultado.className = 'form-text text-danger';
                resultado.textContent = 'Informe um CPF de paciente válido antes de continuar.';
            }
        });
    });
</script>