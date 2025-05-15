<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exame;

class ExameController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $exames = Exame::orderByDesc('inicio')->paginate(10);
        } else {
            $exames = Exame::where('user_id', $user->id)->orderByDesc('inicio')->paginate(10);
        }

        return view('professor.exames.index', compact('exames'));
    }

    public function show($id)
    {
        $exame = Exame::with('questoes.alternativas')->findOrFail($id);
        $user = auth()->user();

        if ($user->role !== 'admin' && $exame->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para ver este exame.');
        }

        return view('professor.exames.show', compact('exame'));
    }

    public function create()
    {
        return view('professor.exames.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'disciplina' => 'required|string',
            'navegacao' => 'required|in:sequencial,livre',
            'tentativas_permitidas' => 'required|integer|min:1',
            'pontuacao_total' => 'nullable|numeric|min:0', // <-- Aqui
            'feedback' => 'required|in:imediato,pos-analise',
            'inicio' => 'required|date',
            'fim' => 'required|date|after:inicio',
        ]);



        $data = $request->all();
        $data['user_id'] = auth()->id(); // <- Aqui associamos o exame ao criador

        Exame::create($data);

        return redirect()->route('professor.exames.index')->with('success', 'Exame criado com sucesso.');
    }


    public function edit($id)
    {
        $exame = Exame::findOrFail($id);
        return view('professor.exames.edit', compact('exame'));
    }

    public function update(Request $request, $id)
    {
        $exame = Exame::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'disciplina' => 'required|string',
            'navegacao' => 'required|in:sequencial,livre',
            'tentativas_permitidas' => 'required|integer|min:1',
            'pontuacao_total' => 'required|numeric|min:1',
            'feedback' => 'required|in:imediato,pos-analise',
            'inicio' => 'required|date',
            'fim' => 'required|date|after:inicio',
        ]);

        $exame->update($request->all());

        return redirect()->route('professor.exames.index')->with('success', 'Exame atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $exame = Exame::findOrFail($id);
        $exame->delete();

        return redirect()->route('professor.exames.index')->with('success', 'Exame excluído com sucesso.');
    }
}
