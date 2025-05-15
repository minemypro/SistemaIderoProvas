@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Cadastrar Novo Usuário</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('usuarios.store') }}">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <label>Nome:</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>

                <label>Email:</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>

                <label>Senha:</label>
                <input type="password" name="password" class="form-control" required>

                <label>Confirmar Senha:</label>
                <input type="password" name="password_confirmation" class="form-control" required>

                <label>Função:</label>
                <select name="role" class="form-control">
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="professor" {{ old('role') == 'professor' ? 'selected' : '' }}>Professor</option>
                    <option value="estudante" {{ old('role') == 'estudante' ? 'selected' : '' }}>Estudante</option>
                    <option value="candidato" {{ old('role') == 'candidato' ? 'selected' : '' }}>Candidato</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Número de Matrícula:</label>
                <input type="text" name="numero_matricula" class="form-control" value="{{ old('numero_matricula') }}">

                <label>Bilhete:</label>
                <input type="text" name="bilhete" class="form-control" value="{{ old('bilhete') }}">

                <label>Contacto:</label>
                <input type="text" name="contacto" class="form-control" value="{{ old('contacto') }}">

                <label>Data de Nascimento:</label>
                <input type="date" name="data_nascimento" class="form-control" value="{{ old('data_nascimento') }}">

                <label>Endereço:</label>
                <textarea name="endereco" class="form-control">{{ old('endereco') }}</textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-success mt-3">Salvar</button>
    </form>
</div>
@endsection
