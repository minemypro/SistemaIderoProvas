@extends('layouts.app')

@section('content')

<div class="container py-4">

{{-- MENSAGENS DE SUCESSO E ERRO --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-4">
    <h4>
        <i class="fas fa-user-check text-primary me-2"></i> Matrícula no Exame:
        <span class="text-dark">{{ $exame->titulo }}</span>
    </h4>
</div>

<form action="{{ route('professor.matriculas.store') }}" method="POST">
    @csrf
    <input type="hidden" name="exame_id" value="{{ $exame->id }}">

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;"></th>
                    <th><i class="fas fa-user me-1"></i> Estudante</th>
                    <th><i class="fas fa-envelope me-1"></i> Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estudantes as $estudante)
                    <tr>
                        <td>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="estudantes[]"
                                       value="{{ $estudante->id }}"
                                       {{ in_array($estudante->id, $matriculados) ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>{{ $estudante->name }}</td>
                        <td>{{ $estudante->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <button class="btn btn-primary" type="submit">
            <i class="fas fa-save me-1"></i> Salvar/Remover Matrícula
        </button>
    </div>
</form>
</div> @endsection
