<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogAtividade;

class LogOperacoes
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check()) {
            LogAtividade::create([
                'tentativa_exame_id' => null, // preencha se necessário
                'evento' => $request->method() . ' ' . $request->path(),
                'ip' => $request->ip(),
                'detalhes' => json_encode([
                    'input' => $request->except(['password', '_token']),
                    'status_code' => $response->status(),
                ]),
                'user_id' => Auth::id(),
            ]);
        }

        return $response;
    }
}
