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
            <div class="alert alert-warning">
                A prova ainda não começou. Aguarde...
            </div>
            <div id="contador_inicio" class="alert alert-info text-center fw-bold fs-4"></div>

            <script>
                const inicio = new Date("{{ $inicio->format('Y-m-d H:i:s') }}").getTime();
                console.log("Data de início programada:", new Date("{{ $inicio->format('Y-m-d H:i:s') }}"));

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
        @elseif($estado === 'encerrado')
            <div class="alert alert-danger">
                Este exame já foi encerrado. O prazo de realização expirou.
            </div>
        @else
            @php
                $tentativa = \App\Models\TentativaExame::firstOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'exame_id' => $exame->id,
                        'status' => 'em andamento',
                    ],
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

                const verificarStatusIntervalo = setInterval(() => {
                    fetch("{{ route('estudante.verificarExpiracao') }}")
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Erro ao verificar expiração");
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log("Verificação de expiração realizada.");
                        })
                        .catch(error => {
                            console.error("Erro na verificação periódica:", error);
                        });
                }, 60 * 1000); // Executa a cada 1 minuto (60000 ms)

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
                        })
                        .then(response => response.json())
                        .then(data => console.log("Resposta salva com sucesso"))
                        .catch(error => console.error('Erro ao salvar resposta:', error));
                }
            </script>
        @endif





        <script>
            let trocouAba = false;
            let alertaExibido = false;

            // Detectar troca de aba (visibilidade da página)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    if (!alertaExibido) {
                        alertaExibido = true;
                        alert("Você saiu da aba. Retorne imediatamente ou a prova será finalizada.");
                    } else {
                        // Finalizar prova e bloquear
                        alert("Você trocou de aba novamente. A prova será finalizada.");
                        document.getElementById('provaForm').submit();
                    }
                }
            });

            // Impedir recarregamento (F5 e Ctrl+R)
            document.addEventListener('keydown', function(e) {
                if ((e.key === 'F5') || (e.ctrlKey && e.key === 'r')) {
                    e.preventDefault();
                    alert("Recarregar a página não é permitido durante a prova.");
                }

                // Impede Backspace fora de campos de input/textarea
                if (e.key === 'Backspace' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                    e.preventDefault();
                }
            });

            // Impedir botão voltar do navegador
            history.pushState(null, null, location.href);
            window.addEventListener('popstate', function() {
                history.pushState(null, null, location.href);
                alert("Voltar a página não é permitido durante a prova.");
            });

            // Aviso ao tentar fechar/atualizar a aba
            window.addEventListener("beforeunload", function(e) {
                e.preventDefault();
                e.returnValue = "Tem certeza que deseja sair? A prova será finalizada.";
            });
        </script>
        <script>
            let tempoTotal = {{ $exame->duracao * 60 }}; // em segundos
            let tempoRestante = tempoTotal;

            let cronometro = setInterval(() => {
                tempoRestante--;
                let min = Math.floor(tempoRestante / 60);
                let seg = tempoRestante % 60;
                document.getElementById("cronometro").innerText = `Tempo restante: ${min}m ${seg}s`;

                if (tempoRestante <= 0) {
                    clearInterval(cronometro);
                    finalizarProvaAutomaticamente('Tempo esgotado.');
                }
            }, 1000);

            // Detectar saída da aba
            let saiuDaAba = false;
            document.addEventListener("visibilitychange", function() {
                if (document.hidden) {
                    saiuDaAba = true;
                    alert("Você saiu da aba. Retorne imediatamente ou a prova será finalizada.");
                    setTimeout(() => {
                        if (document.hidden) {
                            finalizarProvaAutomaticamente('Saiu da aba.');
                        }
                    }, 5000); // tempo de tolerância
                }
            });

            // Finalização automática
            function finalizarProvaAutomaticamente(motivo) {
                alert(motivo + " A prova será finalizada.");
                fetch("{{ route('estudante.finalizar', $exame->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        tentativa_id: {{ $tentativa->id }},
                        motivo: motivo
                    })
                }).then(() => {
                    window.location.href = "{{ route('estudante.dashboard') }}";
                });
            }
        </script>

    </div>
@endsection
