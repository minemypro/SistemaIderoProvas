@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ isset($exame) ? 'Editar Prova' : 'Nova Prova' }}</h2>

    <form method="POST"
        action="{{ isset($exame) ? route('professor.exames.update', $exame->id) : route('professor.exames.store') }}">
        @csrf
        @if (isset($exame))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="titulo">Título <i class="fas fa-heading"></i></label>
                    <input type="text" name="titulo" class="form-control form-control-sm"
                        value="{{ old('titulo', $exame->titulo ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição <i class="fas fa-pencil-alt"></i></label>
                    <textarea name="descricao" class="form-control form-control-sm">{{ old('descricao', $exame->descricao ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="disciplina">Disciplina <i class="fas fa-book"></i></label>
                    <input type="text" name="disciplina" class="form-control form-control-sm"
                        value="{{ old('disciplina', $exame->disciplina ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="navegacao">Navegação <i class="fas fa-route"></i></label>
                    <select name="navegacao" class="form-control form-control-sm">
                        <option value="livre" {{ old('navegacao', $exame->navegacao ?? '') == 'livre' ? 'selected' : '' }}>Livre</option>
                        <option value="sequencial" {{ old('navegacao', $exame->navegacao ?? '') == 'sequencial' ? 'selected' : '' }}>Sequencial</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tentativas_permitidas">Tentativas Permitidas <i class="fas fa-sync-alt"></i></label>
                    <input type="number" name="tentativas_permitidas" class="form-control form-control-sm"
                        value="{{ old('tentativas_permitidas', $exame->tentativas_permitidas ?? 1) }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="pontuacao_total">Pontuação Total <i class="fas fa-chart-line"></i></label>
                    <input type="number" name="pontuacao_total" class="form-control form-control-sm"
                        value="{{ old('pontuacao_total', $exame->pontuacao_total ?? 0) }}">
                </div>

                <div class="form-group">
                    <label for="feedback">Feedback <i class="fas fa-comments"></i></label>
                    <select name="feedback" class="form-control form-control-sm">
                        <option value="imediato" {{ old('feedback', $exame->feedback ?? '') == 'imediato' ? 'selected' : '' }}>Imediato</option>
                        <option value="pos-analise" {{ old('feedback', $exame->feedback ?? '') == 'pos-analise' ? 'selected' : '' }}>Pós-análise</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="inicio">Início <i class="fas fa-calendar-check"></i></label>
                    <input type="datetime-local" name="inicio" class="form-control form-control-sm"
                        value="{{ old('inicio', isset($exame) ? $exame->inicio->format('Y-m-d\TH:i') : '') }}">
                </div>

                <div class="form-group">
                    <label for="fim">Fim <i class="fas fa-calendar-times"></i></label>
                    <input type="datetime-local" name="fim" class="form-control form-control-sm"
                        value="{{ old('fim', isset($exame) ? $exame->fim->format('Y-m-d\TH:i') : '') }}">
                </div>
            </div>
        </div>

        <div class="mt-3 text-right">
            <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-save"></i> {{ isset($exame) ? 'Atualizar' : 'Salvar' }}
            </button>
        </div>
    </form>
</div>
@endsection
