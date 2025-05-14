<?php

namespace App\Http\Controllers;

use App\Models\Exame;
use App\Models\Resposta;
use App\Models\MatriculaExame;
use App\Models\TentativaExame;
use App\Models\LogAtividade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EstudanteController extends Controller
{
    public function dashboard()
    {
        $estudanteId = Auth::id();
        $matriculas = MatriculaExame::with('exame')->where('estudante_id', $estudanteId)
            ->orderByDesc('created_at') // ou ->orderBy('exame.inicio') se fizer join
            ->get();

        return view('estudante.dashboard', compact('matriculas'));
    }

    public function iniciarProva($exame_id)
    {
        $exame = Exame::with('questoes.alternativas')->findOrFail($exame_id);
        $matricula = MatriculaExame::where('estudante_id', Auth::id())
            ->where('exame_id', $exame_id)
            ->first();

        if (!$matricula) {
            abort(403, 'Matrícula não encontrada.');
        }

        // Verifica se há tentativa existente
        $tentativa = TentativaExame::where('user_id', Auth::id())
            ->where('exame_id', $exame_id)
            ->latest()
            ->first();

        // Bloquear se tentativa estiver finalizada ou expirada
        if ($tentativa && in_array($tentativa->status, ['finalizado', 'expirado'])) {
            return redirect()->route('estudante.dashboard')->with('error', 'Esta prova já foi finalizada ou expirada.');
        }

        // Criar tentativa nova apenas se não existir
        if (!$tentativa) {
            $tentativa = TentativaExame::create([
                'user_id' => Auth::id(),
                'exame_id' => $exame_id,
                'tentativa_numero' => $matricula->tentativas_permitidas,
                'inicio' => Carbon::now(),
                'status' => 'em andamento',
                'ip' => request()->ip(),
                'user_agent' => request()->header('User-Agent')
            ]);
        }

        return view('estudante.realizar', compact('exame', 'tentativa'));
    }
    public function finalizarProva(Request $request, $exame_id)
    {
        $tentativa = TentativaExame::where('user_id', auth()->id())
            ->where('exame_id', $exame_id)
            ->where('status', 'em andamento')
            ->firstOrFail();

        $tentativa->finalizar();

        $nota = $this->calcularNotaFinal($tentativa);
        $tentativa->nota_final = $nota;
        $tentativa->save();

        // ✅ Atualiza o status da matrícula após finalizar
        $matricula = MatriculaExame::where('exame_id', $tentativa->exame_id)
            ->where('estudante_id', $tentativa->user_id)
            ->first();

        if ($matricula) {
            $matricula->status = 'finalizado'; // ✅ Corrigido com aspas
            $matricula->save();
        }

        LogAtividade::create([
            'user_id' => auth()->id(),
            'tentativa_exame_id' => $tentativa->id,
            'evento' => 'Finalizou a prova',
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('estudante.dashboard')->with('success', 'Prova finalizada!');
    }

    public function verificarExpiracao()
    {
        $tentativas = TentativaExame::where('status', 'em andamento')->get();

        foreach ($tentativas as $tentativa) {
            if ($tentativa->verificarExpiracao()) {
                // ✅ Se a tentativa foi marcada como expirada, atualiza a matrícula também
                $matricula = MatriculaExame::where('exame_id', $tentativa->exame_id)
                    ->where('estudante_id', $tentativa->user_id)
                    ->first();

                if ($matricula) {
                    $matricula->status = 'expirado';
                    $matricula->save();
                }

                // Também podemos mudar o status da tentativa
                $tentativa->status = 'expirado';
                $tentativa->save();
            }
        }

        return response()->json(['status' => 'ok']);
    }






    public function calcularNotaFinal(TentativaExame $tentativa)
    {
        $questoes = $tentativa->exame->questoes;
        $pontuacaoObtida = 0;

        foreach ($questoes as $questao) {
            $resposta = $tentativa->respostas()->where('questao_id', $questao->id)->first();
            if ($resposta && $resposta->alternativa->correta) {
                $pontuacaoObtida += $questao->pontuacao;
            }
        }

        // Atualiza a nota final também na matrícula
        $matricula = MatriculaExame::where('exame_id', $tentativa->exame_id)
            ->where('estudante_id', $tentativa->user_id)
            ->first();

        if ($matricula) {
            $matricula->nota_final = $pontuacaoObtida;
            $matricula->save();
        }

        return $pontuacaoObtida;
    }


    public function verificarStatusMatricula()
    {
        $estudanteId = Auth::id();
        $matriculas = MatriculaExame::with(['exame', 'tentativas']) // Carrega as tentativas também
            ->where('estudante_id', $estudanteId)
            ->orderByDesc('created_at')
            ->get();

        foreach ($matriculas as $matricula) {
            // Verifica a última tentativa do exame
            $ultimaTentativa = $matricula->tentativas->last();

            // Se houver uma tentativa, ajusta o status
            if ($ultimaTentativa) {
                // Aqui você pode customizar o status conforme a situação da tentativa
                if ($ultimaTentativa->status === 'em andamento') {
                    $matricula->status = 'em andamento';
                } elseif ($ultimaTentativa->status === 'finalizado') {
                    $matricula->status = 'finalizado';
                } elseif ($ultimaTentativa->status === 'expirado') {
                    $matricula->status = 'expirado';
                }
            } else {
                // Caso não haja tentativa, o status pode ser "aguardando"
                $matricula->status = 'aguardando';
            }
            $matricula->save(); // ✅ ESSENCIAL PARA PERSISTIR NO BANCO
        }

        return response()->json(['matriculas' => $matriculas]);
    }



    public function salvarResposta(Request $request)
    {
        $user = Auth::user();
        $tentativaId = $request->input('tentativa_id');

        $tentativa = TentativaExame::findOrFail($tentativaId);

        // Bloquear resposta se prova foi finalizada
        if (in_array($tentativa->status, ['finalizado', 'expirado'])) {
            return response()->json([
                'status' => 'bloqueado',
                'message' => 'Esta tentativa já foi finalizada ou expirada.'
            ], 403);
        }

        // Salvar ou atualizar resposta
        $resposta = \App\Models\Resposta::updateOrCreate(
            [
                'tentativa_exame_id' => $tentativaId,
                'questao_id' => $request->input('questao_id'),
            ],
            [
                'alternativa_id' => $request->input('alternativa_id')
            ]
        );

        $exame = $tentativa->exame;
        $feedback = null;
        if ($exame->feedback === 'imediato') {
            $alternativa = \App\Models\Alternativa::find($request->input('alternativa_id'));
            $correta = $alternativa ? $alternativa->correta : false;
            $feedback = $correta ? 'Correto' : 'Incorreto';
        }

        return response()->json([
            'status' => 'ok',
            'feedback' => $feedback,
        ]);
    }


    // Função para finalizar a prova

    // Método para calcular a nota final da prova


}
