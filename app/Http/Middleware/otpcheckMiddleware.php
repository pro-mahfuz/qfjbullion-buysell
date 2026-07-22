<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class otpcheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return $next($request);
        }
        
        return $next($request);
        
        // if (Auth::check()) {
        //     $user = Auth::user();
            
        //     // If 2FA is enabled but not verified this session
        //     if ($user->google2fa_secret == null) {
        //         return redirect()->route('enable2FA');
                
        //     }else if($user->google2fa_secret != null){
        //         if($user->allowed_devicess != null){
        //             $user->allowed_devices = $request->ip();
        //             $user->save();
        //         }
                
        //         if($user->allowed_devices == $request->ip()){
        //             //dd($request->ip());
        //             return redirect()->route('verify2FAform');
        //         }
                
        //     }else{
        //         return $next($request);
        //     }
        // }
        
        // return $next($request);

      
        // if (Auth::check()) {
        //     $user = Auth::user();
        //     //dd($user);
        //     // If 2FA is enabled but not verified this session
        //     if ($user->google2fa_secret == null) {
        //         return redirect()->route('enable2FA');
                
        //     }else if($user->google2fa_secret){
        //         return redirect()->route('verify2FAform');
        //     }else{
        //         return $next($request);
        //     }
        // }
        
        // return $next($request);
    }
}
