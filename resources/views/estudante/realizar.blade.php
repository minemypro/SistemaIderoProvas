@extends('layouts.app')
@section('title', 'Dashboar Provas')
@section('content')
    @php
        use Carbon\Carbon;
        $agora = Carbon::now()->timezone(config('app.timezone'));
        $inicio = Carbon::parse($exame->inicio)->timezone(config('app.timezone'));
        $fim = Carbon::parse($exame->fim)->timezone(config('app.timezone'));
        $estado = $agora->lt($inicio) ? 'aguardando' : ($agora->gt($fim) ? 'encerrado' : 'liberado');
    @endphp

    <div class="container">
        <h2 class="mb-3">{{ $exame->titulo }}</h2>

        <div class="alert alert-secondary">
            <strong>Início:</strong> {{ $inicio->format('d/m/Y H:i') }} |
            <strong>Fim:</strong> {{ $fim->format('d/m/Y H:i') }}
        </div>

        @if ($estado === 'aguardando')
            <div class="alert alert-warning">A prova ainda não começou. Aguarde...</div>
            <div id="contador_inicio" class="alert alert-info text-center fw-bold fs-4"></div>

            <script>
                const inicio = new Date("{{ $inicio->format('Y-m-d H:i:s') }}").getTime();
                const timer = setInterval(() => {
                    const agora = new Date().getTime();
                    const diff = inicio - agora;
                    if (diff <= 0) {
                        clearInterval(timer);
                        location.reload();
                    } else {
                        const h = Math.floor(diff / (1000 * 60 * 60));
                        const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        const s = Math.floor((diff % (1000 * 60)) / 1000);
                        document.getElementById("contador_inicio").innerText = `A prova começará em: ${h}h ${m}m ${s}s`;
                    }
                }, 1000);
            </script>
        @elseif ($estado === 'encerrado')
            <div class="alert alert-danger">Este exame já foi encerrado. O prazo de realização expirou.</div>
        @else
            @php
                $tentativa = \App\Models\TentativaExame::firstOrCreate(
                    ['user_id' => Auth::id(), 'exame_id' => $exame->id, 'status' => 'em andamento'],
                    [
                        'tentativa_numero' => 1,
                        'inicio' => Carbon::now(),
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ],
                );
                $questoes = $exame->questoes->shuffle();
            @endphp

            <div id="cronometro" class="alert alert-info"></div>

            <form id="provaForm" method="POST" action="{{ route('estudante.finalizar', $exame->id) }}">
                @csrf
                <input type="hidden" name="tentativa_id" value="{{ $tentativa->id }}">

                @foreach ($questoes as $index => $questao)
                    <div class="card mb-3 questao" id="questao-{{ $index }}"
                        style="{{ $exame->navegacao === 'sequencial' && $index !== 0 ? 'display: none;' : '' }}">
                        <div class="card-header d-flex justify-content-between">
                            <strong>Questão {{ $index + 1 }}</strong>
                            @if ($questao->imagem)
                                <img src="{{ asset('storage/' . $questao->imagem) }}" style="max-height: 100px;">
                            @endif
                        </div>

                        @if ($questao->comentario_explicativo)
                            <div class="alert alert-secondary mt-3">
                                <strong>Comentário Explicativo:</strong><br>
                                {!! $questao->comentario_explicativo !!}
                            </div>
                        @endif


                        <div class="card-body">
                            <p>{!! $questao->texto !!}</p>
                            @foreach ($questao->alternativas->shuffle() as $alt)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="questao_{{ $questao->id }}"
                                        onclick="salvarResposta({{ $questao->id }}, {{ $alt->id }})">
                                    <label class="form-check-label">{{ $alt->texto }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <button class="btn btn-success mt-3" type="submit">Finalizar Prova</button>
            </form>

            <script>
                const fim = new Date("{{ $fim->format('Y-m-d H:i:s') }}").getTime();
                let tempoRestante = Math.floor((fim - new Date().getTime()) / 1000);
                const cronometroEl = document.getElementById('cronometro');

                const intervalo = setInterval(() => {
                    if (tempoRestante <= 0) {
                        clearInterval(intervalo);
                        alert("Tempo esgotado. A prova será finalizada.");
                        document.getElementById('provaForm').submit();
                    } else {
                        let h = Math.floor(tempoRestante / 3600);
                        let m = Math.floor((tempoRestante % 3600) / 60);
                        let s = tempoRestante % 60;
                        cronometroEl.innerText = `Tempo restante: ${h}h ${m}m ${s}s`;
                        tempoRestante--;
                    }
                }, 1000);

                function salvarResposta(questaoId, alternativaId) {
                    fetch("{{ route('estudante.salvar') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                questao_id: questaoId,
                                alternativa_id: alternativaId,
                                tentativa_id: '{{ $tentativa->id }}'
                            })
                        }).then(response => response.json())
                        .then(data => console.log("Resposta salva com sucesso"))
                        .catch(error => console.error('Erro ao salvar resposta:', error));
                }

                // ⛔ Sem o alerta indesejado do navegador
                // ✅ Com tolerância para troca de aba
                let saidasAba = 0;

                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        saidasAba++;

                        if (saidasAba === 1) {
                            alert("Você saiu da aba. Esta é a sua única chance. Retorne imediatamente.");
                            setTimeout(() => {
                                if (document.hidden) {
                                    alert("Você não retornou. A prova será finalizada.");
                                    document.getElementById('provaForm').submit();
                                }
                            }, 5000); // tempo de tolerância para retorno
                        } else if (saidasAba >= 2) {
                            alert("Você saiu da aba novamente. A prova será finalizada com nota 0.");
                            document.getElementById('provaForm').submit();
                        }
                    }
                });


                // Prevenir F5, Ctrl+R e Backspace
                document.addEventListener('keydown', function(e) {
                    if ((e.key === 'F5') || (e.ctrlKey && e.key === 'r')) {
                        e.preventDefault();
                        alert("Atualização da página bloqueada durante a prova.");
                    }

                    if (e.key === 'Backspace' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                        e.preventDefault();
                    }
                });

                // Impede voltar no navegador
                history.pushState(null, null, location.href);
                window.addEventListener('popstate', function() {
                    history.pushState(null, null, location.href);
                    alert("Não é permitido voltar a página durante a prova.");
                });

                // Removido beforeunload para evitar alerta indesejado de navegador
            </script>
        @endif
    </div>
@endsection
