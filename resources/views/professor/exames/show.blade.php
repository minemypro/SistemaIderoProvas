@extends('layouts.app')
@section('title', content: 'Dashboar Exames')
@section('content')
<div class="container">
    <h2 class="mb-4 text-center">
        <i class="bi bi-journal-text me-2"></i>Exame: {{ $exame->titulo }}
    </h2>

    <div class="d-flex justify-content-between flex-wrap align-items-start mb-4">
        <div class="mb-2">
            <p><i class="bi bi-book me-1 text-primary"></i><strong>Disciplina:</strong> {{ $exame->disciplina }}</p>
            <p><i class="bi bi-clock me-1 text-primary"></i><strong>Início:</strong> {{ $exame->inicio->format('d/m/Y H:i') }}</p>
            <p><i class="bi bi-clock-history me-1 text-primary"></i><strong>Fim:</strong> {{ $exame->fim->format('d/m/Y H:i') }}</p>
            <p><i class="bi bi-graph-up me-1 text-primary"></i><strong>Pontuação Total:</strong> {{ $exame->pontuacao_total }} pts</p>
            <p><i class="bi bi-repeat me-1 text-primary"></i><strong>Tentativas Permitidas:</strong> {{ $exame->tentativas_permitidas }}</p>
        </div>

        <a href="{{ route('professor.questoes.create', $exame->id) }}" class="btn btn-sm btn-success mt-2">
            <i class="bi bi-plus-circle me-1"></i> Nova Questão
        </a>
    </div>

    <h4 class="mb-3"><i class="bi bi-list-ul me-1"></i> Questões:</h4>

    @forelse ($exame->questoes as $questao)
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="bi bi-question-circle me-1 text-secondary"></i>{{ $loop->iteration }}. {{ $questao->texto }}
                </span>
                <div class="d-flex align-items-center">
                    <span class="badge bg-info text-dark me-2"><i class="bi bi-award me-1"></i>{{ $questao->pontuacao }} pts</span>
                    <a href="{{ route('professor.questoes.edit', [$exame->id, $questao->id]) }}" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                       Editar <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('professor.questoes.destroy', [$exame->id, $questao->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta questão?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Excluir">
                            <i class="bi bi-trash"></i>Excluir
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if ($questao->imagem)
                    <img src="{{ asset('storage/' . $questao->imagem) }}" class="img-thumbnail mb-2" style="max-width: 200px;">
                @endif
                <ul class="list-group list-group-flush">
                    @foreach ($questao->alternativas as $alternativa)
                        <li class="list-group-item d-flex align-items-center {{ $alternativa->correta ? 'list-group-item-success' : '' }}">
                            <i class="bi {{ $alternativa->correta ? 'bi-check-circle-fill text-success' : 'bi-dot text-muted' }} me-2" style="font-size: 1.2rem;"></i>
                            {{ $alternativa->texto }}
                        </li>
                    @endforeach
                </ul>
                @if ($questao->comentario_explicativo)
                    <p class="mt-2 text-muted small"><i class="bi bi-info-circle me-1"></i> {{ $questao->comentario_explicativo }}</p>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-warning text-center">
            <i class="bi bi-exclamation-circle me-1"></i> Nenhuma questão cadastrada neste exame.
        </div>
    @endforelse
</div>
@endsection
