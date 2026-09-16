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
        <p class="mb-1" style="font-size:11px; letter-spacing:3px; color:var(--azul-principal); text-transform:uppercase;">Atualizar Plano de Tratamento:</p>
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

    <div class="card" style="max-width: 480px;">
        <div class="card-body p-4">
            <form action="{{ route('dentista.planos-tratamento.update', $planoTratamento->id) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="id_paciente" value="{{ old('id_paciente', $planoTratamento->id_paciente) }}">

                <div class="mb-4">
                    <span class="form-label d-block">Paciente</span>
                    <div class="form-control bg-light">{{ $planoTratamento->paciente->nome }}</div>
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach (['Em andamento', 'Concluído', 'Cancelado'] as $status)
                        <option value="{{ $status }}" {{ old('status', $planoTratamento->status) == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                        @endforeach
                    </select>
                    @error('status')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <span class="form-label d-block">Serviços planejados</span>
                    <div class="border rounded p-3 @error('servicos_planejados') border-danger @enderror">
                        @forelse ($servicos as $servico)
                        <div class="form-check">
                            <input type="checkbox" name="servicos_planejados[]" value="{{ $servico->id }}"
                                id="servico-planejado-{{ $servico->id }}" class="form-check-input"
                                @checked(in_array($servico->id, old('servicos_planejados', $servicosPlanejadosSelecionados)))>
                            <label for="servico-planejado-{{ $servico->id }}" class="form-check-label">{{ $servico->nome }}</label>
                        </div>
                        @empty
                        <p class="mb-0 text-muted">Nenhum serviço ativo cadastrado.</p>
                        @endforelse
                    </div>
                    @error('servicos_planejados')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <span class="form-label d-block">Serviços concluídos</span>
                    <div class="border rounded p-3 @error('servicos_concluidos') border-danger @enderror">
                        @forelse ($servicos as $servico)
                        <div class="form-check">
                            <input type="checkbox" name="servicos_concluidos[]" value="{{ $servico->id }}"
                                id="servico-concluido-{{ $servico->id }}" class="form-check-input"
                                @checked(in_array($servico->id, old('servicos_concluidos', $servicosConcluidosSelecionados)))>
                            <label for="servico-concluido-{{ $servico->id }}" class="form-check-label">{{ $servico->nome }}</label>
                        </div>
                        @empty
                        <p class="mb-0 text-muted">Nenhum serviço ativo cadastrado.</p>
                        @endforelse
                    </div>
                    @error('servicos_concluidos')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i> Atualizar Plano de Tratamento
                    </button>
                    <a href="{{ route('dentista.planos-tratamento.show', $planoTratamento->id) }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
