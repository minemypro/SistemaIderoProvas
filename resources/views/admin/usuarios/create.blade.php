@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Cadastrar Novo Usuário</h2>
    <form method="POST" action="{{ route('usuarios.store') }}">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <label>Nome:</label>
                <input type="text" name="name" class="form-control" required>
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required>
                <label>Senha:</label>
                <input type="password" name="password" class="form-control" required>
                <label>Função:</label>
                <select name="role" class="form-control">
                    <option value="admin">Admin</option>
                    <option value="professor">Professor</option>
                    <option value="estudante">Estudante</option>
                    <option value="candidato">Candidato</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Número de Matrícula:</label>
                <input type="text" name="numero_matricula" class="form-control">
                <label>Bilhete:</label>
                <input type="text" name="bilhete" class="form-control">
                <label>Contacto:</label>
                <input type="text" name="contacto" class="form-control">
                <label>Data de Nascimento:</label>
                <input type="date" name="data_nascimento" class="form-control">
                <label>Endereço:</label>
                <textarea name="endereco" class="form-control"></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-success mt-3">Salvar</button>
    </form>
</div>
@endsection
