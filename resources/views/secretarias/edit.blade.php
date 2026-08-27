@extends('layout')

@section('titulo', 'Secretárias')

@section('topbar')
    <div>
        <h1 class="topbar-title">Secretárias</h1>
        <p class="topbar-subtitle">Bem vindo, {{ auth()->user()->nome }}</p>
    </div>

    <div class="topbar-actions">
        <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
    </div>
@endsection

@section('content')
    <div class="page-content container-fluid py-4">

        <a href="{{ route('dentista.secretarias.index') }}" class="btn btn-outline-secondary mb-4">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>

        <div class="card" style="max-width: 480px;">
            <div class="card-body p-4">
                <form action="{{ route('dentista.secretarias.update', $secretaria->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome"
                            value="{{ old('nome', $secretaria->nome) }}" required>
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email', $secretaria->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="senha" class="form-label">Nova senha</label>
                        <input type="password" class="form-control @error('senha') is-invalid @enderror" id="senha"
                            name="senha" minlength="6" maxlength="100" oninput="tamanhoMax(this); verificarMin(this)">
                        <div class="form-text">Deixe em branco para manter a senha atual.</div>
                        @error('senha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="id_filial" class="form-label">Filial</label>
                        <select class="form-select @error('id_filial') is-invalid @enderror" id="id_filial" name="id_filial"
                            required>
                            <option value="">Selecione uma filial</option>
                            @foreach($filiais as $filial)
                                <option value="{{ $filial->id }}" @selected(old('id_filial', $secretaria->id_filial) == $filial->id)>
                                    {{ $filial->cidade }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_filial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <input type="hidden" name="tipo" value="2">

                    <div class="d-flex gap-2">
                        <a href="{{ route('dentista.secretarias.index') }}" class="btn btn-outline-secondary flex-fill">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-check-lg me-1"></i> Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

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
        form.addEventListener("submit", function (event) {
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
    document.addEventListener('DOMContentLoaded', function () {
        const cpfInput = document.getElementById('cpf');
        if (cpfInput) {
            cpfInput.addEventListener('input', function (e) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        }
    });
</script>