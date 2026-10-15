@extends('layout')

@section('titulo', 'Filiais')

@section('topbar')
    <div>
        <h1 class="topbar-title">Filiais</h1>
        <p class="topbar-subtitle">Bem vindo, {{ auth()->user()->nome }}</p>
    </div>

    <div class="topbar-actions">
        <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
    </div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
@section('content')
    <div class="page-content container-fluid py-4">

        <a href="{{ route('dentista.filiais.index') }}" class="btn btn-outline-secondary mb-4">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>

        <div class="card" style="max-width: 900px;">
            <div class="card-body p-4">
                <form action="{{ route('dentista.filiais.store') }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="mb-4">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control @error('cidade') is-invalid @enderror" id="cidade"
                            name="cidade" value="{{ old('cidade') }}" required>
                        @error('cidade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="endereco" class="form-label">Endereço</label>
                        <input type="text" class="form-control @error('endereco') is-invalid @enderror" id="endereco"
                            name="endereco" value="{{ old('endereco') }}" required>
                        @error('endereco')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <span class="form-label d-block">Dias de Funcionamento</span>
                        @php
                            $diasSemana = [
                                0 => 'Domingo',
                                1 => 'Segunda-feira',
                                2 => 'Terça-feira',
                                3 => 'Quarta-feira',
                                4 => 'Quinta-feira',
                                5 => 'Sexta-feira',
                                6 => 'Sábado',
                            ];
                        @endphp

                        @foreach($diasSemana as $numero => $dia)
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input @error('datas_agenda') is-invalid @enderror"
                                    id="dia_{{ $numero }}" name="datas_agenda[]" value="{{ $numero }}">
                                <label class="form-check-label" for="dia_{{ $numero }}">{{ $dia }}</label>
                            </div>
                        @endforeach

                        @error('datas_agenda')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="selecao-servicos">
                        <div class="coluna-servicos">
                            <label class="form-label fw-semibold">
                                Serviços disponíveis
                            </label>
                            <ul id="sortable1" class="connectedSortable list-group">
                                @forelse($servicos as $servico)
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <input type="checkbox"
                                                class="form-check-input @error('servicos') is-invalid @enderror"
                                                id="servico_{{ $servico->id }}" value="{{ $servico->id }}">
                                            <label class="form-check-label" for="servico_{{ $servico->id }}">
                                                {{ $servico->nome }}
                                            </label>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted">
                                        Nenhum serviço disponível.
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
                            <ul id="sortable2" class="connectedSortable list-group">
                            </ul>
                        </div>
                    </div>
                    @error('servicos')
                        <div class="invalid-feedback d-block mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> Criar Filial
                        </button>
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
                        item.querySelectorAll('input[type="hidden"][name="servicos[]"]')
                            .forEach(input => input.remove());
                        if (destino.id == 'sortable2') {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'servicos[]';
                            input.value = checkbox.value;

                            item.appendChild(input);
                        }
                    }
                });
            }
            document.getElementById('moverDireita').addEventListener('click', function () {
                moverServicos(disponiveis, selecionados);
            });

            document.getElementById('moverEsquerda').addEventListener('click', function () {
                moverServicos(selecionados, disponiveis);
            });
        });
    </script>
@endsection