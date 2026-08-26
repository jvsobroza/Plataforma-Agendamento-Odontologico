@extends('layout')

@section('titulo', 'Pacientes')

@section('topbar')
<div>
    <h1 class="topbar-title">Pacientes</h1>
    <p class="topbar-subtitle">Bem vindo, {{ auth()->user()->nome }}</p>
</div>

<div class="topbar-actions">
    <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
</div>
@endsection

@section('content')
<div class="page-content container-fluid py-4">

    <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left me-1"></i> Voltar
    </a>

    <div class="mb-5">
        <p class="mb-1" style="font-size:11px; letter-spacing:3px; color:var(--azul-principal); text-transform:uppercase;">Novo Paciente:</p>
        <h1><i class="bi bi-person-plus me-2" style="color:var(--azul-principal);"></i>Cadastrar Paciente</h1>
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

            <form action="{{ route('pacientes.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="nome" class="detail-label">Nome</label>
                    <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror"
                        value="{{ old('nome') }}"
                        minlength="5" maxlength="255" oninput="tamanhoMax(this); verificarMin(this)" required>
                    @error('nome')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="cpf" class="detail-label">CPF</label>
                    <input type="text" name="cpf" id="cpf" class="form-control @error('cpf') is-invalid @enderror"
                        value="{{ old('cpf') }}" minlength="11" maxlength="11" oninput="tamanhoMax(this); verificarMin(this)"
                        placeholder="Somente números" required>
                    @error('cpf')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="data_nascimento" class="detail-label">Data de Nascimento</label>
                    <input type="date" name="data_nascimento" id="data_nascimento"
                        class="form-control @error('data_nascimento') is-invalid @enderror"
                        value="{{ old('data_nascimento') }}" required>
                    @error('data_nascimento')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="telefone" class="detail-label">Telefone</label>
                    <input type="text" name="telefone" id="telefone" class="form-control @error('telefone') is-invalid @enderror"
                        value="{{ old('telefone') }}" minlength="10" maxlength="15" oninput="tamanhoMax(this); verificarMin(this)" required>
                    @error('telefone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="observacoes_medicas" class="detail-label">Observações Médicas</label>
                    <textarea name="observacoes_medicas" id="observacoes_medicas" rows="4"
                        class="form-control @error('observacoes_medicas') is-invalid @enderror">{{ old('observacoes_medicas') }}</textarea>
                    @error('observacoes_medicas')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('pacientes.index') }}" class="btn btn-outline-secondary flex-fill">
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

<script>
    function tamanhoMax(e) {
        if (e.value.length > e.maxLength)
            e.value = e.value.slice(0, e.maxLength)
    }

    function verificarMin(e) {
        const min = e.getAttribute('minlength');
        if (e.value.length > 0 && e.value.length < min) {
            e.style.borderColor = "red";
            e.style.borderWidth = "2px";
        } else {
            e.style.borderColor = "";
            e.style.borderWidth = "";
        }
    }

    document.addEventListener("DOMContentLoaded", (event) => {
        const form = document.querySelector("form");
        const inputs = form.querySelectorAll("input");
        form.addEventListener("submit", function(event) {
            let formValid = true;
            inputs.forEach(e => {
                if (e.value.length > 0 && e.value.length < e.getAttribute('minlength')) {
                    formValid = false;
                }
            });
            if (!formValid) {
                event.preventDefault();
                alert("Preencha todos os campos corretamente!");
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const cpfInput = document.getElementById('cpf');
        if (cpfInput) {
            cpfInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        }
    });
</script>
@endsection