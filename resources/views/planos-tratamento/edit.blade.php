@extends('layout')

@section('titulo', 'Editar Plano de Tratamento')

@section('topbar')
    <div>
        <h1 class="topbar-title">Editar Plano de Tratamento</h1>
        <p class="topbar-subtitle">Bem vindo, {{ auth()->user()->nome }}</p>
    </div>

    <div class="topbar-actions">
        <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
    </div>
@endsection

@section('content')
    <div class="page-content container-fluid py-4">

        <a href="{{ route('dentista.planos-tratamento.show', $planoTratamento->id) }}" class="btn btn-outline-secondary mb-4">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>

        <div class="mb-5">
            <p class="mb-1"
                style="font-size:11px; letter-spacing:3px; color:var(--azul-principal); text-transform:uppercase;">Atualizar
                Plano de Tratamento:</p>
            <h1><i class="bi bi-clipboard2-check me-2" style="color:var(--azul-principal);"></i>Editar Plano</h1>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" style="max-width: 480px;">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card" style="max-width: 900px;">
            <div class="card-body p-4">
                <form action="{{ route('dentista.planos-tratamento.update', $planoTratamento->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id_paciente"
                        value="{{ old('id_paciente', $planoTratamento->id_paciente) }}">

                    <div class="mb-4">
                        <span class="form-label d-block">Paciente</span>
                        <div class="form-control bg-light">{{ $planoTratamento->paciente->nome }}</div>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror"
                            required>
                            @foreach (['Em andamento', 'Concluído', 'Cancelado'] as $status)
                                <option value="{{ $status }}"
                                    {{ old('status', $planoTratamento->status) == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <div class="selecao-servicos" data-campo="servicos_planejados[]">
                            <div class="coluna-servicos">
                                <label class="form-label fw-semibold">
                                    Serviços disponíveis
                                </label>
                                <ul class="connectedSortable list-group lista-disponiveis">
                                    @forelse ($servicos as $servico)
                                        <li class="list-group-item"
                                            data-selecionado="{{ in_array($servico->id, old('servicos_planejados', $servicosPlanejadosSelecionados)) ? '1' : '0' }}">
                                            <div class="form-check">
                                                <input type="checkbox" value="{{ $servico->id }}"
                                                    id="servico-planejado-{{ $servico->id }}" class="form-check-input"
                                                    @checked(in_array($servico->id, old('servicos_planejados', $servicosPlanejadosSelecionados)))>
                                                <label for="servico-planejado-{{ $servico->id }}"
                                                    class="form-check-label">
                                                    {{ $servico->nome }}
                                                </label>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="list-group-item text-muted">
                                            Nenhum serviço ativo cadastrado.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="botoes-servicos">
                                <button type="button" class="btn btn-primary mover-direita">▶</button>
                                <button type="button" class="btn btn-secondary mover-esquerda">◀</button>
                            </div>
                            <div class="coluna-servicos">
                                <label class="form-label fw-semibold">
                                    Serviços selecionados
                                </label>
                                <ul class="connectedSortable list-group lista-selecionados"></ul>
                            </div>
                        </div>
                        @error('servicos_planejados')
                            <div class="invalid-feedback d-block mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <span class="form-label d-block">Serviços concluídos</span>
                        <div class="selecao-servicos" data-campo="servicos_concluidos[]">
                            <div class="coluna-servicos">
                                <ul class="connectedSortable list-group lista-disponiveis">

                                    @forelse ($servicos as $servico)
                                        <li class="list-group-item"
                                            data-selecionado="{{ in_array($servico->id, old('servicos_concluidos', $servicosConcluidosSelecionados)) ? '1' : '0' }}"
                                            data-servico-id="{{ $servico->id }}">

                                            <div class="form-check">
                                                <input type="checkbox" value="{{ $servico->id }}"
                                                    id="servico-concluido-{{ $servico->id }}" class="form-check-input"
                                                    @checked(in_array($servico->id, old('servicos_concluidos', $servicosConcluidosSelecionados)))>

                                                <label for="servico-concluido-{{ $servico->id }}"
                                                    class="form-check-label">
                                                    {{ $servico->nome }}
                                                </label>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="list-group-item text-muted">
                                            Nenhum serviço ativo cadastrado.
                                        </li>
                                    @endforelse

                                </ul>
                            </div>

                            <div class="botoes-servicos">
                                <button type="button" class="btn btn-primary mover-direita">▶</button>
                                <button type="button" class="btn btn-secondary mover-esquerda">◀</button>
                            </div>
                            <div class="coluna-servicos">
                                <label class="form-label fw-semibold">
                                    Serviços selecionados
                                </label>
                                <ul class="connectedSortable list-group lista-selecionados"></ul>
                            </div>
                        </div>
                        @error('servicos_concluidos')
                            <div class="invalid-feedback d-block mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> Atualizar Plano de Tratamento
                        </button>
                        <a href="{{ route('dentista.planos-tratamento.show', $planoTratamento->id) }}"
                            class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const containers = document.querySelectorAll('.selecao-servicos');
            const planejados = document.querySelector(
                '[data-campo="servicos_planejados[]"]'
            );
            const concluidos = document.querySelector(
                '[data-campo="servicos_concluidos[]"]'
            );

            function moverServicos(origem, destino, campo, listaSelecionados) {
                origem.querySelectorAll('li').forEach(item => {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (!checkbox || !checkbox.checked) return;
                    destino.appendChild(item);
                    checkbox.checked = false;
                    checkbox.removeAttribute('name');
                    item.querySelectorAll('input[type="hidden"]')
                        .forEach(input => input.remove());
                    if (destino == listaSelecionados) {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = campo;
                        hidden.value = checkbox.value;
                        item.appendChild(hidden);
                    }
                });
            }

            function sincronizarConcluidos() {
                let idsPlanejados = [];
                planejados.querySelectorAll('.lista-selecionados input[type="hidden"]')
                    .forEach(input => {
                        idsPlanejados.push(input.value);
                    });
                concluidos.querySelectorAll('li[data-servico-id]')
                    .forEach(item => {
                        const id = item.dataset.servicoId;
                        if (!idsPlanejados.includes(id)) {
                            const checkbox = item.querySelector('input[type="checkbox"]');
                            checkbox.checked = false;
                            item.querySelectorAll('input[type="hidden"]')
                                .forEach(input => input.remove());
                            concluidos.querySelector('.lista-disponiveis')
                                .appendChild(item);
                            item.style.display = 'none';
                        } else {
                            item.style.display = '';
                        }
                    });
            }
            containers.forEach(container => {
                const disponiveis = container.querySelector('.lista-disponiveis');
                const selecionados = container.querySelector('.lista-selecionados');
                const campo = container.dataset.campo;
                disponiveis.querySelectorAll('li[data-selecionado="1"]')
                    .forEach(item => {
                        const checkbox = item.querySelector('input[type="checkbox"]');
                        if (checkbox) checkbox.checked = true;
                    });
                moverServicos(disponiveis, selecionados, campo, selecionados);
                container.querySelector('.mover-direita')
                    .addEventListener('click', () => {
                        moverServicos(disponiveis, selecionados, campo, selecionados);
                        sincronizarConcluidos();
                    });
                container.querySelector('.mover-esquerda')
                    .addEventListener('click', () => {
                        moverServicos(selecionados, disponiveis, campo, selecionados);
                        sincronizarConcluidos();
                    });
            });
            sincronizarConcluidos();
        });
    </script>
@endsection
