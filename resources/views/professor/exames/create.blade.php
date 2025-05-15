@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="my-4">{{ isset($exame) ? 'Editar Prova' : 'Nova Prova' }}</h2>

    <form method="POST"
        action="{{ isset($exame) ? route('professor.exames.update', $exame->id) : route('professor.exames.store') }}">
        @csrf
        @if (isset($exame))
            @method('PUT')
        @endif

        <div class="row">
            <!-- Título -->
            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                <label for="titulo" class="form-label">
                    <i class="fas fa-heading fa-sm"></i> Título / curso
                </label>
                <input type="text" name="titulo" id="titulo" class="form-control"
                    value="{{ old('titulo', $exame->titulo ?? '') }}" required>
            </div>

            <!-- Descrição -->
            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                <label for="descricao" class="form-label">
                    <i class="fas fa-pencil-alt fa-sm"></i> Descrição
                </label>
                <textarea name="descricao" id="descricao" class="form-control" rows="3">{{ old('descricao', $exame->descricao ?? '') }}</textarea>
            </div>

            <!-- Disciplina -->
            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                <label for="disciplina" class="form-label">
                    <i class="fas fa-book fa-sm"></i> Disciplina
                </label>
                <input type="text" name="disciplina" id="disciplina" class="form-control"
                    value="{{ old('disciplina', $exame->disciplina ?? '') }}" required>
            </div>

            <!-- Navegação -->
            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                <label for="navegacao" class="form-label">
                    <i class="fas fa-arrows-alt-h fa-sm"></i> Navegação
                </label>
                <select name="navegacao" id="navegacao" class="form-control">
                    <option value="livre" {{ old('navegacao', $exame->navegacao ?? '') == 'livre' ? 'selected' : '' }}>Livre</option>
                    <option value="sequencial" {{ old('navegacao', $exame->navegacao ?? '') == 'sequencial' ? 'selected' : '' }}>Sequencial</option>
                </select>
            </div>

            <!-- Tentativas Permitidas -->
            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                <label for="tentativas_permitidas" class="form-label">
                    <i class="fas fa-sync fa-sm"></i> Tentativas Permitidas
                </label>
                <input type="number" name="tentativas_permitidas" id="tentativas_permitidas" class="form-control"
                    value="{{ old('tentativas_permitidas', $exame->tentativas_permitidas ?? 1) }}">
            </div>

            <!-- Pontuação Total -->
            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                <label for="pontuacao_total" class="form-label">
                    <i class="fas fa-percent fa-sm"></i> Pontuação Total
                </label>
                <input type="number" name="pontuacao_total" id="pontuacao_total" class="form-control"
                    value="{{ old('pontuacao_total', $exame->pontuacao_total ?? 0) }}">
            </div>

            <!-- Início -->
            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                <label for="inicio" class="form-label">
                    <i class="fas fa-calendar-alt fa-sm"></i> Início
                </label>
                <input type="datetime-local" name="inicio" id="inicio" class="form-control"
                    value="{{ old('inicio', isset($exame) ? $exame->inicio->format('Y-m-d\TH:i') : '') }}">
            </div>
  <!-- Fim -->
  <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
    <label for="fim" class="form-label">
        <i class="fas fa-calendar-check fa-sm"></i> Fim
    </label>
    <input type="datetime-local" name="fim" id="fim" class="form-control"
        value="{{ old('fim', isset($exame) ? $exame->fim->format('Y-m-d\TH:i') : '') }}">
</div>
            <!-- Feedback -->
            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                <label for="feedback" class="form-label">
                    <i class="fas fa-comment fa-sm"></i> Feedback/resultado do exame
                </label>
                <select name="feedback" id="feedback" class="form-control">
                    <option value="imediato" {{ old('feedback', $exame->feedback ?? '') == 'imediato' ? 'selected' : '' }}>Imediato</option>
                    <option value="pos-analise" {{ old('feedback', $exame->feedback ?? '') == 'pos-analise' ? 'selected' : '' }}>Pós-análise</option>
                </select>
            </div>



        </div>
        <br><br>
        <!-- Botão de Submit -->
        <div class="form-group mb-3">
            <button type="submit" class="btn btn-success w-100">
                <i class="fas fa-save"></i> {{ isset($exame) ? 'Atualizar' : 'Salvar' }}
            </button>
        </div>
    </form>
</div>
@endsection
