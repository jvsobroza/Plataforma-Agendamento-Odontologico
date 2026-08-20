<aside class="sidebar">

    <div class="sidebar-brand">
        <div class="sidebar-brand-badge">
            <img src="{{ asset('images/logo.png') }}" alt="Sante Oral Odontologia">
        </div>
    </div>

    <div class="sidebar-menu-label">MENU</div>

    <ul class="sidebar-nav">
        <li>
            //adicionar pra secretária
            <a href="{{ route('dentista.index') }}" class="sidebar-link">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('agendamentos.index') }}" class="sidebar-link">
                <i class="bi bi-calendar3"></i>
                <span>Agenda</span>
            </a>
        </li>
        <li>
            <a href="{{ route('pacientes.index') }}" class="sidebar-link">
                <i class="bi bi-person"></i>
                <span>Pacientes</span>
            </a>
        </li>
        <li>
            <a href="{{ route('servicos.index') }}" class="sidebar-link">
                <i class="bi bi-currency-dollar"></i>
                <span>Serviços</span>
            </a>
        </li>
        <li>
            //Verificar erro na rota
            <a href="{{ route('dentista.filiais.index') }}" class="sidebar-link">
                <i class="bi bi-shop"></i>
                <span>Filiais</span>
            </a>
        </li>
        <li>
            //verificar como modificar
            <a href="{{ route('dentista.secretarias.index') }}" class="sidebar-link">
                <i class="bi bi-person-vcard"></i>
                <span>Secretárias</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <span class="sidebar-user-avatar">
                <i class="bi bi-person-fill"></i>
            </span>
            <span class="sidebar-user-name">{{ auth()->user()->name ?? 'Usuário' }}</span>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form">
            @csrf
            <button type="submit" class="sidebar-logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sair</span>
            </button>
        </form>
    </div>

</aside>