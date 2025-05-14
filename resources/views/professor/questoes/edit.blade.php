@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Questão do Exame: {{ $exame->titulo }}</h2>

    <form action="{{ route('professor.questoes.update', [$exame->id, $questao->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Texto da Questão</label>
            <textarea name="texto" class="form-control" required>{{ old('texto', $questao->texto) }}</textarea>
        </div>

        <div class="form-group">
            <label>Imagem (opcional)</label>
            @if($questao->imagem)
                <p><img src="{{ asset('storage/' . $questao->imagem) }}" width="200"></p>
            @endif
            <input type="file" name="imagem" class="form-control">
        </div>

        <div class="form-group">
            <label>Comentário Explicativo</label>
            <textarea name="comentario_explicativo" class="form-control">{{ old('comentario_explicativo', $questao->comentario_explicativo) }}</textarea>
        </div>

        <div class="form-group">
            <label>Pontuação</label>
            <input type="number" name="pontuacao" class="form-control" required min="1" value="{{ old('pontuacao', $questao->pontuacao) }}">
        </div>

        <div class="form-group">
            <label>Alternativas</label>
            @foreach($questao->alternativas as $i => $alternativa)
                <input type="text" name="alternativas[]" class="form-control mb-2" value="{{ old('alternativas.' . $i, $alternativa->texto) }}" required>
            @endforeach
        </div>

        <div class="form-group">
            <label>Alternativa Correta (0 para A, 1 para B...)</label>
            <input type="number" name="correta" class="form-control" required min="0" max="3" value="{{ old('correta', $questao->alternativas->search(fn($alt) => $alt->correta)) }}">
        </div>

        <button type="submit" class="btn btn-success">Atualizar</button>

    </form>
</div>
@endsection
