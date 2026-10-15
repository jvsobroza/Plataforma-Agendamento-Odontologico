@extends('layout')

@section('titulo', 'Plano de Tratamento')

@section('topbar')
    <div>
        <h1 class="topbar-title">Plano de Tratamento</h1>
        <p class="topbar-subtitle">Bem vindo, {{ auth()->user()->nome }}</p>
    </div>

    <div class="topbar-actions">
        <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
    </div>
@endsection

@section('content')
    <div class="page-content container-fluid py-4">

        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mb-4">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>

        <div class="mb-5">
            <p class="mb-1"
                style="font-size:11px; letter-spacing:3px; color:var(--azul-principal); text-transform:uppercase;">Novo
                Plano de Tratamento:</p>
            <h1><i class="bi bi-clipboard2-plus me-2" style="color:var(--azul-principal);"></i>Cadastrar Plano de Tratamento
            </h1>
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
                <form action="{{ route('dentista.planos-tratamento.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_paciente" value="{{ $paciente->id }}">
                    <div class="mb-4">
                        <span class="form-label d-block">Paciente</span>
                        <div class="form-control bg-light">{{ $paciente->nome }}</div>
                    </div>
                    <input type="hidden" name="status" value="Em andamento">
                    <div class="mb-4">
                        <span class="form-label d-block">Serviços planejados</span>
                        <div class="selecao-servicos">
                            <div class="coluna-servicos">
                                <label class="form-label fw-semibold">
                                    Serviços disponíveis
                                </label>
                                <ul id="sortable1" class="connectedSortable list-group">
                                    @forelse ($servicos as $servico)
                                        <li class="list-group-item"
                                            data-selecionado="{{ in_array($servico->id, old('servicos_planejados', [])) ? '1' : '0' }}">
                                            <div class="form-check">
                                                <input type="checkbox" value="{{ $servico->id }}"
                                                    id="servico-{{ $servico->id }}" class="form-check-input"
                                                    @checked(in_array($servico->id, old('servicos_planejados', [])))>

                                                <label for="servico-{{ $servico->id }}" class="form-check-label">
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
                                <button type="button" id="moverDireita" class="btn btn-primary">▶</button>
                                <button type="button" id="moverEsquerda" class="btn btn-secondary">◀</button>
                            </div>
                            <div class="coluna-servicos">
                                <label class="form-label fw-semibold">
                                    Serviços selecionados
                                </label>
                                <ul id="sortable2" class="connectedSortable list-group"></ul>
                            </div>
                        </div>
                        @error('servicos_planejados')
                            <div class="invalid-feedback d-block mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> Criar Plano de Tratamento
                        </button>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const disponiveis = document.getElementById('sortable1');
            const selecionados = document.getElementById('sortable2');
            function moverServicos(origem, destino) {
                const itens = origem.querySelectorAll('li');
                itens.forEach(item => {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (checkbox && checkbox.checked) {
                        destino.appendChild(item);
                        checkbox.checked = false;
                        checkbox.removeAttribute('name');
                        item.querySelectorAll('input[type="hidden"][name="servicos_planejados[]"]')
                            .forEach(input => input.remove());
                        if (destino.id == 'sortable2') {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'servicos_planejados[]';
                            input.value = checkbox.value;

                            item.appendChild(input);
                        }
                    }
                });
            }
            disponiveis.querySelectorAll('li[data-selecionado="1"]')
                .forEach(item => {
                    const checkbox = item.querySelector('input[type="checkbox"]');

                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            moverServicos(disponiveis, selecionados);
            document.getElementById('moverDireita').addEventListener('click', function () {
                moverServicos(disponiveis, selecionados);
            });
            document.getElementById('moverEsquerda').addEventListener('click', function () {
                moverServicos(selecionados, disponiveis);
            });
        });

    </script>
@endsection