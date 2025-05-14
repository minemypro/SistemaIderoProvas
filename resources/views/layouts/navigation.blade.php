@auth
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">Sistema Acadêmico</a>
  <div class="collapse navbar-collapse">
    <ul class="navbar-nav">
      @if(Auth::user()->role == 'admin')
        <li class="nav-item"><a class="nav-link" href="{{ route('usuarios.index') }}">Gerenciar Usuários</a></li>
      @endif
      <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
      <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST">@csrf
          <button class="btn btn-link nav-link" type="submit">Sair</button>
        </form>
      </li>
    </ul>
  </div>
</nav>
@endauth
