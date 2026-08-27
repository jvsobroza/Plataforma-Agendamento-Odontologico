@extends('layout')

@section('titulo', 'Secretarias')

@section('topbar')
    <div>
        <h1 class="topbar-title">Secretárias</h1>
        <p class="topbar-subtitle">Bem vindo, {{ auth()->user()->nome }}</p>
    </div>

    <div class="topbar-actions">
        <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
        <a href="{{ route('dentista.secretarias.create') }}" class="btn-primary-brand">
            <i class="bi bi-plus-lg"></i> Nova Secretária
        </a>
    </div>
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center px-4 py-3">
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Secretárias</h5>
                <span class="badge badge-count">{{ count($secretaria) }}</span>
            </div>
        </div>

        <div class="card-body p-0">
            @forelse($secretaria as $user)
                @if($loop->first)
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Nome</th>
                                    <th>E-mail</th>
                                    <th>Filial</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                @endif
                            <tr>
                                <td class="ps-4"><strong>{{ $user->nome }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->filial->cidade ?? 'Não especificada' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('dentista.secretarias.edit', $user->id) }}"
                                            class="btn btn-outline-primary btn-sm" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('dentista.secretarias.destroy', $user->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Tem certeza que deseja excluir esta secretária?')">
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
                    <i class="bi bi-person-x"></i>
                    <p>Nenhuma secretária cadastrada</p>
                </div>
            @endforelse
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center px-4 py-3">
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Secretárias Desativadas</h5>
                <span class="badge badge-count">{{ count($secretariaDesativada) }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            @forelse($secretariaDesativada as $user)
                @if($loop->first)
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Nome</th>
                                    <th>E-mail</th>
                                    <th>Filial</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                @endif
                            <tr>
                                <td class="ps-4"><strong>{{ $user->nome }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->filial->cidade ?? 'Não especificada' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('dentista.secretarias.edit', $user->id) }}"
                                            class="btn btn-outline-primary btn-sm" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('dentista.secretarias.restore', $user->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-success btn-sm"
                                                title="Reativar" onclick="return confirm('Deseja reativar esta secretária?')">
                                                <i class="bi bi-person-check-fill"></i>
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
                    <i class="bi bi-person-x"></i>
                    <p>Nenhuma secretária desativada</p>
                </div>
            @endforelse
        </div>
    </div>
    </div>
@endsection