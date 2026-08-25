@extends('layout')

@section('titulo', 'Filiais')

@section('topbar')
<div>
    <h1 class="topbar-title">Filiais</h1>
    <p class="topbar-subtitle">Bem vindo, {{ auth()->user()->nome }}</p>
</div>

<div class="topbar-actions">
    <span class="pill-date">{{ \Carbon\Carbon::today()->locale('pt_BR')->translatedFormat('d \d\e F, Y') }}</span>
    <a href="{{ route('dentista.filiais.create') }}" class="btn-primary-brand">
        <i class="bi bi-plus-lg"></i> Nova Filial
    </a>
</div>
@endsection

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center px-4 py-3">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Filiais</h5>
            <span class="badge badge-count">{{ count($filiais) }}</span>
        </div>
    </div>

    <div class="card-body p-0">
        @forelse($filiais as $filial)
        @if($loop->first)
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Cidade</th>
                        <th>Endereço</th>
                        <th>Datas de Atendimento</th>
                        <th>Serviços</th>
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
                                    <i class="fas fa-cut"></i>
                                </div>
                                {{ $filial->cidade }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="service-avatar">
                                    <i class="fas fa-cut"></i>
                                </div>
                                {{ $filial->endereco }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="service-avatar">
                                    <i class="fas fa-cut"></i>
                                </div>
                                <div>
                                    {{ !empty($filial->dias_nomes) ? implode(', ', $filial->dias_nomes) : 'Nenhum dia definido' }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="service-avatar">
                                    <i class="fas fa-cut"></i>
                                </div>
                                {{ $filial->servicos ?? 'Nenhum serviço' }} <!-- arrumar erro -->
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('dentista.filiais.edit', $filial->id) }}"
                                    class="btn btn-outline-primary btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('dentista.filiais.destroy', $filial->id) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Excluir esta filial?')">
                                        <i class="fas fa-trash"></i>
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
            <i class="fas fa-cut"></i>
            <p>Nenhuma filial registrada</p>
        </div>
        @endforelse
    </div>
</div>
@endsection