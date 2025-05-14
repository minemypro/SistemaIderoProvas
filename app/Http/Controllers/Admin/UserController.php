<?php

namespace App\Http\Controllers\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;


class UserController extends Controller
{

    public function index()
    {
        $usuarios = User::all();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'numero_matricula' => $request->numero_matricula,
            'bilhete' => $request->bilhete,
            'contacto' => $request->contacto,
            'data_nascimento' => $request->data_nascimento,
            'endereco' => $request->endereco,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $usuario->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'numero_matricula' => $request->numero_matricula,
            'bilhete' => $request->bilhete,
            'contacto' => $request->contacto,
            'data_nascimento' => $request->data_nascimento,
            'endereco' => $request->endereco,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuário excluído com sucesso!');
    }

    public function show(string $id)
    {
        //
    }

    // Exibir formulário para alterar a senha
public function editPassword($id)
{
    $usuario = User::findOrFail($id);
    return view('admin.usuarios.edit-password', compact('usuario'));
}

// Atualizar a senha no banco de dados
public function updatePassword(Request $request, $id)
{
    $request->validate([
        'password' => 'required|confirmed|min:6',
    ]);

    $usuario = User::findOrFail($id);
    $usuario->password = Hash::make($request->password);
    $usuario->save();

    return redirect()->route('usuarios.index')->with('success', 'Senha atualizada com sucesso!');
}



    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */

}
