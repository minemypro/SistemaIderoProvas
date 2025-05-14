<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema SIPRODEL</title>

    <!-- Fonte -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .welcome-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(to right, #006099, #0099cc);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .welcome-box {
            background-color: rgba(255, 255, 255, 0.95);
            color: #2c3e50;
            padding: 40px;
            border-radius: 15px;
            max-width: 600px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .welcome-box h1 {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .welcome-box p {
            font-size: 1.1rem;
            margin: 20px 0;
        }

        .welcome-box .btn {
            padding: 10px 20px;
            font-size: 1rem;
            margin: 5px;
        }
    </style>
</head>
<body>

<div class="welcome-container">
    <div class="welcome-box">
        <img src="{{ asset('images/idero_lofg.png') }}" alt="Sua Imagem" class="rounded" width="100" height="100">
        <h1>Bem-vindo ao Sistema de Provas Online</h1>
        <p>Gerencie exames, crie questões e acompanhe o desempenho acadêmico com facilidade e segurança.</p>

        @if (Route::has('login'))
            <div class="mt-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-success">
                        <i class="fas fa-tachometer-alt"></i> Acessar Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>


                @endauth
            </div>
        @endif
    </div>
</div>

</body>
</html>
