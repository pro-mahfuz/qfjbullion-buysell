<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserBusinessMap;
use Spatie\Permission\Models\Role;
use Session;
class UserService
{
    public static function getAllSchoolUsers($schoolId)
    {
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');

        if ($isSuperAdmin) {
            $result = User::with('roles')->get();
        } else {
            $result = User::with('roles')
                ->join('user_has_business', 'users.id', '=', 'user_has_business.user_id')
                ->where('user_has_business.bussiness_id', Session::get('businessId'))
                ->orderBy('users.id', 'desc')
                ->get();
        }

        return $result;

    }

    public function users()
    {
        $users = User::all();
        return $users->filter(function ($user) {
            return $user->hasRole('manager') || $user->hasRole('staff');
        });

    }

    public static function createUser(array $data)
    {

        $businessId = $data['business_id'];

        $user = new User();
        $user->full_name = $data['full_name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);

        $user->save();

        $role = Role::find($data['role_id']);
        $user->assignRole($role->name);
        $userMap = new UserBusinessMap();
        $userMap->user_id = $user->id;
        $userMap->bussiness_id = $businessId;
        $userMap->save();
    }

    public static function findUser($id)
    {
        return User::find($id);
    }

    public static function updateUser(array $data, $id)
    {
        $password = $data['password'];
        if ($password) {
            $data['password'] = bcrypt($password);
        } else {
            unset($data['password']);
        }

        $user = User::find($id);
        if ($user) {
            $businessId = $data['business_id'];
            unset($data['business_id']);
            $user->update($data);
            $role = Role::find($data['role_id']);
            $user->syncRoles($role->name);
            UserBusinessMap::where('user_id', $user->id)->update(['bussiness_id' => $businessId]);
        }
    }

    public static function deleteUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            if ($user->roles->first()) {
                $user->removeRole($user->roles->first()->name);
                UserBusinessMap::where('user_id', $user->id)->delete();
            }

        }
    }
}
