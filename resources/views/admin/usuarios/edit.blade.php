@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Editar Usuário</h2>

    <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6">
                <label>Nome:</label>
                <input type="text" name="name" class="form-control" value="{{ $usuario->name }}" required>
                <label>Email:</label>
                <input type="email" name="email" class="form-control" value="{{ $usuario->email }}" required>
                <label>Função:</label>
                <select name="role" class="form-control">
                    <option value="admin" {{ $usuario->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="professor" {{ $usuario->role == 'professor' ? 'selected' : '' }}>Professor</option>
                    <option value="estudante" {{ $usuario->role == 'estudante' ? 'selected' : '' }}>Estudante</option>
                    <option value="candidato" {{ $usuario->role == 'candidato' ? 'selected' : '' }}>Candidato</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Número de Matrícula:</label>
                <input type="text" name="numero_matricula" class="form-control" value="{{ $usuario->numero_matricula }}">
                <label>Bilhete:</label>
                <input type="text" name="bilhete" class="form-control" value="{{ $usuario->bilhete }}">
                <label>Contacto:</label>
                <input type="text" name="contacto" class="form-control" value="{{ $usuario->contacto }}">
                <label>Data de Nascimento:</label>
                <input type="date" name="data_nascimento" class="form-control" value="{{ $usuario->data_nascimento }}">
                <label>Endereço:</label>
                <textarea name="endereco" class="form-control">{{ $usuario->endereco }}</textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Atualizar</button>
    </form>
</div>
@endsection
