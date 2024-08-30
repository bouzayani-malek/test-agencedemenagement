<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CheckTokenExpiration
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
        $user = Auth::user();
        if ($user && $request->bearerToken()) {
            $token = $user->tokens()->where('id', $user->currentAccessToken()->id)->first();

            if ($token) {
                if ($token->expires_at && $token->expires_at->lt(Carbon::now())) {
                    return response()->json(['message' => 'Token expired'], 401);
                }
            } else {
                return response()->json(['message' => 'Invalid token'], 401);
            }
        }
        return $next($request);
    }
}
