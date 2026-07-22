<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserACLMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        //dd($permission);
        if (auth()->user()->can($permission) || auth()->user()->hasRole('Super Admin')) {
            return $next($request);
        }
        return redirect()->back()->with('error', 'You do not have permission, ask your admin!!');

    }
}
