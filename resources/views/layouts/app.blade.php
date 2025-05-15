<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-summary {
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-weight: bold;
        }

        .bg-clientes {
            background-color: #1abc9c;
        }

        .bg-ativos {
            background-color: #9b59b6;
        }

        .bg-concluidos {
            background-color: #27ae60;
        }

        .bg-perdidos {
            background-color: #e74c3c;
        }

        .bg-descartados {
            background-color: #f39c12;
        }

        .sidebar {
            background-color: #2c3e50;
            height: 100vh;
            color: white;
            padding-top: 20px;
            transition: all 0.4s ease;
            position: fixed;
            width: 250px;
            z-index: 1000;
            top: 0;
            left: 0;
            overflow-y: auto;
        }

        .sidebar.closed {
            left: -250px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .sidebar .active,
        .sidebar .logout {
            background-color: #006099;
        }

        .sidebar .logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .sidebar .logo img {
            max-width: 80px;
            height: auto;
            margin-bottom: 10px;
        }

        .sidebar h4 {
            margin-top: 10px;
            font-weight: bold;
        }

        .topbar {
            background-color: #006099;
            color: white;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .topbar .btn {
            background-color: #67c9ce;
            color: black;
            transition: all 0.3s;
        }

        .topbar .btn:hover {
            background-color: black;
            color: white;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: white;
            margin-right: 15px;
            transition: transform 0.4s ease;
        }

        .sidebar-toggle:hover {
            transform: rotate(90deg);
        }

        .main-content {
            margin-left: 250px;
            transition: margin-left 0.4s ease;
        }

        .main-content.full-width {
            margin-left: 0;
        }

        footer {
            background-color: #2c3e50;
            color: white;
            padding: 15px 10px;
            text-align: center;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>

    <title>Sistema SIPRODEL</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-..." crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />

    <!-- Custom Styles -->
    <link rel="stylesheet" href="path_to_custom_styles.css"> <!-- Adicione o link para o seu CSS customizado -->

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-light">
    <div class="min-h-screen">

        @if(Auth::check())
            <nav class="sidebar" id="sidebar">
                <div class="logo text-center">
                    <img src="{{ asset('images/idero_lofg.png') }}" alt="Sua Imagem" class="rounded" width="100"
                        height="100">
                    <h4>Inst. Acadêmica</h4>
                </div>
                <ul class="navbar-nav">
                    <li><a href="{{ route('dashboard') }}" class="active"><i class="fas fa-chalkboard"></i> Dashboard</a>
                    </li>
                    @if(Auth::user()->role === 'admin')
                        <li><a href="{{ route('usuarios.index') }}"><i class="fas fa-users-cog"></i> Gerenciar Usuários</a></li>
                    @endif
                    @if(in_array(Auth::user()->role, ['professor', 'admin']))
                        <li><a href="{{ route('professor.exames.index') }}"><i class="fas fa-book"></i> Meus Exames</a></li>
                        <li><a href="{{ route('professor.exames_questoes.index', ['exame' => 1]) }}"><i
                                    class="fas fa-question-circle"></i> Perguntas para Prova</a></li>

                        <li><a href="{{ route('professor.matriculas.index') }}"><i class="fas fa-user-plus"></i> Matricular
                                Estudante</a></li>

                        {{-- Botão de Relatório para professor e admin --}}
                        <li><a href="{{ route('relatorio') }}"><i class="fas fa-file-alt"></i> Relatórios</a></li>
                    @endif
                    <li><a href="{{ route('profile.edit') }}"><i class="fas fa-user"></i> Meu Perfil</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-link nav-link logout" type="submit"><i class="fas fa-sign-out-alt"></i>
                                Sair </button>
                        </form>
                    </li>
                </ul>

            </nav>

        @endif

        <div class="main-content" id="mainContent">
            <header class="topbar">
                <div>
                    <button class="sidebar-toggle" id="toggleSidebar"><i class="fas fa-bars"></i></button>
                    <span>{{ Auth::user()->name }} - Sistema Acadêmico</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-link nav-link logout" type="submit"><i class="fas fa-sign-out-alt"></i> Sair
                        do sistema</button>
                </form>
            </header>

            <div class="container-fluid px-4">
                @hasSection('header')
                    <header class="bg-white shadow mb-3">
                        <div class="container py-4">
                            @yield('header')
                        </div>
                    </header>
                @endif

                <main class="container">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-..."
        crossorigin="anonymous"></script>

    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('closed');
            mainContent.classList.toggle('full-width');
        });

        document.addEventListener('click', function (event) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = toggleBtn.contains(event.target);

            if (window.innerWidth <= 768 && !isClickInsideSidebar && !isClickOnToggle && !sidebar.classList.contains('closed')) {
                sidebar.classList.add('closed');
                mainContent.classList.add('full-width');
            }
        });

        function adjustSidebarOnResize() {
            if (window.innerWidth <= 768) {
                sidebar.classList.add('closed');
                mainContent.classList.add('full-width');
            } else {
                sidebar.classList.remove('closed');
                mainContent.classList.remove('full-width');
            }
        }

        window.addEventListener('load', adjustSidebarOnResize);
        window.addEventListener('resize', adjustSidebarOnResize);
    </script>
</body>

</html>
