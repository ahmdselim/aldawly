<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActiveUserCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        if ($user->active !== 1) {
            return response()->json(['error' => 'Account not verified yet.'], 401);
        }

        return $next($request);
    }
}
