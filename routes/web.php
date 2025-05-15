<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ExameController;
// aqui vao as analise das  questões
use App\Http\Controllers\QuestaoController;


use App\Http\Controllers\MatriculaExameController;
use App\Http\Controllers\EstudanteController;

use App\Http\Controllers\RelatorioController;

Route::get('/relatorios', [RelatorioController::class, 'relatorio'])->name('relatorio');
Route::get('/relatorios/pdf', [RelatorioController::class, 'exportarPDF'])->name('relatorio.pdf');





Route::get('status-matricula', [EstudanteController::class, 'verificarStatusMatricula'])->name('status.matricula');

Route::get('/estudante/status', [EstudanteController::class, 'verificarStatusMatricula'])->name('status.matricula');


Route::middleware(['auth', 'role:estudante'])->group(function () {
    Route::get('/area-estudante', [EstudanteController::class, 'dashboard'])->name('estudante.dashboard');
    Route::get('/area-estudante/prova/{exame_id}', [EstudanteController::class, 'iniciarProva'])->name('estudante.prova');
    Route::post('/area-estudante/salvar-resposta', [EstudanteController::class, 'salvarResposta'])->name('estudante.salvar');
    Route::post('/area-estudante/finalizar/{exame_id}', [EstudanteController::class, 'finalizarProva'])->name('estudante.finalizar');
    Route::post('/area-estudante/finalizar/{tentativa_id}', [EstudanteController::class, 'finalizarProva'])->name('estudante.finalizar');
    Route::get('/estudante/verificar-expiracao', [EstudanteController::class, 'verificarExpiracao'])->name('estudante.verificarExpiracao');
});



Route::prefix('professor/matriculas')->middleware(['auth', 'role:professor'])->group(function () {
    Route::get('/', [MatriculaExameController::class, 'index'])->name('professor.matriculas.index');
    Route::get('/{exame_id}', [MatriculaExameController::class, 'showExame'])->name('professor.matriculas.exame');
    Route::post('/store', [MatriculaExameController::class, 'store'])->name('professor.matriculas.store');
});
Route::post('/ajax/verificar-matricula', [MatriculaExameController::class, 'verificarMatricula'])->middleware('auth');


Route::prefix('professor/exames/{exame}/questoes')->middleware(['auth', 'role:professor'])->group(function () {
    Route::get('/', [QuestaoController::class, 'listarTodos'])->name('professor.exames_questoes.index');
    Route::get('/create', [QuestaoController::class, 'create'])->name('professor.questoes.create'); // Página de criação
    Route::post('/', [QuestaoController::class, 'store'])->name('professor.questoes.store'); // Rota de salvamento
    Route::get('/{questao}/edit', [QuestaoController::class, 'edit'])->name('professor.questoes.edit');
    Route::put('/{questao}', [QuestaoController::class, 'update'])->name('professor.questoes.update');
    Route::delete('/{questao}', [QuestaoController::class, 'destroy'])->name('professor.questoes.destroy');
});


Route::prefix('professor')->middleware(['auth', 'role:professor'])->group(function () {
    Route::resource('exames', ExameController::class)->names('professor.exames');
    Route::get('/professor/exames/{id}', [ExameController::class, 'show'])->name('professor.exames.show');
});


Route::get('/', function () {
    return view('welcome');
});



Route::middleware(['auth'])->group(function () {

    // Redirecionamento de dashboard com base no tipo de usuário
    Route::get('/dashboard', function () {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('usuarios.index');
            case 'professor':
                return view('professor.dashboard');
            case 'estudante':
                return redirect()->route('estudante.dashboard');
            case 'candidato':
                return view('candidato.dashboard');
            default:
                abort(403, 'Acesso não autorizado.');
        }
    })->name('dashboard');




    // Rotas do perfil do usuário logado
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Rotas para Administradores
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware('auth')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{id}/edit', [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{id}', [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{id}', [UserController::class, 'destroy'])->name('usuarios.destroy');


        Route::get('/usuarios/{id}/edit-password', [UserController::class, 'editPassword'])->name('usuarios.edit-password');
        Route::put('/usuarios/{id}/update-password', [UserController::class, 'updatePassword'])->name('usuarios.update-password');
    });
});

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação (Jetstream/Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
