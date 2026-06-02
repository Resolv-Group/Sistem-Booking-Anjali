<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionIsValid
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if the session contains an authentication identifier
        if (Auth::check()) {
            // 2. Try to refresh the user from the database.
            // If the database was freshly migrated, this returns null.
            if (Auth::user() === null) {

                // 3. Forcefully log out, clear session files, and regenerate tokens
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // 4. Redirect them to login with a clean slate
                return redirect()->route('auth.login')->with('error', 'Database di reset, silahkan login kembali');
            }
        }

        return $next($request);
    }
}
