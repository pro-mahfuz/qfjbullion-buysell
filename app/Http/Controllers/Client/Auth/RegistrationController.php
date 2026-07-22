<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Client\BaseContrller;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use App\Traits\UserTrait;
use Auth;
use Illuminate\Http\Request;
use Validator;

class RegistrationController extends BaseContrller
{
    use UserTrait;

    public function showRegisterForm()
    {
        return view('client.auth.register');
    }




    public function register(Request $request)
    {
        if (!$request->link) {
            return redirect()->back()->with('error', 'No Link Found');
        }

        if ($request->password != $request->password_confirmation) {
            return redirect()->back()->with('error', 'Password does not match');
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'password_confirmation' => 'required|same:password',
                'link' => 'required',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $postData = array_merge($request->except('_token'));

        $postData['business_id'] = $request->link;

        unset($postData['businessID']);
        try {
            $data = $this->post('/register', [], $postData);

            if ($data->status() != 200) {
                dd($data->json());
                return redirect()->back()->with('error', $data->json()['message']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('client.login')->with('success', 'Registration successful');

    }


    public function termsandconditions()
    {
        return view('web.termsandconditions');
    }

    public function privacypolicy()
    {
        return view('web.privacypolicy');
    }

    public function accountdelation()
    {
        return view('web.accountdelation');
    }


}
