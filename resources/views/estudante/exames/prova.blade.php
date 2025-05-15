@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $tentativa->exame->titulo }}</h2>
    <div id="cronometro"></div>

    <form method="POST" action="{{ route('estudante.finalizar', $tentativa->id) }}">
        @csrf
        @if($questoes->isNotEmpty())  <!-- Verifica se existem questões -->
            @foreach ($questoes as $q)
                <div class="card mb-3">
                    <div class="card-header">
                        {{ $q->texto }}
                    </div>
                    <div class="card-body">
                        @foreach ($q->alternativas->shuffle() as $alt)  <!-- Embaralha as alternativas -->
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="questao_{{ $q->id }}"
                                       value="{{ $alt->id }}">
                                <label class="form-check-label">{{ $alt->texto }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <p>Não há questões disponíveis para este exame.</p>
        @endif
        <button class="btn btn-success" type="submit">Finalizar Prova</button>
    </form>
</div>

<script>
    let tempoRestante = {{ \Carbon\Carbon::parse($tentativa->fim)->diffInSeconds(now()) }};
    const cronometroEl = document.getElementById('cronometro');

    const intervalo = setInterval(() => {
        if (tempoRestante <= 0) {
            clearInterval(intervalo);
            alert("Tempo esgotado. A prova será finalizada.");
            document.querySelector('form').submit();
        } else {
            let minutos = Math.floor(tempoRestante / 60);
            let segundos = tempoRestante % 60;
            cronometroEl.innerText = `Tempo restante: ${minutos}m ${segundos}s`;
            tempoRestante--;
        }
    }, 1000);

    // Prevenir recarregamento
    window.onbeforeunload = () => "Você perderá seu progresso se sair da página.";

    // Bloqueio de troca de aba
    document.addEventListener("visibilitychange", () => {
        if (document.hidden) {
            alert("Troca de aba detectada! Isso pode ser registrado.");
        }
    });
</script>
@endsection
