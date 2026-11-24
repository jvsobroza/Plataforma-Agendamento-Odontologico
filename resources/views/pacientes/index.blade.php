@extends('layout')

@section('titulo', 'Pacientes')

@section('topbar')
<div>
    <h1 class="topbar-title">Pacientes</h1>
    <p class="topbar-subtitle">Bem vindo, {{ auth()->user()->nome }}</p>
</div>

<div class="topbar-actions">
    <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
    <a href="{{ route('pacientes.create') }}" class="btn-primary-brand">
        <i class="bi bi-plus-lg"></i> Novo Paciente
    </a>
</div>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center px-4 py-3">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Pacientes</h5>
            <span class="badge badge-count">{{ count($pacientes) }}</span>
        </div>
    </div>

    <div class="card-body border-bottom px-4 py-3">
        <fieldset class="mb-3">
            <legend class="fs-6 mb-2">Pesquisar por:</legend>
            <div class="d-flex flex-wrap gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipoBuscaPaciente" id="buscarPorNome" value="nome" checked>
                    <label class="form-check-label" for="buscarPorNome">Nome</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipoBuscaPaciente" id="buscarPorCpf" value="cpf">
                    <label class="form-check-label" for="buscarPorCpf">CPF</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipoBuscaPaciente" id="buscarPorTelefone" value="telefone">
                    <label class="form-check-label" for="buscarPorTelefone">Telefone</label>
                </div>
            </div>
        </fieldset>
        <input type="search" id="buscarPaciente" class="form-control" placeholder="Buscar por nome...">
    </div>

    <div class="card-body p-0">
        @forelse($pacientes as $paciente)
        @if($loop->first)
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Idade</th>
                        <th>Telefone</th>
                        <th>Observações Médicas</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @endif
                    <tr>
                        <td class="ps-4">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="service-avatar">
                                    <i class="bi bi-briefcase"></i>
                                </div>
                                {{ $paciente->nome }}
                            </div>
                        </td>
                        <td>{{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $paciente->cpf) }}</td> <!-- https://gist.github.com/davidalves1/3c98ef866bad4aba3987e7671e404c1e -->
                        <td>{{ \Carbon\Carbon::parse($paciente->data_nascimento)->age }} Anos</td>
                        <td>{{ $paciente->telefone }}</td>
                        <td>{{ $paciente->observacoes_medicas ?? 'Nenhuma' }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('pacientes.show', $paciente->id) }}"
                                    class="btn btn-outline-primary btn-sm" title="Visualizar">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('pacientes.edit', $paciente->id) }}"
                                    class="btn btn-outline-primary btn-sm" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form action="{{ route('pacientes.destroy', $paciente->id) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Excluir este paciente?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @if($loop->last)
                </tbody>
            </table>
        </div>
        @endif
        @empty
        <div class="empty-state">
            <i class="bi bi-briefcase"></i>
            <p>Nenhum serviço registrado</p>
        </div>
        @endforelse
    </div>
</div>

<script>
    const campoBusca = document.getElementById('buscarPaciente');
    const todasAsLinhas = document.querySelectorAll('tbody tr');
    const opcoesBusca = document.querySelectorAll('input[name="tipoBuscaPaciente"]');
    const tiposBusca = {
        nome: {
            coluna: 2,
            placeholder: 'Buscar por nome...'
        },
        cpf: {
            coluna: 3,
            placeholder: 'Buscar por CPF...'
        },
        telefone: {
            coluna: 5,
            placeholder: 'Buscar por telefone...'
        }
    };
    const textoMinusculo = texto => texto.toLocaleLowerCase('pt-BR');

    const filtrarPacientes = () => {
        const tipoBusca = document.querySelector('input[name="tipoBuscaPaciente"]:checked').value;
        const consulta = campoBusca.value.trim();
        const textoBuscado = tipoBusca == 'nome' ?
            textoMinusculo(consulta) :
            consulta.replace(/\D/g, '');
        todasAsLinhas.forEach(linha => {
            const valorPaciente = linha.querySelector(`td:nth-child(${tiposBusca[tipoBusca].coluna})`).textContent;
            const valorNormalizado = tipoBusca == 'nome' ?
                textoMinusculo(valorPaciente) :
                valorPaciente.replace(/\D/g, ''); //se for número ou cpf remove tudo, se não deixa o nome em minúscula
            const corresponde = consulta == '' ||
                (textoBuscado != '' && valorNormalizado.includes(textoBuscado));

            linha.style.display = corresponde ? '' : 'none';
        });
    };
    campoBusca.addEventListener('input', filtrarPacientes);
    opcoesBusca.forEach(opcao => {
        opcao.addEventListener('change', function() {
            campoBusca.placeholder = tiposBusca[this.value].placeholder;
            filtrarPacientes();
        });
    });
</script>
@endsection