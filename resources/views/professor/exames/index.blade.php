@extends('layouts.app')
@section('title', 'Dashboar Estudante')
@section('content')
<div class="container">
    <h2>Lista de Provas</h2>
    <a href="{{ route('professor.exames.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus-circle"></i> Nova Prova
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Título</th>
                <th>Disciplina</th>
                <th>Início</th>
                <th>Fim</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exames as $exame)
            <tr>
                <td>{{ $exame->titulo }}</td>
                <td>{{ $exame->disciplina }}</td>
                <td>{{ $exame->inicio }}</td>
                <td>{{ $exame->fim }}</td>
                <td>
                    <!-- Botão de Editar com ícone -->
                    <a href="{{ route('professor.exames.edit', $exame->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>

                    <!-- Formulário de Exclusão com ícone -->
                    <form action="{{ route('professor.exames.destroy', $exame->id) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Confirmar exclusão?')">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Paginação -->
    {{ $exames->links() }}
</div>
@endsection
