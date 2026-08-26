@extends('layout')

@section('titulo', 'Secretarias')

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
        <i class="fas fa-arrow-left me-1"></i> Voltar
    </a>

    <div class="card" style="max-width: 480px;">
        <div class="card-body p-4">
            <form action="{{ route('dentista.servicos.store') }}" method="POST">
                @csrf
                @method('POST')

                <div class="mb-4">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control @error('nome') is-invalid @enderror"
                        id="nome" name="nome"
                        value="{{ old('nome') }}" required>
                    @error('nome')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Criar Serviço
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection