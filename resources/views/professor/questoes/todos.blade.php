@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-file-alt"></i> Todos os Exames com Questões
            </h5>
        </div>

        <div class="card-body">

            <form method="GET" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Buscar por título" value="{{ request('titulo') }}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-book"></i></span>
                            <input type="text" name="disciplina" id="disciplina" class="form-control" placeholder="Buscar por disciplina" value="{{ request('disciplina') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-success w-100">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table id="tabela-exames" class="table table-sm table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-heading"></i> Título</th>
                            <th><i class="fas fa-book-reader"></i> Disciplina</th>
                            <th><i class="fas fa-calendar-alt"></i> Início</th>
                            <th><i class="fas fa-star"></i> Máx. Pontuação</th>
                            <th><i class="fas fa-list-ol"></i> Questões</th>
                            <th><i class="fas fa-cogs"></i> Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exames as $exame)
                        <tr>
                            <td>{{ $exame->titulo }}</td>
                            <td>{{ $exame->disciplina }}</td>
                            <td>{{ $exame->inicio->format('d/m/Y H:i') }}</td>
                            <td>{{ $exame->pontuacao_total }} pts</td>
                            <td>
                                <span class="badge bg-info">{{ $exame->questoes->count() }}</span>
                            </td>
                            <td>
                                <a href="{{ route('professor.exames.show', $exame->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Ver Questões
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- jQuery UI Autocomplete -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(function() {
    const titulos = @json(\App\Models\Exame::distinct()->pluck('titulo'));
    const disciplinas = @json(\App\Models\Exame::distinct()->pluck('disciplina'));

    $('#titulo').autocomplete({ source: titulos });
    $('#disciplina').autocomplete({ source: disciplinas });

    $('#tabela-exames').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        }
    });
});
</script>
@endsection
