@extends('layout')

@section('titulo', 'Serviços')

@section('topbar')
<div>
    <h1 class="topbar-title">Serviços</h1>
    <p class="topbar-subtitle">Bem vindo, {{ auth()->user()->nome }}</p>
</div>

<div class="topbar-actions">
    <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
</div>
@endsection

@section('content')
<div class="page-content container-fluid py-4">

    <a href="{{ route('dentista.servicos.index') }}" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left me-1"></i> Voltar
    </a>

    <div class="mb-5">
        <p class="mb-1" style="font-size:11px; letter-spacing:3px; color:var(--azul-principal); text-transform:uppercase;">Editar</p>
        <h1><i class="bi bi-pencil-square me-2" style="color:var(--azul-principal);"></i>{{ $servico->nome }}</h1>
    </div>

    <div class="card" style="max-width: 480px;">
        <div class="card-body p-4">
            <form action="{{ route('dentista.servicos.update', $servico->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control @error('nome') is-invalid @enderror"
                        id="nome" name="nome"
                        value="{{ old('nome', $servico->nome) }}" required>
                    @error('nome')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection