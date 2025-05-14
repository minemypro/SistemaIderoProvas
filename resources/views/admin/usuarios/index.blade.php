@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Lista de Usuários</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <a href="{{ route('usuarios.create') }}" class="btn btn-success mb-3">Novo Usuário</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Função</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ ucfirst($usuario->role) }}</td>
                        <td>
                            <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-primary">Editar</a>
                            <a href="{{ route('usuarios.edit-password', $usuario->id) }}"
                                class="btn btn-warning btn-sm">Editar Senha</a>

                            <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Deseja realmente excluir este usuário?')">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
