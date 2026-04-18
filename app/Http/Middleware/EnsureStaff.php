<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Allows admins and staff through, blocks viewers (read-only users).
 */
class EnsureStaff
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        abort_unless(
            $user && method_exists($user, 'canManage') && $user->canManage(),
            403,
            'This action requires staff or admin privileges.'
        );
        return $next($request);
    }
}
