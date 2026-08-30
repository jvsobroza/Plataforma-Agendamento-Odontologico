<aside class="sidebar">
  
  <div class="sidebar-brand">
    <div class="sidebar-brand-badge">
      <img src="{{ asset('images/logo.png') }}" alt="Sante Oral Odontologia">
    </div>
  </div>

  <div class="sidebar-menu-label">MENU</div>

  <ul class="sidebar-nav">
    <li>
      @if (auth()->user()->tipo == 1)
      <a href="{{ route('dentista.index') }}" class="sidebar-link {{ request()->routeIs('dentista.index') ? 'active' : '' }}">
        <i class="bi bi-grid-fill"></i>
        <span>Dashboard</span>
      </a>
      @else
      <a href="{{ route('secretaria.index') }}" class="sidebar-link {{ request()->routeIs('secretaria.index') ? 'active' : '' }}">
        <i class="bi bi-grid-fill"></i>
        <span>Dashboard</span>
      </a>
      @endif
    </li>
    <li>
      <a href="{{ route('agendamentos.index') }}" class="sidebar-link {{ request()->routeIs('agendamentos.*') ? 'active' : '' }}">
        <i class="bi bi-calendar3"></i>
        <span>Agenda</span>
      </a>
    </li>
    <li>
      <a href="{{ route('pacientes.index') }}" class="sidebar-link {{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
        <i class="bi bi-person"></i>
        <span>Pacientes</span>
      </a>
    </li>
    @if (auth()->user()->tipo == 1)
    <li>
      <a href="{{ route('dentista.servicos.index') }}" class="sidebar-link {{ request()->routeIs('dentista.servicos.*') ? 'active' : '' }}">
        <i class="bi bi-currency-dollar"></i>
        <span>Serviços</span>
      </a>
    </li>
    <li>
      <a href="{{ route('dentista.filiais.index') }}" class="sidebar-link {{ request()->routeIs('dentista.filiais.*') ? 'active' : '' }}">
        <i class="bi bi-shop"></i>
        <span>Filiais</span>
      </a>
    </li>
    <li>
      <a href="{{ route('dentista.secretarias.index') }}" class="sidebar-link {{ request()->routeIs('dentista.secretarias.*') ? 'active' : '' }}">
        <i class="bi bi-person-vcard"></i>
        <span>Secretárias</span>
      </a>
    </li>
    @endif
  </ul>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <span class="sidebar-user-avatar">
        <i class="bi bi-person-fill"></i>
      </span>
      <span class="sidebar-user-name">{{ auth()->user()->nome }}</span>
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