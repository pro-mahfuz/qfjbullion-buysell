<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Http\Request;
use Session;
use DB;
use App\Http\Middleware\otpcheckMiddleware;

use Jenssegers\Agent\Agent;


class LoginController extends Controller
{
    public function enable2FA()
    {
        $user = auth()->user();
        
        $google2fa = app('pragmarx.google2fa');
        
        if($user->google2fa_secret == null){
            $secret = $google2fa->generateSecretKey(); // Generate a unique 2FA secret
            $user->google2fa_secret = $secret;
            $user->save();
            
            $QR_Image  = $google2fa->getQRCodeInline(
                config('app.name'),
                $user->email,
                $secret
            );
            //dd($QR_Image);
            
            //session(['2fa_secret' => $secret]);
    
            return view('client.auth.2fa-setup', compact('QR_Image', 'secret'));
        }else{
            $otp = $google2fa->getCurrentOtp($user->google2fa_secret);
            return view('client.auth.refreshOTP', compact('otp'));
        }
        
        
    }
    
    public function verify2FAform()
    {
        return view('client.auth.refreshOTP');
    }

    public function verify2FA(Request $request)
    {
        
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        
        $google2fa = app('pragmarx.google2fa');

        $user = auth()->user();
        
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->code);
        
        if ($valid) {
            return redirect()->route('admin.dashboard')->with('success', 'Login Successful');
        }else{
            return redirect()->route('login');
        }

        return back()->withErrors(['code' => 'Invalid authentication code.']);
    }

    public function showLoginForm()
    {
        if(auth()->check()){
            return redirect()->route('admin.dashboard');
        }
        
        $hostName = $_SERVER['HTTP_HOST'];
        $business = DB::table('bussiness')->where('domain', $hostName)->first();
        return view('vendor.adminlte.auth.login', compact('business'));
    }

    public function login(Request $request)
    {
        
        $credentials = $request->only('email', 'password');

        if (auth()->guard('web')->attempt($credentials)) {
            //check if user is active
            if (auth()->user()->is_active == 0) {
                auth()->guard('web')->logout();
                return back()->withErrors([
                    'email' => 'Your account is not active. Please contact admin.',
                ]);
            }
            if (auth()->user()->self == 1) {
                auth()->guard('web')->logout();
                return back()->withErrors([
                    'email' => 'You are not allowed to login here.',
                ]);
            }

            //check if user has business
            $user = auth()->user()->load('business');
            $business = $user->business->first();
            $image = $business->bussiness_id ? DB::table('bussiness')->where('id', $business->bussiness_id)->first()->logo : null;
            if ($business) {
                Session::put('bussinessId', $business->bussiness_id);
                Session::put('image', $image);
            }
            
            
            if ($user->google2fa_secret == null) {
                return redirect()->route('enable2FA');
                
            }
            if($user->google2fa_secret != null){
                if($user->allowed_devicess == null){
                    $user->allowed_devices = $request->ip();
                    $user->save();
                }
                
                if($user->allowed_devices == $request->ip()){
                    //dd($request->ip());
                    return redirect()->route('verify2FAform');
                }
            
                return redirect()->route('admin.dashboard')->with('success', 'Login Successful');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        $user = auth()->user();
        $user->logged_in = 0;
        $user->save();

        auth()->guard('web')->logout();

        Session::flush();

        return redirect()->route('login');
    }

    public function showRegisterForm()
    {
        return view('vendor.adminlte.auth.register');
    }

    public function showResetForm()
    {
        return view('vendor.adminlte.auth.passwords.email');
    }
    public function checkPassword(Request $request)
    {
        $user = auth()->user();

        if (Hash::check($request->password, $user->password)) {
            return response()->json(['success' => true, 'message' => 'Password is correct.']);
        }
        return response()->json(['success' => false, 'message' => 'Password is incorrect.']);

    }

}
