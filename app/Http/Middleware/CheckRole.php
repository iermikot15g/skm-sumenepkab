<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Redirect berdasarkan role
        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        } elseif ($user->isAdminOpd()) {
            return redirect()->route('admin-opd.dashboard');
        } elseif ($user->isPimpinanOpd()) {
            return redirect()->route('pimpinan.dashboard');
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}