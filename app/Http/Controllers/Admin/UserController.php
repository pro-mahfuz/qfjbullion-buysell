<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Models\UserBusinessMap;
use App\Services\BussinessService;
use App\Services\RolePermissionService;
use App\Services\UserService;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use Auth;

class UserController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $usersQuery = User::query()
            ->where('is_active', 1)
            ->where('users.id', '!=', $user->id); // Explicitly specify "users.id"

        if (!$user->hasRole('Super Admin')) {
            $businessId = UserBusinessMap::where('user_id', $user->id)->value('bussiness_id');

            $usersQuery->join('user_has_business', 'users.id', '=', 'user_has_business.user_id')
                ->where('user_has_business.bussiness_id', $businessId);
        }

        $users = $usersQuery->select('users.*')->with('roles:id,name')->get(); // Ensure only 'users' columns are selected

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $data['roles'] = RolePermissionService::getAllRoles();

        $data['businesses'] = (new BussinessService)->getAllBussiness();
        return view('admin.users.add', $data);
    }


    public function store(UserRequest $request)
    {
        $validatedData = $request->validated();

        UserService::createUser($validatedData);

        return redirect()->back()->with('success', 'User Created Successfully');
    }

    public function edit(User $user)
    {
        $bussiness_id = UserBusinessMap::where('user_id', $user->id)->first();
        $data['user'] = $user;
        $data['roles'] = RolePermissionService::getAllRoles();
        $data['businesses'] = (new BussinessService)->getAllBussiness();
        $data['bussiness_id'] = $bussiness_id->bussiness_id;
        return view('admin.users.edit', $data);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        // dd($request->all());
        $validatedData = $request->validated();
        UserService::updateUser($request->all(), $user->id);

        return redirect()->back()->with('success', 'User Updated Successfully');
    }

    public function destroy(Request $request)
    {
        $user = User::find($request->id);
        // dd($user);
        $user->is_active = 0;
        $user->save();
        return redirect(route('admin.users.index'))->with('success', 'User Deleted Successfully');
    }

    public function changePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect.']);
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password updated successfully!']);
    }
}
