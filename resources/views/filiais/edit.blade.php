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

@section('content')
<div class="page-content container-fluid py-4">

    <a href="{{ route('dentista.filiais.index') }}" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left me-1"></i> Voltar
    </a>

    <div class="card" style="max-width: 480px;">
        <div class="card-body p-4">
            <form action="{{ route('dentista.filiais.update', $filial->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" class="form-control @error('cidade') is-invalid @enderror"
                        id="cidade" name="cidade"
                        value="{{ old('cidade', $filial->cidade) }}" required>
                    @error('cidade')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="endereco" class="form-label">Endereço</label>
                    <input type="text" class="form-control @error('endereco') is-invalid @enderror"
                        id="endereco" name="endereco"
                        value="{{ old('endereco', $filial->endereco) }}" required>
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
                        <input type="checkbox"
                            class="form-check-input @error('datas_agenda') is-invalid @enderror"
                            id="dia_{{ $numero }}" name="datas_agenda[]"
                            value="{{ $numero }}"
                            @checked(in_array($numero, $diasSelecionados))>
                        <label class="form-check-label" for="dia_{{ $numero }}">{{ $dia }}</label>
                    </div>
                    @endforeach

                    @error('datas_agenda')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <span class="form-label d-block">Serviços</span>
                    @forelse($servicos as $servico)
                    <div class="form-check">
                        <input type="checkbox"
                            class="form-check-input @error('servicos') is-invalid @enderror"
                            id="servico_{{ $servico->id }}" name="servicos[]"
                            value="{{ $servico->id }}"
                            @checked(in_array($servico->id, $servicosSelecionados))>
                        <label class="form-check-label" for="servico_{{ $servico->id }}">
                            {{ $servico->nome }}
                        </label>
                    </div>
                    @empty
                    <p class="text-muted mb-0">Nenhum serviço disponível.</p>
                    @endforelse

                    @error('servicos')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i> Atualizar Filial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection