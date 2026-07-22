<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientPFCheker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $completed = session()->get('is_completed');

        if ($completed == 0 || $completed == null) {
            // dd('here');
            return redirect()->route('client.dashboard')->with('error', 'Please complete your profile');
        }

        return $next($request);
    }
}
