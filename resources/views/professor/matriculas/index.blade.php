@extends('layouts.app')

@section('content')

<div class="container py-4"> <div class="d-flex justify-content-between align-items-center mb-4"> <h4 class="mb-0"> <i class="fas fa-file-alt text-primary me-2"></i>Selecionar Exame para Matrícula </h4> </div>

<div class="row">
    @forelse($exames as $exame)
        <div class="col-md-6 col-lg-4 mb-3">
            <a href="{{ route('professor.matriculas.exame', $exame->id) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 hover-shadow">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-clipboard-list fa-2x text-secondary me-3"></i>
                        <div>
                            <h6 class="card-title mb-0 text-dark">{{ $exame->titulo }}</h6>
                            <small class="text-muted">Clique para gerenciar matrículas</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> Nenhum exame disponível para matrícula.
            </div>
        </div>
    @endforelse
</div>
</div> @endsection
