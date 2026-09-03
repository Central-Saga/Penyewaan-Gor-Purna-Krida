<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Pastikan pengguna yang terautentikasi memiliki salah satu role.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  list<string>  $roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_if($user === null || ! $user->hasAnyRole($roles), 403);

        return $next($request);
    }
}
