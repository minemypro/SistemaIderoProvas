@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">{{ $titulo }}</h3>

    @switch($tipo)
        @case('frequencia')
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Estudante</th>
                        <th>Exame</th>
                        <th>Status</th>
                        <th>Data Matrícula</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dados as $matricula)
                        <tr>
                            <td>{{ $matricula->estudante->name }}</td>
                            <td>{{ $matricula->exame->titulo }}</td>
                            <td>{{ $matricula->status ?? 'Aguardando' }}</td>
                            <td>{{ $matricula->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @break

        @case('notas')
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Estudante</th>
                        <th>Disciplina</th>
                        <th>Exame</th>
                        <th>Nota</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dados as $tentativa)
                        <tr>
                            <td>{{ $tentativa->user->name }}</td>
                            <td>{{ $tentativa->exame->disciplina->nome ?? '-' }}</td>
                            <td>{{ $tentativa->exame->titulo }}</td>
                            <td>{{ $tentativa->nota_final }}</td>
                            <td>{{ $tentativa->inicio->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @break

        @case('questoes')
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Questão</th>
                        <th>Quantidade de Erros</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dados as $questaoId => $respostas)
                        <tr>
                            <td>{{ $respostas->first()->questao->texto ?? '---' }}</td>
                            <td>{{ $respostas->count() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @break

        @case('expiradas')
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Estudante</th>
                        <th>Exame</th>
                        <th>Início</th>
                        <th>Expiração</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dados as $tentativa)
                        <tr>
                            <td>{{ $tentativa->user->name }}</td>
                            <td>{{ $tentativa->exame->titulo }}</td>
                            <td>{{ $tentativa->inicio->format('d/m/Y H:i') }}</td>
                            <td>{{ $tentativa->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @break
    @endswitch
</div>
@endsection
