<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BussinessService;
use App\Services\FileUploadService;
use App\Services\UserService;
use DB;
use Illuminate\Http\Request;

class BussinessController extends Controller
{
    public function __construct(private BussinessService $bussinessService)
    {
        $this->bussinessService = $bussinessService;
    }


    public function index()
    {
        $bussinesses = $this->bussinessService->getAllBussiness();
        return view('admin.bussiness.list', ['result' => $bussinesses]);
    }

    public function create()
    {
        return view('admin.bussiness.add');
    }


    public function map()
    {
        $result = $this->bussinessService->getUserMap();
        return view('admin.bussiness.map-list', compact('result'));
    }

    public function createMap()
    {
        $bussiness = $this->bussinessService->getAllBussiness();
        $users = (new UserService())->users();
        return view('admin.bussiness.map-create', ['bussiness' => $bussiness, 'users' => $users]);
    }

    public function deleteMap($id)
    {
        $this->bussinessService->deleteMap($id);
        return redirect()->back()->with('success', 'User mapped to bussiness deleted successfully');
    }

    public function storeMap(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'bussiness_id' => 'required',
        ]);

        $this->bussinessService->storeMap($request->all());
        return redirect()->route('admin.bussiness.map')->with('success', 'User mapped to bussiness successfully');
    }


    public function store(Request $request)
    {
        $request->validate(rules: $this->getRules());
        $postData = array_merge($request->except('_token'));

        $this->bussinessService->createBussiness($postData);

        // return redirect()->back()->with('success', 'Bussiness Created Successfully');
        return redirect()->route('admin.bussiness.list')->with('success', 'Bussiness Created Successfully');
    }

    public function edit($id)
    {
        $bussiness = $this->bussinessService->getBussinessById($id);
        return view('admin.bussiness.edit', ['bussiness' => $bussiness]);
    }

    public function update($id, Request $request)
    {
        $request->validate(rules: $this->getRules());
        $postData = array_merge($request->except('_token'));

        $attachemtUrl = FileUploadService::handleFileUpload($request, 'image', 'business/');
        unset($postData['image']);

        $postData['image'] = $attachemtUrl;
        $this->bussinessService->updateBussiness($postData, $id);

        return redirect()->back()->with('success', 'Bussiness Updated Successfully');
    }

    public function delete($id)
    {
        $this->bussinessService->deleteBussiness($id);

        return redirect()->back()->with('success', 'Bussiness Deleted Successfully');
    }

    private function getRules()
    {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'address' => 'required|string',
        ];
    }

    public function privacyPolicy()
    {
        $business = DB::table('bussiness')->where('id', 1)->first();
        // return $business->privacy;
        return $this->sendResponse($business->privacy, 'Privacy Policy');
    }

    public function termsAndConditions()
    {
        $business = DB::table('bussiness')->where('id', 1)->first();
        // return $business->terms;
        return $this->sendResponse($business->terms, 'Terms and Conditions');
    }

    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

}
