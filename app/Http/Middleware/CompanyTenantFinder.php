<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyTenantFinder
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
        if (Auth::check()) {
            $employment = Auth::user()->employment;
            if (is_null($employment)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $employment->tenant->makeCurrent();
            return $next($request);
        }
        return response()->json(['error' => 'Unauthenticated'], 401);
    }
}
