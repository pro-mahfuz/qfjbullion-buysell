<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BussinessService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        session()->flash('success', 'Welcome to the admin dashboard!');
        return view('admin.dashboard')->with('success', 'Welcome to the admin dashboard!');
    }


    public function changeBussiness()
    {

        $active_businesses = BussinessService::getAllBussiness();
        $bussinessId = \Request::session()->get('bussinessId');
        
        //dd($bussinessId);
        if(auth()->user()->is_superadmin == 0){
            return redirect()->route('admin.dashboard.buysell');
        }

        return view('admin.dashboard', ['active_businesses' => $active_businesses, 'bussinessId' => $bussinessId]);
    }

    public function changeBusiness(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id' => 'required|integer'
        ]);

        $request->session()->put('bussinessId', $request->id);
        $request->session()->put('bussinessName', $request->name);
        return redirect()->route('admin.dashboard');
    }
}
