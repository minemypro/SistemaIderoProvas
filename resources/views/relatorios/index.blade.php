@extends('layouts.app')

@section('content')
<div class="container">

    <form method="GET" action="{{ route('relatorio') }}" class="mb-4">
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-person-lines-fill"></i> Estudante</label>
                <select name="estudante_id" class="form-select form-select-sm">
                    <option value="">-- Todos --</option>
                    @foreach($estudantes as $e)
                        <option value="{{ $e->id }}" {{ request('estudante_id') == $e->id ? 'selected' : '' }}>
                            {{ $e->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-journal-text"></i> Exame</label>
                <select name="exame_id" class="form-select form-select-sm">
                    <option value="">-- Todos --</option>
                    @foreach($exames as $ex)
                        <option value="{{ $ex->id }}" {{ request('exame_id') == $ex->id ? 'selected' : '' }}>
                            {{ $ex->titulo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label"><i class="bi bi-list-nested"></i> Por página</label>
                <select name="per_page" class="form-select form-select-sm">
                    @foreach([5, 10, 20, 50] as $num)
                        <option value="{{ $num }}" {{ request('per_page', 10) == $num ? 'selected' : '' }}>
                            {{ $num }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-funnel-fill"></i> Filtrar
                </button>
            </div>
        </div>
    </form>

    @if($tentativas->count())
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th><i class="bi bi-person"></i> Estudante</th>
                    <th><i class="bi bi-book"></i> Exame</th>
                    <th><i class="bi bi-award"></i> Nota Final</th>
                    <th><i class="bi bi-flag"></i> Status</th>
                    <th><i class="bi bi-clock"></i> Início</th>
                    <th><i class="bi bi-clock-history"></i> Fim</th>
                    <th><i class="bi bi-arrow-repeat"></i> Tentativa Nº</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tentativas as $t)
                <tr>
                    <td>{{ $t->user->name }}</td>
                    <td>{{ $t->exame->titulo }}</td>
                    <td><span class="badge bg-success">{{ $t->nota_final }}</span></td>
                    <td>
                        <span class="badge bg-{{ $t->status == 'aprovado' ? 'success' : ($t->status == 'reprovado' ? 'danger' : 'secondary') }}">
                            {{ ucfirst($t->status) }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($t->inicio)->format('d/m/Y H:i') }}</td>
                    <td>{{ $t->fim ? \Carbon\Carbon::parse($t->fim)->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $t->tentativa_numero }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $tentativas->appends(request()->query())->links() }}
    </div>

    @else
        <div class="alert alert-info mt-3"><i class="bi bi-info-circle"></i> Nenhum resultado encontrado.</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-4">
        <h4 class="mb-0"><i class="bi bi-bar-chart-line-fill"></i> Relatório de Resultados</h4>
        <a href="{{ route('relatorio.pdf', request()->query()) }}" target="_blank" class="btn btn-secondary btn-sm">
            <i class="bi bi-file-earmark-pdf-fill"></i> Imprimir PDF
        </a>
    </div>
</div>
@endsection
