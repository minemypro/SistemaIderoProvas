<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Exame;
use App\Models\MatriculaExame;
use Illuminate\Http\Request;

class MatriculaExameController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin vê todos, professor vê apenas os seus exames
        if ($user->role === 'admin') {
            $exames = Exame::where('ativo', true)->get();
            $alunos = User::where('role', 'estudante')->get();
        } else {
            $exames = Exame::where('user_id', $user->id)->where('ativo', true)->get();
            $alunos = User::where('role', 'estudante')->get(); // opcional: limitar alunos da sua turma se quiser
        }

        return view('professor.matriculas.index', compact('exames', 'alunos'));
    }

    public function showExame($exame_id)
    {
        $exame = Exame::findOrFail($exame_id);
        $user = auth()->user();

        // Restrição para não permitir que professores acessem exames de outros
        if ($user->role !== 'admin' && $exame->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para acessar este exame.');
        }

        $estudantes = User::where('role', 'estudante')->get();
        $matriculados = MatriculaExame::where('exame_id', $exame_id)->pluck('estudante_id')->toArray();

        return view('professor.matriculas.exame', compact('exame', 'estudantes', 'matriculados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exame_id' => 'required|exists:exames,id',
            'estudantes' => 'nullable|array',
        ]);

        $exame_id = $request->exame_id;
        $exame = Exame::findOrFail($exame_id);
        $user = auth()->user();

        // Restrição para garantir que apenas o criador/admin possa matricular
        if ($user->role !== 'admin' && $exame->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para modificar este exame.');
        }

        $matriculadosAtuais = MatriculaExame::where('exame_id', $exame_id)->pluck('estudante_id')->toArray();
        $novosMatriculados = $request->input('estudantes', []);

        $aRemover = array_diff($matriculadosAtuais, $novosMatriculados);
        $aAdicionar = array_diff($novosMatriculados, $matriculadosAtuais);

        // Remove estudantes desmarcados
        MatriculaExame::where('exame_id', $exame_id)
            ->whereIn('estudante_id', $aRemover)
            ->delete();

        // Adiciona novos estudantes
        foreach ($aAdicionar as $estudanteId) {
            MatriculaExame::create([
                'exame_id' => $exame_id,
                'estudante_id' => $estudanteId,
                'status' => 'aguardando',
            ]);
        }

        // Mensagem
        if (count($aAdicionar) > 0 && count($aRemover) > 0) {
            $mensagem = 'Matrículas atualizadas: estudantes adicionados e removidos.';
        } elseif (count($aAdicionar) > 0) {
            $mensagem = 'Estudantes matriculados com sucesso.';
        } elseif (count($aRemover) > 0) {
            $mensagem = 'Matrículas removidas com sucesso.';
        } else {
            $mensagem = 'Nenhuma alteração feita.';
        }

        return redirect()->back()->with('success', $mensagem);
    }

    // Verifica se um aluno está matriculado - usado via AJAX
    public function verificarMatricula(Request $request)
    {
        $matriculado = MatriculaExame::where('exame_id', $request->exame_id)
            ->where('estudante_id', $request->estudante_id)
            ->exists();

        return response()->json(['matriculado' => $matriculado]);
    }
}
