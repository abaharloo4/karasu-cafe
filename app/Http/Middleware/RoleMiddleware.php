<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'شما دسترسی لازم برای انجام این عملیات را ندارید.',
                ], 403);
            }

            abort(403, 'شما دسترسی لازم برای مشاهده این صفحه را ندارید.');
        }

        return $next($request);
    }
}
