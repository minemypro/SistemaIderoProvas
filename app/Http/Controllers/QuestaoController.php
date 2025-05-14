<?php

namespace App\Http\Controllers;

use App\Models\Exame;
use App\Models\Questao;
use App\Models\Alternativa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class QuestaoController extends Controller
{
    public function listarTodos(Request $request)
    {
        $user = auth()->user();

        $query = Exame::with('questoes.alternativas')
            ->where('user_id', $user->id) // <-- filtro por professor
            ->orderByDesc('inicio');

        if ($request->filled('titulo')) {
            $query->where('titulo', 'like', '%' . $request->titulo . '%');
        }

        if ($request->filled('disciplina')) {
            $query->where('disciplina', 'like', '%' . $request->disciplina . '%');
        }

        $exames = $query->paginate(5)->appends($request->query());

        return view('professor.questoes.todos', compact('exames'));
    }



    public function create(Exame $exame)
    {
        $user = auth()->user();

        // Apenas exames do usuário
        $exames = Exame::where('user_id', $user->id)->get();
        $exameSelecionado = $exame;

        // Verifica se o exame pertence ao usuário
        if ($exame && $exame->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para acessar este exame.');
        }

        $quantidadeAlternativas = $exameSelecionado ? $exameSelecionado->tentativas_permitidas : 4;

        return view('professor.questoes.create', compact('exames', 'exameSelecionado', 'quantidadeAlternativas'));
    }

    public function store(Request $request, Exame $exame)
    {
        $request->validate([
            'texto' => 'required|string',
            'pontuacao' => 'required|integer|min:1',
            'alternativas' => 'required|array',
            'correta' => 'required|integer',
            'exame_id' => 'required|exists:exames,id'
        ]);

        $exame = Exame::findOrFail($request->exame_id);

        // Verificação de permissão
        if ($exame->user_id !== auth()->id()) {
            abort(403, 'Você não tem permissão para adicionar questões neste exame.');
        }

        // Valida pontuação total
        $pontuacaoAtual = $exame->questoes->sum('pontuacao');
        $novaPontuacao = $pontuacaoAtual + $request->pontuacao;

        if ($novaPontuacao > $exame->pontuacao_total) {
            return redirect()->back()->withErrors(['pontuacao' => 'A pontuação total do exame não pode ser ultrapassada.']);
        }

        $questao = new Questao();
        $questao->exame_id = $exame->id;
        $questao->texto = $request->texto;
        $questao->pontuacao = $request->pontuacao;
        $questao->comentario_explicativo = $request->comentario_explicativo;

        if ($request->hasFile('imagem')) {
            $path = $request->file('imagem')->store('imagens/questoes', 'public');
            $questao->imagem = $path;
        }

        $questao->save();

        foreach ($request->alternativas as $key => $alternativa) {
            if (!empty($alternativa)) {
                $questao->alternativas()->create([
                    'texto' => $alternativa,
                    'correta' => $key == $request->correta
                ]);
            }
        }

        return redirect()->route('professor.exames_questoes.index', $exame->id)
            ->with('success', 'Questão cadastrada com sucesso!');
    }






    public function edit($exame_id, $id)
    {
        $user = auth()->user();
        $exame = Exame::findOrFail($exame_id);

        if ($exame->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para editar questões deste exame.');
        }

        $questao = Questao::with('alternativas')->findOrFail($id);

        return view('professor.questoes.edit', compact('questao', 'exame'));
    }


public function update(Request $request, $exame_id, $id)
{
    $request->validate([
        'texto' => 'required|string',
        'imagem' => 'nullable|image|max:2048',
        'comentario_explicativo' => 'nullable|string',
        'pontuacao' => 'required|numeric|min:1',
        'alternativas' => 'required|array|min:2',
        'correta' => 'required|integer'
    ]);

    $exame = Exame::findOrFail($exame_id);

    if ($exame->user_id !== auth()->id()) {
        abort(403, 'Você não tem permissão para editar questões deste exame.');
    }

    DB::transaction(function () use ($request, $exame, $id) {
        $questao = Questao::with('alternativas')->findOrFail($id);

        // Validação da pontuação total
        $pontuacaoAtual = $exame->questoes->where('id', '!=', $questao->id)->sum('pontuacao');
        $novaPontuacao = $pontuacaoAtual + $request->pontuacao;
        if ($novaPontuacao > $exame->pontuacao_total) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pontuacao' => 'A pontuação total do exame não pode ser ultrapassada.'
            ]);
        }

        if ($request->hasFile('imagem')) {
            if ($questao->imagem) {
                Storage::disk('public')->delete($questao->imagem);
            }
            $questao->imagem = $request->file('imagem')->store('questoes', 'public');
        }

        $questao->update([
            'texto' => $request->texto,
            'comentario_explicativo' => $request->comentario_explicativo,
            'pontuacao' => $request->pontuacao
        ]);

        $questao->alternativas()->delete();
        foreach ($request->alternativas as $index => $texto) {
            $questao->alternativas()->create([
                'texto' => $texto,
                'correta' => ($index == $request->correta)
            ]);
        }
    });

    return redirect()->route('professor.exames_questoes.index', ['exame' => $exame->id])
        ->with('success', 'Questão atualizada com sucesso!');
}

  public function destroy($exame_id, $id)
{
    $exame = Exame::findOrFail($exame_id);

    if ($exame->user_id !== auth()->id()) {
        abort(403, 'Você não tem permissão para excluir questões deste exame.');
    }

    $questao = Questao::findOrFail($id);

    DB::transaction(function () use ($questao) {
        if ($questao->imagem) {
            Storage::disk('public')->delete($questao->imagem);
        }
        $questao->alternativas()->delete();
        $questao->delete();
    });

    return redirect()->route('professor.exames_questoes.index', ['exame' => $exame_id])
        ->with('success', 'Questão removida com sucesso!');
}

}
