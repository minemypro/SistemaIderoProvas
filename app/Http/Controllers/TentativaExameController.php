<?php

namespace App\Http\Controllers;

use App\Models\Exame;
use App\Models\TentativaExame;
use App\Models\Resposta;
use App\Models\Questao;
use App\Models\Alternativa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TentativaExameController extends Controller
{
    public function iniciar($exameId)
    {
        $exame = Exame::findOrFail($exameId);
        $user = Auth::user();

        $tentativasRealizadas = TentativaExame::where('user_id', $user->id)
                                              ->where('exame_id', $exameId)
                                              ->count();

        if ($tentativasRealizadas >= $exame->tentativas_permitidas) {
            return redirect()->back()->with('error', 'Número máximo de tentativas atingido.');
        }

        $tentativa = TentativaExame::create([
            'user_id' => $user->id,
            'exame_id' => $exameId,
            'tentativa_numero' => $tentativasRealizadas + 1,
            'inicio' => now(),
            'status' => 'em_andamento',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('estudante.exames.realizar', $tentativa->id);
    }

    public function realizar($tentativaId)
    {
        $tentativa = TentativaExame::with('exame.questoes.alternativas')->findOrFail($tentativaId);
        return view('estudante.exames.realizar', compact('tentativa'));
    }

    public function submeter(Request $request, $tentativaId)
    {
        $tentativa = TentativaExame::with('exame.questoes.alternativas')->findOrFail($tentativaId);

        DB::beginTransaction();

        $notaFinal = 0;

        foreach ($tentativa->exame->questoes as $questao) {
            $resposta = new Resposta();
            $resposta->tentativa_exame_id = $tentativa->id;
            $resposta->questao_id = $questao->id;

            $alternativaId = $request->input('questao_' . $questao->id);
            $resposta->alternativa_id = $alternativaId;
            $resposta->save();

            $alternativa = Alternativa::find($alternativaId);
            if ($alternativa && $alternativa->correta) {
                $notaFinal += $questao->pontuacao;
            }
        }

        $tentativa->nota_final = $notaFinal;
        $tentativa->fim = now();
        $tentativa->status = 'finalizado';
        $tentativa->save();

        DB::commit();

        return redirect()->route('estudante.exames.resultado', $tentativa->id)
                         ->with('success', 'Prova submetida com sucesso.');
    }

    public function resultado($tentativaId)
    {
        $tentativa = TentativaExame::with('respostas.questao', 'respostas.alternativa')->findOrFail($tentativaId);
        return view('estudante.exames.resultado', compact('tentativa'));
    }
}
