<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Resultados</title>
    <style>
        @page {
            margin: 100px 30px 80px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
        }

        .logo {
            float: left;
            width: 80px;
        }

        .institution-info {
            text-align: center;
        }

        .institution-info h1 {
            font-size: 18px;
            margin: 0;
        }

        .institution-info p {
            margin: 2px 0;
            font-size: 12px;
        }

        h2.title {
            margin-top: 0;
            text-align: center;
            font-size: 16px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .generated {
            text-align: right;
            margin-top: 15px;
            font-style: italic;
        }

        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    <!-- Cabeçalho para múltiplas páginas -->
    <header>
        <div class="logo">
            <!-- <img src="{{ public_path('logo.png') }}" width="80"> -->
        </div>
        <div class="institution-info">
            <h1>INSTITUTO SUPERIOR DEOLINDA RODRIGUES</h1>
            <p>NIF: 123456789</p>
            <p>Relatório de Desempenho Académico</p>
        </div>
        <div class="clear"></div>
    </header>

    <!-- Rodapé -->
    <footer>
        <div>
            <strong>Instituto Superior Deolinda Rodrigues</strong> - Todos os direitos reservados<br>
            Tel: (+244) 999-999-999 | Email: idero@instituto.ao<br>
            Localização: Bairro nova vida, Luanda - Angola
        </div>
    </footer>

    <!-- Conteúdo -->
    <main>
        <h2 class="title">Relatório de Resultados dos Estudantes</h2>

        <table>
            <thead>
                <tr>
                    <th>Estudante</th>
                    <th>Exame</th>
                    <th>Nota Final</th>
                    <th>Status</th>
                    <th>Início</th>
                    <th>Fim</th>
                    <th>Tentativa Nº</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tentativas as $t)
                    <tr>
                        <td>{{ $t->user->name }}</td>
                        <td>{{ $t->exame->titulo }}</td>
                        <td>{{ $t->nota_final }}</td>
                        <td>{{ ucfirst($t->status) }}</td>
                        <td>{{ \Carbon\Carbon::parse($t->inicio)->format('d/m/Y H:i') }}</td>
                        <td>{{ $t->fim ? \Carbon\Carbon::parse($t->fim)->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $t->tentativa_numero }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Nenhum resultado encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="generated">
            Gerado em {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </div>
    </main>

</body>
</html>
