<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not authenticated. Please log in.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!isset($user->role)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The user does not have an assigned role.',
            ], Response::HTTP_FORBIDDEN);
        }

        if (!in_array($user->role, $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. You do not have permission to access this path.',
                'required_roles' => $roles,
                'user_role' => $user->role,
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
