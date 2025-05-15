<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Exame;
use App\Models\TentativaExame;
use App\Models\User;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function relatorio(Request $request)
    {
        $estudanteId = $request->input('estudante_id');
        $exameId = $request->input('exame_id');
        $perPage = $request->input('per_page', 5); // valor padrão: 10

        $estudantes = User::where('role', 'estudante')->get();
        $exames = Exame::orderBy('inicio', 'desc')->get();

        $query = TentativaExame::with(['exame', 'user'])->orderByDesc('inicio');

        if ($estudanteId) {
            $query->where('user_id', $estudanteId);
        }

        if ($exameId) {
            $query->where('exame_id', $exameId);
        }

        $tentativas = $query->paginate($perPage);

        return view('relatorios.index', compact('tentativas', 'estudantes', 'exames', 'estudanteId', 'exameId', 'perPage'));
    }

    public function exportarPDF(Request $request)
    {
        $estudanteId = $request->input('estudante_id');
        $exameId = $request->input('exame_id');

        $query = TentativaExame::with(['exame', 'user'])->orderByDesc('inicio');

        if ($estudanteId) {
            $query->where('user_id', $estudanteId);
        }

        if ($exameId) {
            $query->where('exame_id', $exameId);
        }

        $tentativas = $query->get();

        $pdf = Pdf::loadView('relatorios.pdf_geral', compact('tentativas'));
        return $pdf->stream('relatorio_resultados.pdf');
    }
}
