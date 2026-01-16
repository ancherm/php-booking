<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Необходимо войти в систему');
        }

        $user = auth()->user();

        if ($user->disabled) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Ваш аккаунт заблокирован');
        }

        if (!empty($roles) && !in_array($user->user_type, $roles)) {
            abort(403, 'У вас нет доступа к этой странице');
        }

        return $next($request);
    }
}
