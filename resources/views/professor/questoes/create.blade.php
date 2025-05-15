@extends('layouts.app')

@section('content')




</div><div class="container mt-3">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-plus-circle"></i> Cadastrar Nova Questão</h6>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-sm p-2 mb-2">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @php
                $pontuacaoAtual = $exameSelecionado ? $exameSelecionado->questoes->sum('pontuacao') : 0;
            @endphp

            <div id="alerta-pontuacao" class="alert alert-info alert-sm p-2 mb-2" style="{{ $exameSelecionado ? '' : 'display: none;' }}">
                <small>
                    <i class="fas fa-bullseye"></i> Total: <strong>{{ $exameSelecionado->pontuacao_total ?? '' }} pts</strong><br>
                    <i class="fas fa-clipboard-check"></i> Cadastrada: <strong>{{ $pontuacaoAtual }} pts</strong>
                </small>
            </div>

            @if ($exameSelecionado && $pontuacaoAtual >= $exameSelecionado->pontuacao_total)
                <div class="alert alert-danger alert-sm p-2 mb-2">
                    <i class="fas fa-exclamation-triangle"></i> Pontuação máxima atingida.
                </div>
            @endif

            <form action="{{ route('professor.questoes.store', ['exame' => $exameSelecionado->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <!-- Coluna 1 -->
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="small"><i class="fas fa-file-alt"></i> Exame</label>
                            <select name="exame_id" class="form-control form-control-sm" required>
                                <option value="">Selecione um exame</option>
                                @foreach ($exames as $exame)
                                    <option value="{{ $exame->id }}" {{ $exame->id == $exameSelecionado->id ? 'selected' : '' }}
                                        data-quantidade="{{ $exame->tentativas_permitidas }}"
                                        data-pontuacao-total="{{ $exame->pontuacao_total }}"
                                        data-pontuacao-atual="{{ $exame->questoes->sum('pontuacao') }}">
                                        {{ $exame->titulo }} - {{ $exame->disciplina }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label class="small"><i class="fas fa-question-circle"></i> Texto da Questão</label>
                            <textarea name="texto" class="form-control form-control-sm" rows="2" required>{{ old('texto') }}</textarea>
                        </div>

                        <div class="form-group mb-2">
                            <label class="small"><i class="fas fa-image"></i> Imagem (opcional)</label>
                            <input type="file" name="imagem" class="form-control form-control-sm">
                        </div>

                        <div class="form-group mb-2">
                            <label class="small"><i class="fas fa-star"></i> Pontuação</label>
                            <input type="number" name="pontuacao" class="form-control form-control-sm" required min="1" value="{{ old('pontuacao') }}">
                        </div>
                    </div>

                    <!-- Coluna 2 -->
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="small"><i class="fas fa-comment-dots"></i> Comentário Explicativo</label>
                            <textarea name="comentario_explicativo" class="form-control form-control-sm" rows="2">{{ old('comentario_explicativo') }}</textarea>
                        </div>

                        <div class="form-group mb-2" id="alternativas-container">
                            <label class="small"><i class="fas fa-list-ul"></i> Alternativas</label>
                            @for ($i = 0; $i < $quantidadeAlternativas; $i++)
                                <input type="text" name="alternativas[]" class="form-control form-control-sm mb-1"
                                    placeholder="Alternativa {{ chr(65 + $i) }}" value="{{ old('alternativas.' . $i) }}" required>
                            @endfor
                        </div>

                        <div class="form-group mb-2">
                            <label class="small"><i class="fas fa-check"></i> Alternativa Correta</label>
                            <input type="number" name="correta" class="form-control form-control-sm" required min="0"
                                max="{{ $quantidadeAlternativas - 1 }}" value="{{ old('correta') }}">
                            <small class="form-text text-muted">0 para A, 1 para B, até {{ $quantidadeAlternativas - 1 }}</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


    <script>
        // Função para atualizar o número de campos de alternativas
        document.getElementById('exame_id').addEventListener('change', function () {
            let selectedOption = this.options[this.selectedIndex];
            let quantidadeAlternativas = selectedOption.getAttribute('data-quantidade');
            let container = document.getElementById('alternativas-container');

            // Limpar campos de alternativas existentes
            container.innerHTML = '<label>Alternativas</label>';

            // Criar novos campos de alternativas
            for (let i = 0; i < quantidadeAlternativas; i++) {
                let input = document.createElement('input');
                input.type = 'text';
                input.name = 'alternativas[]';
                input.className = 'form-control mb-2';
                input.placeholder = 'Alternativa ' + String.fromCharCode(65 + i);
                container.appendChild(input);
            }

            // Atualizar o campo de alternativa correta com o novo limite
            let maxCorreta = quantidadeAlternativas - 1;
            document.querySelector('input[name="correta"]').setAttribute('max', maxCorreta);

            // Atualizar a pontuação total e cadastrada dinamicamente
            let pontuacaoTotal = selectedOption.getAttribute('data-pontuacao-total');
            let pontuacaoAtual = selectedOption.getAttribute('data-pontuacao-atual');

            if (pontuacaoTotal && pontuacaoAtual) {
                document.getElementById('alerta-pontuacao').style.display = 'block';
                document.getElementById('pontuacao-total').innerText = `Pontuação Total Permitida: ${pontuacaoTotal} pts`;
                document.getElementById('pontuacao-atual').innerText = `Pontuação Já Cadastrada: ${pontuacaoAtual} pts`;
            } else {
                document.getElementById('alerta-pontuacao').style.display = 'none';
            }
        });


    </script>
@endsection
