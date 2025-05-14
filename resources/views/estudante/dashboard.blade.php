@extends('layouts.app')
@section('title', 'Dashboard Estudante')

@section('content')
    <div class="container">
        <h1>Área do Estudante</h1>
        <h3>Exames Inscritos</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div id="aviso-importante" class="alert alert-warning d-flex align-items-center gap-2 small py-2 px-3 mb-4 shadow-sm"
            role="alert" style="max-width: 500px; margin: auto;">
            <i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>
            <div>
                <strong>Aviso:</strong> Você só poderá acessar a prova no horário programado. Não atualize, feche a aba ou
                troque de janela enquanto estiver fazendo a prova. ou será considerada <strong>fraude</strong> e você receberá
                <strong>0</strong>.
            </div>
        </div>
    </div>

    <script>
        setTimeout(function() {
            const aviso = document.getElementById('aviso-importante');
            if (aviso) {
                aviso.style.transition = "opacity 0.5s ease";
                aviso.style.opacity = 0;
                setTimeout(() => aviso.remove(), 500);
            }
        }, 30000); // 30 segundos
    </script>

    <table class="table">
        <thead>
            <tr>
                <th>Exame</th>
                <th>Data</th>
                <th>Status</th>
                <th>Nota Final</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($matriculas as $matricula)
                <tr>
                    <td>{{ $matricula->exame->titulo }}</td>
                    <td>{{ $matricula->exame->inicio->format('d/m/Y H:i') }}</td>
                    <td>
                        @if ($matricula->status === 'finalizado')
                            <span class="badge bg-success">Finalizado</span>
                        @elseif ($matricula->status === 'expirado')
                            <span class="badge bg-danger">Expirado</span>
                        @elseif ($matricula->status === 'aguardando')
                            <span class="badge bg-warning text-dark">Aguardando</span>
                        @else
                            <span class="badge bg-secondary">Em andamento</span>
                        @endif
                    </td>
                    <td>{{ $matricula->nota_final ?? '---' }}</td>
                    <td>
                        @if ($matricula->status === 'aguardando')
                            <a href="{{ route('estudante.prova', $matricula->exame_id) }}" class="btn btn-primary">
                                Iniciar Prova
                            </a>
                        @else
                            <span class="text-muted">Indisponível</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>

<script>
    function atualizarStatus() {
        $.ajax({
            url: '{{ route('status.matricula') }}',
            method: 'GET',
            success: function(response) {
                let tbody = $('table tbody');
                tbody.empty();

                response.matriculas.forEach(function(matricula) {
                    let status = '';
                    if (matricula.status === 'finalizado') {
                        status = '<span class="badge bg-success">Finalizado</span>';
                    } else if (matricula.status === 'expirado') {
                        status = '<span class="badge bg-danger">Expirado</span>';
                    } else if (matricula.status === 'aguardando') {
                        status = '<span class="badge bg-warning text-dark">Aguardando</span>';
                    } else {
                        status = '<span class="badge bg-secondary">Em andamento</span>';
                    }

                    let notaFinal = matricula.nota_final ?? '---';
                    let acao = (matricula.status === 'aguardando') ?
                        `<a href="/estudante/prova/${matricula.exame_id}" class="btn btn-primary">Iniciar Prova</a>` :
                        `<span class="text-muted">Indisponível</span>`;

                    let tr = `
                        <tr>
                            <td>${matricula.exame.titulo}</td>
                            <td>${new Date(matricula.exame.inicio).toLocaleString()}</td>
                            <td>${status}</td>
                            <td>${notaFinal}</td>
                            <td>${acao}</td>
                        </tr>
                    `;
                    tbody.append(tr);
                });
            },
            error: function(xhr, status, error) {
                console.error('Erro ao atualizar status:', error);
                // ALERTA REMOVIDO PARA NÃO EXIBIR MENSAGEM AO USUÁRIO
            }
        });
    }

    setInterval(atualizarStatus, 15000); // A cada 15 segundos
</script>
@endsection
