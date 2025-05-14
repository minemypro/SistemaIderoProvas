@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Provas Disponíveis</h2>
    <ul>
        @foreach ($provasDisponiveis as $p)
            <li>
                <strong>{{ $p['exame']->titulo }}</strong> ({{ $p['status'] }})
                @if($p['exame']->inicio <= now() && $p['exame']->fim >= now())
                    <form method="POST" action="{{ route('estudante.exames.iniciar', $p['exame']->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">Realizar Prova</button>
                    </form>
                @else
                    <span class="text-muted">Aguardando início</span>
                @endif
            </li>
        @endforeach
    </ul>

    <hr>

    <h2>Provas Realizadas</h2>
    <ul>
        @foreach ($provasRealizadas as $p)
            <li>
                <strong>{{ $p['exame']->titulo }}</strong> - Nota: {{ $p['exame']->nota_final ?? 'N/A' }}
            </li>
        @endforeach
    </ul>
</div>
@endsection
