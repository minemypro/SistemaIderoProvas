<?php

namespace App\Http\Controllers;

use App\Models\TentativaExame;
use App\Models\Resposta;
use Illuminate\Http\Request;

class CorrecaoController extends Controller
{
    public function listar()
    {
        $tentativas = TentativaExame::where('status', 'finalizado')
                                    ->whereNull('nota_final')
                                    ->with('user', 'exame')
                                    ->get();

        return view('professor.correcao.listar', compact('tentativas'));
    }

    public function corrigir($tentativaId)
    {
        $tentativa = TentativaExame::with('respostas.questao', 'respostas.alternativa')->findOrFail($tentativaId);
        return view('professor.correcao.corrigir', compact('tentativa'));
    }

    public function salvar(Request $request, $tentativaId)
    {
        $tentativa = TentativaExame::findOrFail($tentativaId);
        $notaFinal = 0;

        foreach ($request->input('notas') as $respostaId => $nota) {
            $resposta = Resposta::find($respostaId);
            $resposta->nota = $nota;
            $resposta->save();
            $notaFinal += $nota;
        }

        $tentativa->nota_final = $notaFinal;
        $tentativa->save();

        return redirect()->route('professor.correcao.listar')
                         ->with('success', 'Correção salva com sucesso.');
    }
}
