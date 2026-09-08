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
        <p class="mb-1" style="font-size:11px; letter-spacing:3px; color:var(--azul-principal); text-transform:uppercase;">Novo Plano de Tratamento:</p>
        <h1><i class="bi bi-person-plus me-2" style="color:var(--azul-principal);"></i>Cadastrar Plano de Tratamento</h1>
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

    <div class="card" style="max-width: 640px;">
        <div class="card-body p-4">
            <form action="{{ route('dentista.planos-tratamento.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_paciente" value="{{ $paciente->id }}">
                <div class="mb-3">
                    <span class="detail-label">Paciente</span>
                    <div class="form-control bg-light">{{ $paciente->nome }}</div>
                </div>
                <input type="hidden" name="status" value="Em andamento">
                <div class="mb-3">
                    <label class="detail-label">Serviços planejados</label>
                    <div class="border rounded p-3 @error('servicos_planejados') border-danger @enderror">
                        @forelse ($servicos as $servico)
                        <div class="form-check mb-2">
                            <input type="checkbox" name="servicos_planejados[]" value="{{ $servico->id }}"
                                id="servico-{{ $servico->id }}" class="form-check-input"
                                @checked(in_array($servico->id, old('servicos_planejados', [])))>
                            <label for="servico-{{ $servico->id }}" class="form-check-label">{{ $servico->nome }}</label>
                        </div>
                        @empty
                        <p class="mb-0 text-muted">Nenhum serviço ativo cadastrado.</p>
                        @endforelse
                    </div>
                    @error('servicos_planejados')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary flex-fill">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-check-lg me-1"></i> Cadastrar
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection