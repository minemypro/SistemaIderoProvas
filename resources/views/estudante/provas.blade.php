@extends('layouts.app')

@section('content')
<h2>📋 Provas Disponíveis</h2>
@foreach($disponiveis as $matricula)
    <div>
        <strong>{{ $matricula->exame->titulo }}</strong> -
        {{ \Carbon\Carbon::parse($matricula->exame->inicio)->format('d/m/Y H:i') }} <br>
        <a href="{{ route('estudante.iniciar', $matricula->exame->id) }}" class="btn btn-success">
            Realizar Prova
        </a>
    </div>
@endforeach

<h2>✅ Provas Realizadas</h2>
@foreach($realizadas as $matricula)
    <div>
        <strong>{{ $matricula->exame->titulo }}</strong> -
        Nota: {{ $matricula->nota_final }} -
        Finalizado em: {{ $matricula->updated_at->format('d/m/Y H:i') }}
    </div>
@endforeach
@endsection
