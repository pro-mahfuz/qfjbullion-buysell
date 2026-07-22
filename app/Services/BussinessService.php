<?php

namespace App\Services;


use App\Models\Bussiness;
use App\Models\UserBusinessMap;
use Request;

class BussinessService
{
    public static function getAllBussiness()
    {
        $isSuperAdmin = auth()->user()->hasRole('Super Admin');

        $result = Bussiness::where('is_active', 1);

        if (!$isSuperAdmin) {
            $businessMap = UserBusinessMap::where('user_id', auth()->user()->id)->first();

            if ($businessMap) {
                $result = $result->where('id', $businessMap->bussiness_id);
            }
        }

        return $result->get();

    }
    public function getBussiness($id)
    {
        $result = Bussiness::find($id);
        return $result;
    }

    public function updateBussiness(array $data, $id)
    {
        Bussiness::find($id)->update($data);
    }

    public function createBussiness(array $data)
    {
        return Bussiness::create($data);
    }

    public function deleteBussiness($id)
    {
        Bussiness::find($id)->update(['is_active' => 0]);
    }

    public function getBussinessById(int $id): Bussiness
    {
        $bussiness = Bussiness::find($id);
        return $bussiness;
    }

    public function getUserMap()
    {
        return UserBusinessMap::join('bussiness', 'bussiness.id', '=', 'user_has_business.bussiness_id')
            ->join('users', 'users.id', '=', 'user_has_business.user_id')
            ->select('bussiness.name as bussiness_name', 'users.full_name as user_name', 'user_has_business.id')
            ->get();

    }

    public function storeMap(array $data)
    {
        $userMap = new UserBusinessMap();
        $userMap->user_id = $data['user_id'];
        $userMap->bussiness_id = $data['bussiness_id'];
        $userMap->save();
    }

    public function deleteMap($id)
    {
        UserBusinessMap::find($id)->delete();
    }


}
