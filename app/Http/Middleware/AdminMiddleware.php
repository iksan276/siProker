<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->level !== 1) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
            }
            return redirect()->route('pilars.index')->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
        }

        return $next($request);
    }
}
